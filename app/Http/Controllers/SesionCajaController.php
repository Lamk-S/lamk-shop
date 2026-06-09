<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSesionCajaRequest;
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
            new Middleware('permission:gestionar_cajas|abrir_caja|cerrar_caja', only: ['index', 'show']),
            new Middleware('permission:abrir_caja', only: ['create', 'store']),
            new Middleware('permission:cerrar_caja', only: ['destroy']),
        ];
    }

    public function index()
    {
        $sesiones = SesionCaja::with(['caja', 'user', 'userCierre'])
            ->latest('id')
            ->get();

        return view('sesion_caja.index', compact('sesiones'));
    }

    public function create()
    {
        $cajas = Caja::where('estado', 1)->latest('id')->get();

        return view('sesion_caja.create', compact('cajas'));
    }

    public function store(StoreSesionCajaRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $caja = Caja::whereKey($data['caja_id'])
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->firstOrFail();

                $sesionActivaCaja = SesionCaja::where('caja_id', $caja->id)
                    ->where('estado_sesion', 'ABIERTA')
                    ->lockForUpdate()
                    ->exists();

                if ($sesionActivaCaja) {
                    throw new Exception('Esta caja ya tiene una sesión abierta.');
                }

                $sesionActivaUsuario = SesionCaja::where('user_id', Auth::id())
                    ->where('estado_sesion', 'ABIERTA')
                    ->lockForUpdate()
                    ->exists();

                if ($sesionActivaUsuario) {
                    throw new Exception('El usuario ya tiene una sesión de caja abierta.');
                }

                SesionCaja::create([
                    'caja_id' => $caja->id,
                    'user_id' => Auth::id(),
                    'fecha_hora_apertura' => now(),
                    'saldo_inicial' => $data['saldo_inicial'] ?? $caja->fondo_fijo,
                    'saldo_final_declarado' => null,
                    'saldo_final_esperado' => null,
                    'diferencia' => null,
                    'estado_sesion' => 'ABIERTA',
                    'observacion_apertura' => $data['observacion_apertura'] ?? null,
                    'observacion_cierre' => null,
                ]);
            });

            return redirect()->route('sesiones-caja.index')->with('success', 'Sesión de caja abierta correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(SesionCaja $sesionCaja)
    {
        $sesionCaja->load([
            'caja',
            'user',
            'userCierre',
            'movimientosCaja',
            'ventas.detalles',
            'movimientosTesoreria',
        ]);

        return view('sesion_caja.show', compact('sesionCaja'));
    }

    public function destroy(Request $request, string $id)
    {
        $data = $request->validate([
            'saldo_final_declarado' => 'required|numeric|min:0',
            'observacion_cierre' => 'nullable|string|max:1000',
        ]);

        try {
            $sesion = SesionCaja::with(['movimientosCaja', 'caja'])
                ->findOrFail($id);

            if ($sesion->estado_sesion !== 'ABIERTA') {
                return back()->withErrors(['error' => 'La sesión ya está cerrada o anulada.']);
            }

            DB::transaction(function () use ($sesion, $data) {
                $sesion = SesionCaja::whereKey($sesion->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $ingresos = $sesion->movimientosCaja()
                    ->where('tipo', 'INGRESO')
                    ->sum('monto');

                $egresos = $sesion->movimientosCaja()
                    ->where('tipo', 'EGRESO')
                    ->sum('monto');

                $saldoEsperado = (float) $sesion->saldo_inicial + (float) $ingresos - (float) $egresos;
                $saldoDeclarado = (float) $data['saldo_final_declarado'];
                $diferencia = $saldoDeclarado - $saldoEsperado;

                $fondoFijo = (float) ($sesion->caja->fondo_fijo ?? 0);
                $montoTransferir = max(0, $saldoDeclarado - $fondoFijo);

                $tesoreria = Tesoreria::firstOrCreate(
                    ['codigo' => 'TES-EFECTIVO'],
                    [
                        'nombre' => 'Caja General',
                        'tipo_cuenta' => 'EFECTIVO',
                        'saldo_actual' => 0,
                        'estado' => 1,
                    ]
                );

                $saldoAnterior = (float) $tesoreria->saldo_actual;
                $saldoPosterior = $saldoAnterior + $montoTransferir;

                $tesoreria->update([
                    'saldo_actual' => $saldoPosterior,
                ]);

                MovimientoTesoreria::create([
                    'tesoreria_id' => $tesoreria->id,
                    'user_id' => Auth::id(),
                    'sesion_caja_id' => $sesion->id,
                    'venta_id' => null,
                    'compra_id' => null,
                    'tipo' => 'INGRESO',
                    'medio' => 'EFECTIVO',
                    'origen' => 'CIERRE_CAJA',
                    'descripcion' => 'Traslado de efectivo desde cierre de sesión de caja #' . $sesion->id,
                    'monto' => $montoTransferir,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_posterior' => $saldoPosterior,
                    'numero_operacion' => null,
                    'referencia' => null,
                ]);

                $sesion->update([
                    'fecha_hora_cierre' => now(),
                    'saldo_final_esperado' => $saldoEsperado,
                    'saldo_final_declarado' => $saldoDeclarado,
                    'diferencia' => $diferencia,
                    'estado_sesion' => 'CERRADA',
                    'user_cierre_id' => Auth::id(),
                    'observacion_cierre' => $data['observacion_cierre'] ?? null,
                ]);
            });

            return redirect()->route('sesiones-caja.index')->with('success', 'Sesión de caja cerrada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cerrar la sesión: ' . $e->getMessage()]);
        }
    }
}