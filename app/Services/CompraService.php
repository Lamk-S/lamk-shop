<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CompraProducto;
use App\Models\Comprobante;
use App\Models\EmpresaConfiguracion;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CompraService
{
    public function __construct(
        protected InventarioService $inventarioService,
        protected TesoreriaService $tesoreriaService,
        protected ComprobanteService $comprobanteService,
        protected AuditoriaService $auditoriaService,
        protected CuentasPorPagarService $cuentasPorPagarService
    ) {
    }

    public function registrar(array $data, User $user, ?Request $request = null): Compra
    {
        return DB::transaction(function () use ($data, $user, $request) {
            $empresa = EmpresaConfiguracion::query()->first();
            $igv = (float) ($empresa?->igv_porcentaje ?? 18.00);

            $proveedor = Proveedor::query()
                ->with(['persona.documento'])
                ->whereKey($data['proveedor_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $datosComprobante = [
                'comprobante_id' => null,
                'tipo_comprobante' => null,
                'serie' => null,
                'correlativo' => null,
            ];

            if (!empty($data['comprobante_id'])) {
                $comprobante = Comprobante::query()
                    ->whereKey($data['comprobante_id'])
                    ->where('uso_comprobante', 'COMPRA')
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->firstOrFail();

                $datosComprobante = $this->comprobanteService->reservarCorrelativo($comprobante);
            }

            $subtotal = 0.0;
            $descuentoTotal = 0.0;
            $impuestoTotal = 0.0;
            $total = 0.0;
            $lineas = [];

            foreach ($data['detalles'] as $row) {
                $variante = ProductoVariante::query()
                    ->with(['producto', 'talla'])
                    ->whereKey($row['producto_variante_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $producto = Producto::query()
                    ->whereKey($variante->producto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $cantidad = (int) $row['cantidad'];
                $costoUnitario = (float) $row['costo_unitario'];
                $descuento = (float) ($row['descuento'] ?? 0);

                $base = round(($cantidad * $costoUnitario) - $descuento, 2);
                $impuesto = $producto->afecto_igv ? round($base * $igv / 100, 2) : 0.00;
                $totalLinea = round($base + $impuesto, 2);

                $lineas[] = [
                    'variante' => $variante,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costoUnitario,
                    'descuento' => $descuento,
                    'subtotal' => round($cantidad * $costoUnitario, 2),
                    'impuesto' => $impuesto,
                    'total' => $totalLinea,
                    'producto_codigo' => $producto->codigo,
                    'producto_nombre' => $producto->nombre,
                    'talla_codigo' => $variante->talla->codigo,
                    'talla_nombre' => $variante->talla->nombre,
                ];

                $subtotal += round($cantidad * $costoUnitario, 2);
                $descuentoTotal += $descuento;
                $impuestoTotal += $impuesto;
                $total += $totalLinea;
            }

            $persona = $proveedor->persona;
            $documento = $persona?->documento;

            $compra = Compra::create([
                'proveedor_id' => $proveedor->id,
                'user_id' => $user->id,
                'comprobante_id' => $datosComprobante['comprobante_id'],
                'tipo_comprobante' => $datosComprobante['tipo_comprobante'],
                'serie' => $datosComprobante['serie'],
                'correlativo' => $datosComprobante['correlativo'],

                'proveedor_tipo_documento' => $documento?->codigo,
                'proveedor_numero_documento' => $persona?->numero_documento,
                'proveedor_nombre' => $persona?->nombre_completo,
                'proveedor_direccion' => $persona?->direccion,
                'proveedor_telefono' => $persona?->telefono,
                'proveedor_email' => $persona?->email,

                'metodo_pago' => strtoupper((string) $data['metodo_pago']),
                'moneda' => $data['moneda'] ?? 'PEN',
                'fecha_emision' => $data['fecha_emision'] ?? now(),

                'subtotal' => round($subtotal, 2),
                'descuento_total' => round($descuentoTotal, 2),
                'impuesto_total' => round($impuestoTotal, 2),
                'total' => round($total, 2),

                'monto_pagado' => 0,
                'saldo_pendiente' => round($total, 2),
                'estado_pago' => 'PENDIENTE',
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
                'fecha_pago_total' => null,

                'estado_documento' => 'RECEPCIONADA',
                'observacion' => $data['observacion'] ?? null,
                'motivo_anulacion' => null,
                'anulado_at' => null,
            ]);

            foreach ($lineas as $linea) {
                $this->inventarioService->registrarEntrada(
                    $linea['variante'],
                    $linea['cantidad'],
                    $linea['costo_unitario'],
                    'Compra #' . $compra->id,
                    $compra,
                    $user
                );

                CompraProducto::create([
                    'compra_id' => $compra->id,
                    'producto_variante_id' => $linea['variante']->id,
                    'cantidad' => $linea['cantidad'],
                    'costo_unitario' => $linea['costo_unitario'],
                    'descuento' => $linea['descuento'],
                    'subtotal' => $linea['subtotal'],
                    'impuesto' => $linea['impuesto'],
                    'total' => $linea['total'],
                    'producto_codigo' => $linea['producto_codigo'],
                    'producto_nombre' => $linea['producto_nombre'],
                    'talla_codigo' => $linea['talla_codigo'],
                    'talla_nombre' => $linea['talla_nombre'],
                ]);
            }

            $pagos = $this->normalizarPagosCompra($data, (float) $compra->total);

            $cuenta = $this->cuentasPorPagarService->crearDesdeCompra(
                $compra,
                $pagos,
                $data['fecha_vencimiento'] ?? null,
                $user
            );

            $compra->update([
                'monto_pagado' => $cuenta->monto_pagado,
                'saldo_pendiente' => $cuenta->saldo_pendiente,
                'estado_pago' => $cuenta->estado,
                'fecha_vencimiento' => $cuenta->fecha_vencimiento,
                'fecha_pago_total' => $cuenta->estado === 'PAGADA' ? now() : null,
            ]);

            $this->auditoriaService->registrar(
                'Compra',
                $compra->id,
                'CREAR',
                [],
                $compra->fresh()->toArray(),
                $user,
                $request
            );

            return $compra->fresh([
                'detalles',
                'comprobante',
                'proveedor.persona.documento',
                'cuentaPorPagar.pagos',
            ]);
        });
    }

    public function anular(Compra $compra, string $motivo, User $user, ?Request $request = null): Compra
    {
        return DB::transaction(function () use ($compra, $motivo, $user, $request) {
            $compra = Compra::query()
                ->with([
                    'detalles.productoVariante.producto',
                    'comprobante',
                    'cuentaPorPagar.pagos',
                    'proveedor.persona.documento',
                ])
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($compra->estado_documento === 'ANULADA') {
                return $compra;
            }

            foreach ($compra->detalles as $detalle) {
                $this->inventarioService->revertirEntrada(
                    $detalle->productoVariante,
                    (int) $detalle->cantidad,
                    (float) $detalle->costo_unitario,
                    'Anulación de compra #' . $compra->id . ' - reversa de ingreso de mercadería',
                    $compra,
                    $user
                );
            }

            $this->cuentasPorPagarService->anularPorCompra($compra, $user);

            $compra->update([
                'estado_documento' => 'ANULADA',
                'motivo_anulacion' => $motivo,
                'anulado_at' => now(),
            ]);

            $this->auditoriaService->registrar(
                'Compra',
                $compra->id,
                'ANULAR',
                [],
                $compra->fresh()->toArray(),
                $user,
                $request
            );

            return $compra->fresh([
                'detalles',
                'comprobante',
                'proveedor.persona.documento',
                'cuentaPorPagar.pagos',
            ]);
        });
    }

    protected function normalizarPagosCompra(array $data, float $total): array
    {
        $pagos = collect($data['pagos'] ?? [])
            ->map(function (array $pago) {
                return [
                    'metodo_pago' => strtoupper(trim((string) ($pago['metodo_pago'] ?? ''))),
                    'monto' => (float) ($pago['monto'] ?? 0),
                    'referencia_operacion' => $pago['referencia_operacion'] ?? null,
                    'observacion' => $pago['observacion'] ?? null,
                ];
            })
            ->filter(fn (array $pago) => $pago['metodo_pago'] !== '' && $pago['monto'] > 0)
            ->values()
            ->all();

        if (!empty($pagos)) {
            return $pagos;
        }

        $metodoPago = strtoupper((string) ($data['metodo_pago'] ?? 'CREDITO'));

        if ($metodoPago === 'CREDITO') {
            return [];
        }

        if ($metodoPago === 'MIXTO') {
            throw new RuntimeException('Para una compra mixta debes enviar el detalle de pagos.');
        }

        return [
            [
                'metodo_pago' => $metodoPago,
                'monto' => $total,
                'referencia_operacion' => null,
                'observacion' => 'Pago registrado junto con la compra.',
            ],
        ];
    }
}