<?php

namespace App\Services;

use App\Enums\MetodoPago;
use App\Enums\OrigenMovimientoTesoreria;
use App\Enums\TipoCuenta;
use App\Enums\TipoMovimiento;
use App\Models\Compra;
use App\Models\MovimientoTesoreria;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TesoreriaService
{
    public function obtenerOCrear(string $codigo, string $nombre, string $tipoCuenta): Tesoreria
    {
        return Tesoreria::firstOrCreate(
            ['codigo' => $codigo],
            [
                'nombre' => $nombre,
                'tipo_cuenta' => $tipoCuenta,
                'saldo_actual' => 0,
                'estado' => 1,
            ]
        );
    }

    public function obtenerBloqueadaPorCodigo(string $codigo, string $nombre, string $tipoCuenta): Tesoreria
    {
        return DB::transaction(function () use ($codigo, $nombre, $tipoCuenta) {
            $tesoreria = Tesoreria::query()
                ->where('codigo', $codigo)
                ->lockForUpdate()
                ->first();

            if ($tesoreria) {
                return $tesoreria;
            }

            return Tesoreria::create([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'tipo_cuenta' => $tipoCuenta,
                'saldo_actual' => 0,
                'estado' => 1,
            ]);
        });
    }

    public function registrarMovimiento(array $data): MovimientoTesoreria
    {
        return DB::transaction(function () use ($data) {
            $tesoreria = Tesoreria::query()
                ->whereKey($data['tesoreria_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $monto = (float) $data['monto'];
            
            $saldoAnterior = (float) $tesoreria->saldo_actual;
            $saldoPosterior = $data['tipo'] === TipoMovimiento::INGRESO
                ? round($saldoAnterior + $monto, 2)
                : round($saldoAnterior - $monto, 2);

            if ($saldoPosterior < 0) {
                throw new RuntimeException('Saldo insuficiente en tesorería.');
            }

            $tesoreria->update([
                'saldo_actual' => $saldoPosterior,
            ]);

            return MovimientoTesoreria::create([
                'tesoreria_id' => $tesoreria->id,
                'user_id' => $data['user_id'] ?? null,
                'sesion_caja_id' => $data['sesion_caja_id'] ?? null,
                'venta_id' => $data['venta_id'] ?? null,
                'compra_id' => $data['compra_id'] ?? null,
                'tipo' => $data['tipo'],
                'medio' => $data['medio'],
                'origen' => $data['origen'],
                'descripcion' => $data['descripcion'],
                'monto' => $monto,
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoPosterior,
                'numero_operacion' => $data['numero_operacion'] ?? null,
                'referencia' => $data['referencia'] ?? null,
            ]);
        });
    }

    public function registrarIngresoEfectivo(array $data): MovimientoTesoreria
    {
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-EFECTIVO', 'Caja General', TipoCuenta::EFECTIVO->value);

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => TipoMovimiento::INGRESO,
            'medio' => TipoCuenta::EFECTIVO->value,
        ]));
    }

    public function registrarEgresoEfectivo(array $data): MovimientoTesoreria
    {
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-EFECTIVO', 'Caja General', TipoCuenta::EFECTIVO->value);

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => TipoMovimiento::EGRESO,
            'medio' => TipoCuenta::EFECTIVO->value,
        ]));
    }

    public function registrarIngresoBanco(array $data): MovimientoTesoreria
    {
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-BANCO', 'Banco Principal', TipoCuenta::BANCO->value);

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => TipoMovimiento::INGRESO,
            'medio' => TipoCuenta::BANCO->value,
        ]));
    }

    public function registrarEgresoBanco(array $data): MovimientoTesoreria
    {
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-BANCO', 'Banco Principal', TipoCuenta::BANCO->value);

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => TipoMovimiento::EGRESO,
            'medio' => TipoCuenta::BANCO->value,
        ]));
    }

    public function registrarCierreCaja(SesionCaja $sesion, float $monto, ?User $user = null): MovimientoTesoreria
    {
        return $this->registrarIngresoEfectivo([
            'user_id' => $user?->id,
            'sesion_caja_id' => $sesion->id,
            'origen' => OrigenMovimientoTesoreria::CIERRE_CAJA,
            'descripcion' => 'Traslado de efectivo desde cierre de caja #' . $sesion->id,
            'monto' => $monto,
            'referencia' => 'CIERRE_CAJA',
        ]);
    }

    public function origenDesdeMetodoPago(string $metodoPago): array
    {
        return match (strtoupper($metodoPago)) {
            MetodoPago::EFECTIVO->value => ['codigo' => 'TES-EFECTIVO', 'nombre' => 'Caja General', 'tipo' => TipoCuenta::EFECTIVO->value],
            MetodoPago::TARJETA->value, MetodoPago::YAPE->value, MetodoPago::PLIN->value, MetodoPago::TRANSFERENCIA->value 
                => ['codigo' => 'TES-BANCO', 'nombre' => 'Banco Principal', 'tipo' => TipoCuenta::BANCO->value],
            default => ['codigo' => 'TES-BANCO', 'nombre' => 'Banco Principal', 'tipo' => TipoCuenta::BANCO->value],
        };
    }

    public function registrarCompraPago(Compra $compra, string $metodoPago, float $monto, ?string $referencia, ?User $user = null): MovimientoTesoreria
    {
        $origen = $this->origenDesdeMetodoPago($metodoPago);
        $tesoreria = $this->obtenerBloqueadaPorCodigo($origen['codigo'], $origen['nombre'], $origen['tipo']);

        return $this->registrarMovimiento([
            'tesoreria_id' => $tesoreria->id,
            'user_id' => $user?->id,
            'compra_id' => $compra->id,
            'tipo' => TipoMovimiento::EGRESO,
            'medio' => $origen['tipo'],
            'origen' => OrigenMovimientoTesoreria::COMPRA_PRODUCTO,
            'descripcion' => 'Pago de compra #' . $compra->id,
            'monto' => $monto,
            'numero_operacion' => $referencia,
        ]);
    }

    public function registrarVentaIngreso(
        Venta $venta,
        string $metodoPago,
        float $monto,
        ?string $referencia,
        ?SesionCaja $sesionCaja,
        ?User $user = null
    ): MovimientoTesoreria {
        $origen = $this->origenDesdeMetodoPago($metodoPago);
        $tesoreria = $this->obtenerBloqueadaPorCodigo($origen['codigo'], $origen['nombre'], $origen['tipo']);

        $origenMovimiento = $origen['tipo'] === TipoCuenta::EFECTIVO->value
            ? OrigenMovimientoTesoreria::VENTA_EFECTIVO
            : ($metodoPago === MetodoPago::TARJETA->value ? OrigenMovimientoTesoreria::VENTA_TARJETA : OrigenMovimientoTesoreria::VENTA_TRANSFERENCIA);

        return $this->registrarMovimiento([
            'tesoreria_id' => $tesoreria->id,
            'user_id' => $user?->id,
            'sesion_caja_id' => $sesionCaja?->id,
            'venta_id' => $venta->id,
            'tipo' => TipoMovimiento::INGRESO,
            'medio' => $origen['tipo'],
            'origen' => $origenMovimiento,
            'descripcion' => 'Cobro de venta #' . $venta->id,
            'monto' => $monto,
            'numero_operacion' => $referencia,
        ]);
    }

    public function registrarAnulacion(
        string $medio,
        float $monto,
        string $origen,
        string $descripcion,
        ?int $userId = null,
        ?int $sesionCajaId = null,
        ?int $ventaId = null,
        ?int $compraId = null,
        ?string $numeroOperacion = null
    ): MovimientoTesoreria {
        $origenInfo = strtoupper($medio) === TipoCuenta::EFECTIVO->value
            ? ['codigo' => 'TES-EFECTIVO', 'nombre' => 'Caja General', 'tipo' => TipoCuenta::EFECTIVO->value]
            : ['codigo' => 'TES-BANCO', 'nombre' => 'Banco Principal', 'tipo' => TipoCuenta::BANCO->value];

        $tesoreria = $this->obtenerBloqueadaPorCodigo($origenInfo['codigo'], $origenInfo['nombre'], $origenInfo['tipo']);

        return $this->registrarMovimiento([
            'tesoreria_id' => $tesoreria->id,
            'user_id' => $userId,
            'sesion_caja_id' => $sesionCajaId,
            'venta_id' => $ventaId,
            'compra_id' => $compraId,
            'tipo' => TipoMovimiento::EGRESO,
            'medio' => $origenInfo['tipo'],
            'origen' => OrigenMovimientoTesoreria::ANULACION,
            'descripcion' => $descripcion,
            'monto' => $monto,
            'numero_operacion' => $numeroOperacion,
        ]);
    }
}