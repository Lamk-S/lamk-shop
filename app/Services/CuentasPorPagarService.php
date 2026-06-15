<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CuentaPorPagar;
use App\Models\PagoCompra;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CuentasPorPagarService
{
    public function __construct(
        protected TesoreriaService $tesoreriaService
    ) {
    }

    public function crearDesdeCompra(
        Compra $compra,
        array $pagos = [],
        ?string $fechaVencimiento = null,
        ?User $user = null
    ): CuentaPorPagar {
        $compra = Compra::query()
            ->with(['proveedor.persona.documento'])
            ->whereKey($compra->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($compra->estado_documento === 'ANULADA') {
            throw new RuntimeException('No se puede crear cuenta por pagar sobre una compra anulada.');
        }

        $pagos = collect($pagos)
            ->map(function (array $pago) {
                return [
                    'metodo_pago' => strtoupper(trim((string) ($pago['metodo_pago'] ?? ''))),
                    'monto' => (float) ($pago['monto'] ?? 0),
                    'referencia_operacion' => $pago['referencia_operacion'] ?? null,
                    'observacion' => $pago['observacion'] ?? null,
                ];
            })
            ->filter(fn (array $pago) => $pago['metodo_pago'] !== '' && $pago['monto'] > 0)
            ->values();

        if ($pagos->isEmpty()) {
            if (strtoupper((string) $compra->metodo_pago) === 'CREDITO') {
                $pagos = collect();
            } elseif (strtoupper((string) $compra->metodo_pago) === 'MIXTO') {
                throw new RuntimeException('Para una compra mixta debes enviar el detalle de pagos.');
            } else {
                $pagos = collect([
                    [
                        'metodo_pago' => strtoupper((string) $compra->metodo_pago),
                        'monto' => (float) $compra->total,
                        'referencia_operacion' => null,
                        'observacion' => 'Pago registrado junto con la compra.',
                    ],
                ]);
            }
        }

        $montoPagado = round((float) $pagos->sum('monto'), 2);
        $saldoPendiente = round(max(0, (float) $compra->total - $montoPagado), 2);

        $estado = 'PENDIENTE';
        if ($montoPagado > 0 && $saldoPendiente > 0) {
            $estado = 'PARCIAL';
        } elseif ($saldoPendiente <= 0) {
            $estado = 'PAGADA';
        }

        $cuenta = CuentaPorPagar::create([
            'compra_id' => $compra->id,
            'proveedor_id' => $compra->proveedor_id,
            'user_id' => $user?->id,
            'total' => (float) $compra->total,
            'monto_pagado' => $montoPagado,
            'saldo_pendiente' => $saldoPendiente,
            'fecha_emision' => $compra->fecha_emision->toDateString(),
            'fecha_vencimiento' => $fechaVencimiento ?: null,
            'fecha_cancelacion' => $saldoPendiente <= 0 ? now() : null,
            'estado' => $estado,
            'observacion' => 'Cuenta por pagar generada desde la compra #' . $compra->id,
        ]);

        foreach ($pagos as $pago) {
            $this->registrarPagoInterno($cuenta, $pago, $user);
        }

        return $this->sincronizarCuenta($cuenta);
    }

    public function registrarPago(
        CuentaPorPagar $cuenta,
        array $pago,
        ?User $user = null
    ): PagoCompra {
        $cuenta = CuentaPorPagar::query()
            ->with('compra')
            ->whereKey($cuenta->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($cuenta->estado === 'ANULADA') {
            throw new RuntimeException('No se puede registrar pago sobre una cuenta anulada.');
        }

        $metodoPago = strtoupper(trim((string) ($pago['metodo_pago'] ?? '')));
        $monto = round((float) ($pago['monto'] ?? 0), 2);

        if ($metodoPago === '' || $monto <= 0) {
            throw new RuntimeException('El método de pago y el monto son obligatorios.');
        }

        $saldoDisponible = round((float) $cuenta->saldo_pendiente, 2);
        if ($monto > $saldoDisponible) {
            throw new RuntimeException('El monto del pago supera el saldo pendiente.');
        }

        $registro = $this->registrarPagoInterno($cuenta, [
            'metodo_pago' => $metodoPago,
            'monto' => $monto,
            'referencia_operacion' => $pago['referencia_operacion'] ?? null,
            'observacion' => $pago['observacion'] ?? null,
        ], $user);

        $this->sincronizarCuenta($cuenta->fresh(['pagos']));

        return $registro;
    }

    public function anularPorCompra(Compra $compra, ?User $user = null): void
    {
        $compra = Compra::query()
            ->with(['cuentaPorPagar.pagos', 'pagosCompra'])
            ->whereKey($compra->id)
            ->lockForUpdate()
            ->firstOrFail();

        $cuenta = $compra->cuentaPorPagar;

        if ($cuenta) {
            $cuenta = CuentaPorPagar::query()
                ->with('pagos')
                ->whereKey($cuenta->id)
                ->lockForUpdate()
                ->firstOrFail();

            foreach ($cuenta->pagos as $pago) {
                if (! $pago->estado) {
                    continue;
                }

                $medio = strtoupper($pago->metodo_pago) === 'EFECTIVO' ? 'EFECTIVO' : 'BANCO';

                $this->tesoreriaService->registrarAnulacion(
                    $medio,
                    (float) $pago->monto,
                    'ANULACION',
                    'Anulación de pago de compra #' . $compra->id,
                    $user?->id,
                    null,
                    null,
                    $compra->id,
                    $pago->referencia_operacion
                );

                $pago->update([
                    'estado' => 0,
                ]);
            }

            $cuenta->update([
                'estado' => 'ANULADA',
                'monto_pagado' => 0,
                'saldo_pendiente' => 0,
                'fecha_cancelacion' => now(),
            ]);
        }
    }

    protected function registrarPagoInterno(
        CuentaPorPagar $cuenta,
        array $pago,
        ?User $user = null
    ): PagoCompra {
        $cuenta = CuentaPorPagar::query()
            ->with('compra')
            ->whereKey($cuenta->id)
            ->lockForUpdate()
            ->firstOrFail();

        $metodoPago = strtoupper(trim((string) ($pago['metodo_pago'] ?? '')));
        $monto = round((float) ($pago['monto'] ?? 0), 2);

        if ($metodoPago === '' || $monto <= 0) {
            throw new RuntimeException('El método de pago y el monto son obligatorios.');
        }

        $registro = PagoCompra::create([
            'compra_id' => $cuenta->compra_id,
            'cuenta_por_pagar_id' => $cuenta->id,
            'user_id' => $user?->id,
            'metodo_pago' => $metodoPago,
            'monto' => $monto,
            'referencia_operacion' => $pago['referencia_operacion'] ?? null,
            'moneda' => $cuenta->compra->moneda ?? 'PEN',
            'fecha_pago' => now(),
            'estado' => 1,
            'observacion' => $pago['observacion'] ?? null,
        ]);

        $medio = $metodoPago === 'EFECTIVO' ? 'EFECTIVO' : 'BANCO';

        $this->tesoreriaService->registrarCompraPago(
            $cuenta->compra,
            $metodoPago,
            $monto,
            $pago['referencia_operacion'] ?? null,
            $user
        );

        $this->sincronizarCuenta($cuenta);

        return $registro;
    }

    protected function sincronizarCuenta(CuentaPorPagar $cuenta): CuentaPorPagar
    {
        $cuenta->loadMissing('pagos');

        $montoPagado = round(
            (float) $cuenta->pagos()
                ->where('estado', 1)
                ->sum('monto'),
            2
        );

        $saldoPendiente = round(max(0, (float) $cuenta->total - $montoPagado), 2);

        $estado = 'PENDIENTE';
        if ($montoPagado > 0 && $saldoPendiente > 0) {
            $estado = 'PARCIAL';
        } elseif ($saldoPendiente <= 0) {
            $estado = 'PAGADA';
        }

        $cuenta->update([
            'monto_pagado' => $montoPagado,
            'saldo_pendiente' => $saldoPendiente,
            'estado' => $estado,
            'fecha_cancelacion' => $saldoPendiente <= 0 ? now() : null,
        ]);

        $compra = $cuenta->compra()->first();
        if ($compra) {
            $compra->update([
                'monto_pagado' => $montoPagado,
                'saldo_pendiente' => $saldoPendiente,
                'estado_pago' => $estado,
                'fecha_pago_total' => $saldoPendiente <= 0 ? now() : null,
                'fecha_vencimiento' => $cuenta->fecha_vencimiento,
            ]);
        }

        return $cuenta->fresh(['pagos', 'compra']);
    }
}