<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovimientoCajaRequest;
use App\Models\MovimientoCaja;
use App\Models\SesionCaja;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MovimientoCajaController extends Controller implements HasMiddleware
{
    public function __construct(protected CajaService $cajaService) { }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:movimientos_caja', only: ['index', 'create', 'store']),
        ];
    }

    public function index(Request $request)
    {
        $query = MovimientoCaja::with(['sesionCaja.caja', 'sesionCaja.user'])
            ->latest('id');

        $query->when($request->filled('tipo'), fn($q) => $q->where('tipo', $request->tipo))
              ->when($request->filled('origen'), fn($q) => $q->where('origen', $request->origen))
              ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
              ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta));

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $movimientos = $query->paginate($perPage)->withQueryString();

        return view('movimiento_caja.index', compact('movimientos', 'perPage'));
    }

    public function create()
    {
        $sesionesAbiertas = SesionCaja::with(['caja', 'user'])
            ->where('estado_sesion', 'ABIERTA')
            ->latest('id')
            ->get();

        return view('movimiento_caja.create', compact('sesionesAbiertas'));
    }

    public function store(StoreMovimientoCajaRequest $request)
    {
        $data = $request->validated();

        try {
            $sesion = SesionCaja::query()
                ->whereKey($data['sesion_caja_id'])
                ->where('estado_sesion', 'ABIERTA')
                ->lockForUpdate()
                ->firstOrFail();

            $this->cajaService->registrarMovimiento($sesion, [
                'tipo' => $data['tipo'],
                'origen' => $data['origen'],
                'descripcion' => $data['descripcion'],
                'monto' => $data['monto'],
                'referencia_type' => $data['referencia_type'] ?? null,
                'referencia_id' => $data['referencia_id'] ?? null,
            ]);

            $this->cajaService->recalcularSaldoEsperado($sesion);

            return redirect()->route('movimientos-caja.index')->with('success', 'Movimiento registrado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el movimiento: ' . $e->getMessage()]);
        }
    }
}