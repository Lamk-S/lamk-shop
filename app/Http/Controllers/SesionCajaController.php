<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSesionCajaRequest;
use App\Models\Caja;
use App\Models\SesionCaja;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SesionCajaController extends Controller implements HasMiddleware
{
    public function __construct(protected CajaService $cajaService) { }

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
        $sesiones = SesionCaja::with(['caja', 'user', 'userCierre'])->latest('id')->get();

        return view('sesion_caja.index', compact('sesiones'));
    }

    public function create()
    {
        $cajas = Caja::where('estado', 1)->orderBy('id')->get();

        return view('sesion_caja.create', compact('cajas'));
    }

    public function store(StoreSesionCajaRequest $request)
    {
        $data = $request->validated();

        try {
            $sesion = $this->cajaService->abrirCaja(
                $request->user(),
                (int) $data['caja_id'],
                isset($data['saldo_inicial']) ? (float) $data['saldo_inicial'] : null,
                $data['observacion_apertura'] ?? null
            );

            return redirect()
                ->route('sesiones-caja.index')
                ->with('success', 'Sesión de caja abierta correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
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

    public function destroy(Request $request, SesionCaja $sesion_caja)
    {
        $data = $request->validate([
            'saldo_final_declarado' => ['required', 'numeric', 'min:0'],
            'observacion_cierre' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->cajaService->cerrarCaja(
                $sesion_caja,
                (float) $data['saldo_final_declarado'],
                $data['observacion_cierre'] ?? null,
                $request->user()
            );

            return redirect()
                ->route('sesiones-caja.index')
                ->with('success', 'Sesión de caja cerrada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cerrar la sesión: ' . $e->getMessage()]);
        }
    }
}