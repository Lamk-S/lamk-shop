<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\SesionCaja;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CajaService
{
    public function __construct(
        protected TesoreriaService $tesoreriaService
    ) {
    }

    public function obtenerSesionAbiertaPorUsuario(int $userId): ?SesionCaja
    {
        return SesionCaja::query()
            ->where('user_id', $userId)
            ->where('estado_sesion', 'ABIERTA')
            ->first();
    }

    public function obtenerSesionAbiertaPorCaja(int $cajaId): ?SesionCaja
    {
        return SesionCaja::query()
            ->where('caja_id', $cajaId)
            ->where('estado_sesion', 'ABIERTA')
            ->first();
    }

    public function abrirCaja(User $user, int $cajaId, ?float $saldoInicial = null, ?string $observacion = null): SesionCaja
    {
        return DB::transaction(function () use ($user, $cajaId, $saldoInicial, $observacion) {
            $caja = Caja::query()
                ->whereKey($cajaId)
                ->where('estado', 1)
                ->lockForUpdate()
                ->firstOrFail();

            $cajaAbierta = SesionCaja::query()
                ->where('caja_id', $caja->id)
                ->where('estado_sesion', 'ABIERTA')
                ->lockForUpdate()
                ->exists();

            if ($cajaAbierta) {
                throw new RuntimeException('Esta caja ya tiene una sesión abierta.');
            }

            $usuarioAbierto = SesionCaja::query()
                ->where('user_id', $user->id)
                ->where('estado_sesion', 'ABIERTA')
                ->lockForUpdate()
                ->exists();

            if ($usuarioAbierto) {
                throw new RuntimeException('El usuario ya tiene una sesión de caja abierta.');
            }

            $sesion = SesionCaja::create([
                'caja_id' => $caja->id,
                'user_id' => $user->id,
                'fecha_hora_apertura' => now(),
                'saldo_inicial' => $saldoInicial ?? $caja->fondo_fijo,
                'saldo_final_declarado' => null,
                'saldo_final_esperado' => null,
                'diferencia' => null,
                'estado_sesion' => 'ABIERTA',
                'observacion_apertura' => $observacion,
                'observacion_cierre' => null,
            ]);

            MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'INGRESO',
                'origen' => 'APERTURA',
                'descripcion' => 'Apertura de caja ' . $caja->nombre,
                'monto' => $sesion->saldo_inicial,
                'referencia_type' => null,
                'referencia_id' => null,
            ]);

            return $sesion;
        });
    }

    public function cerrarCaja(SesionCaja $sesion, float $saldoDeclarado, ?string $observacion = null, ?User $userCierre = null): SesionCaja
    {
        return DB::transaction(function () use ($sesion, $saldoDeclarado, $observacion, $userCierre) {
            $sesion = SesionCaja::query()
                ->whereKey($sesion->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($sesion->estado_sesion !== 'ABIERTA') {
                throw new RuntimeException('La sesión ya está cerrada o anulada.');
            }

            $ingresos = (float) $sesion->movimientosCaja()->where('tipo', 'INGRESO')->sum('monto');
            $egresos = (float) $sesion->movimientosCaja()->where('tipo', 'EGRESO')->sum('monto');
            $saldoEsperado = round((float) $sesion->saldo_inicial + $ingresos - $egresos, 2);
            $diferencia = round($saldoDeclarado - $saldoEsperado, 2);

            $fondoFijo = (float) ($sesion->caja?->fondo_fijo ?? 0);
            $montoTransferir = max(0, round($saldoDeclarado - $fondoFijo, 2));

            if ($montoTransferir > 0) {
                $this->tesoreriaService->registrarIngresoEfectivo([
                    'user_id' => $userCierre?->id,
                    'sesion_caja_id' => $sesion->id,
                    'origen' => 'CIERRE_CAJA',
                    'descripcion' => 'Traslado de efectivo desde cierre de sesión de caja #' . $sesion->id,
                    'monto' => $montoTransferir,
                    'referencia' => null,
                ]);
            }

            $sesion->update([
                'fecha_hora_cierre' => now(),
                'saldo_final_esperado' => $saldoEsperado,
                'saldo_final_declarado' => $saldoDeclarado,
                'diferencia' => $diferencia,
                'estado_sesion' => 'CERRADA',
                'user_cierre_id' => $userCierre?->id,
                'observacion_cierre' => $observacion,
            ]);

            return $sesion;
        });
    }

    public function registrarMovimiento(SesionCaja $sesion, array $data): MovimientoCaja
    {
        return MovimientoCaja::create([
            'sesion_caja_id' => $sesion->id,
            'tipo' => strtoupper($data['tipo']),
            'origen' => strtoupper($data['origen']),
            'descripcion' => $data['descripcion'],
            'monto' => $data['monto'],
            'referencia_type' => $data['referencia_type'] ?? null,
            'referencia_id' => $data['referencia_id'] ?? null,
        ]);
    }

    public function recalcularSaldoEsperado(SesionCaja $sesion): float
    {
        $ingresos = (float) $sesion->movimientosCaja()->where('tipo', 'INGRESO')->sum('monto');
        $egresos = (float) $sesion->movimientosCaja()->where('tipo', 'EGRESO')->sum('monto');

        $saldoEsperado = round((float) $sesion->saldo_inicial + $ingresos - $egresos, 2);

        $sesion->update([
            'saldo_final_esperado' => $saldoEsperado,
        ]);

        return $saldoEsperado;
    }
}