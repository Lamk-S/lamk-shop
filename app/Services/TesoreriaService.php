<?php

namespace App\Services;

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
            $tipo = strtoupper($data['tipo']);

            $saldoAnterior = (float) $tesoreria->saldo_actual;
            $saldoPosterior = $tipo === 'INGRESO'
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
                'tipo' => $tipo,
                'medio' => strtoupper($data['medio']),
                'origen' => strtoupper($data['origen']),
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
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-EFECTIVO', 'Caja General', 'EFECTIVO');

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => 'INGRESO',
            'medio' => 'EFECTIVO',
        ]));
    }

    public function registrarEgresoEfectivo(array $data): MovimientoTesoreria
    {
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-EFECTIVO', 'Caja General', 'EFECTIVO');

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => 'EGRESO',
            'medio' => 'EFECTIVO',
        ]));
    }

    public function registrarIngresoBanco(array $data): MovimientoTesoreria
    {
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-BANCO', 'Banco Principal', 'BANCO');

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => 'INGRESO',
            'medio' => 'BANCO',
        ]));
    }

    public function registrarEgresoBanco(array $data): MovimientoTesoreria
    {
        $tesoreria = $this->obtenerBloqueadaPorCodigo('TES-BANCO', 'Banco Principal', 'BANCO');

        return $this->registrarMovimiento(array_merge($data, [
            'tesoreria_id' => $tesoreria->id,
            'tipo' => 'EGRESO',
            'medio' => 'BANCO',
        ]));
    }

    public function registrarCierreCaja(SesionCaja $sesion, float $monto, ?User $user = null): MovimientoTesoreria
    {
        return $this->registrarIngresoEfectivo([
            'user_id' => $user?->id,
            'sesion_caja_id' => $sesion->id,
            'origen' => 'CIERRE_CAJA',
            'descripcion' => 'Traslado de efectivo desde cierre de caja #' . $sesion->id,
            'monto' => $monto,
            'referencia' => 'CIERRE_CAJA',
        ]);
    }

    public function origenDesdeMetodoPago(string $metodoPago): array
    {
        return match (strtoupper($metodoPago)) {
            'EFECTIVO' => ['codigo' => 'TES-EFECTIVO', 'nombre' => 'Caja General', 'tipo' => 'EFECTIVO'],
            'TARJETA', 'YAPE', 'PLIN', 'TRANSFERENCIA' => ['codigo' => 'TES-BANCO', 'nombre' => 'Banco Principal', 'tipo' => 'BANCO'],
            default => ['codigo' => 'TES-BANCO', 'nombre' => 'Banco Principal', 'tipo' => 'BANCO'],
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
            'tipo' => 'EGRESO',
            'medio' => $origen['tipo'],
            'origen' => 'COMPRA_PRODUCTO',
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

        return $this->registrarMovimiento([
            'tesoreria_id' => $tesoreria->id,
            'user_id' => $user?->id,
            'sesion_caja_id' => $sesionCaja?->id,
            'venta_id' => $venta->id,
            'tipo' => 'INGRESO',
            'medio' => $origen['tipo'],
            'origen' => $origen['tipo'] === 'EFECTIVO'
                ? 'VENTA_EFECTIVO'
                : ($metodoPago === 'TARJETA' ? 'VENTA_TARJETA' : 'VENTA_TRANSFERENCIA'),
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
        $origenInfo = strtoupper($medio) === 'EFECTIVO'
            ? ['codigo' => 'TES-EFECTIVO', 'nombre' => 'Caja General', 'tipo' => 'EFECTIVO']
            : ['codigo' => 'TES-BANCO', 'nombre' => 'Banco Principal', 'tipo' => 'BANCO'];

        $tesoreria = $this->obtenerBloqueadaPorCodigo($origenInfo['codigo'], $origenInfo['nombre'], $origenInfo['tipo']);

        return $this->registrarMovimiento([
            'tesoreria_id' => $tesoreria->id,
            'user_id' => $userId,
            'sesion_caja_id' => $sesionCajaId,
            'venta_id' => $ventaId,
            'compra_id' => $compraId,
            'tipo' => 'EGRESO',
            'medio' => $origenInfo['tipo'],
            'origen' => $origen,
            'descripcion' => $descripcion,
            'monto' => $monto,
            'numero_operacion' => $numeroOperacion,
        ]);
    }
}