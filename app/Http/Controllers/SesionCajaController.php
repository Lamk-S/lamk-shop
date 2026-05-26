<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoTesoreria;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SesionCajaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-sesion-caja|abrir-sesion-caja|cerrar-sesion-caja', only: ['index', 'show']),
            new Middleware('permission:abrir-sesion-caja', only: ['create', 'store']),
            new Middleware('permission:cerrar-sesion-caja', only: ['destroy']),
        ];
    }

    public function index()
    {
        $sesiones = SesionCaja::with('caja', 'user')->latest()->get();
        return view('sesion_caja.index', compact('sesiones'));
    }

    public function create()
    {
        $cajas = Caja::where('estado', 1)->get();
        return view('sesion_caja.create', compact('cajas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'caja_id' => 'required|exists:cajas,id',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $caja = Caja::whereKey($data['caja_id'])->lockForUpdate()->firstOrFail();

                $sesionActivaCaja = SesionCaja::where('caja_id', $caja->id)
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->exists();

                if ($sesionActivaCaja) {
                    throw new Exception('Esta caja ya tiene una sesión abierta.');
                }

                $sesionActivaUsuario = SesionCaja::where('user_id', Auth::id())
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->exists();

                if ($sesionActivaUsuario) {
                    throw new Exception('El usuario ya tiene una sesión de caja abierta.');
                }

                SesionCaja::create([
                    'caja_id' => $caja->id,
                    'user_id' => Auth::id(),
                    'fecha_hora_apertura' => now(),
                    'saldo_inicial' => $caja->fondo_fijo,
                    'saldo_final_declarado' => null,
                    'saldo_final_esperado' => null,
                    'diferencia' => null,
                    'estado' => 1,
                ]);
            });

            return redirect()->route('sesiones-caja.index')->with('success', 'Sesión de caja abierta');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(SesionCaja $sesionCaja)
    {
        $sesionCaja->load('caja', 'user', 'movimientosCaja', 'ventas');
        return view('sesion_caja.show', compact('sesionCaja'));
    }

    public function destroy(Request $request, string $id)
    {
        $data = $request->validate([
            'saldo_final_declarado' => 'required|numeric|min:0',
        ]);

        try {
            $sesion = SesionCaja::with(['movimientosCaja', 'caja'])->findOrFail($id);

            if ((int) $sesion->estado === 0) {
                return back()->withErrors(['error' => 'La sesión ya está cerrada.']);
            }

            DB::transaction(function () use ($sesion, $data) {
                $ingresos = $sesion->movimientosCaja()
                    ->where('tipo', 'INGRESO')
                    ->sum('monto');

                $egresos = $sesion->movimientosCaja()
                    ->where('tipo', 'EGRESO')
                    ->sum('monto');

                $saldoEsperado = (float) $sesion->saldo_inicial + (float) $ingresos - (float) $egresos;
                $saldoDeclarado = (float) $data['saldo_final_declarado'];
                $diferencia = $saldoDeclarado - $saldoEsperado;

                $fondoFijo = (float) ($sesion->caja->fondo_fijo ?? 100);
                $montoTransferir = max(0, $saldoDeclarado - $fondoFijo);

                $tesoreria = Tesoreria::withTrashed()->firstOrCreate(
                    ['nombre' => 'Tesorería Principal'],
                    ['saldo_efectivo' => 0, 'saldo_banco' => 0, 'estado' => 1]
                );

                $saldoAnterior = (float) $tesoreria->saldo_efectivo;
                $saldoPosterior = $saldoAnterior + $montoTransferir;

                $tesoreria->update([
                    'saldo_efectivo' => $saldoPosterior,
                ]);

                MovimientoTesoreria::create([
                    'tesoreria_id' => $tesoreria->id,
                    'user_id' => Auth::id(),
                    'sesion_caja_id' => $sesion->id,
                    'tipo' => 'INGRESO',
                    'origen' => 'CIERRE_CAJA',
                    'descripcion' => 'Traslado de efectivo desde sesión de caja #' . $sesion->id,
                    'monto' => $montoTransferir,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_posterior' => $saldoPosterior,
                ]);

                $sesion->update([
                    'fecha_hora_cierre' => now(),
                    'saldo_final_esperado' => $saldoEsperado,
                    'saldo_final_declarado' => $saldoDeclarado,
                    'diferencia' => $diferencia,
                    'estado' => 0,
                ]);
            });

            return redirect()->route('sesiones-caja.index')->with('success', 'Sesión de caja cerrada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cerrar la sesión: ' . $e->getMessage()]);
        }
    }
}