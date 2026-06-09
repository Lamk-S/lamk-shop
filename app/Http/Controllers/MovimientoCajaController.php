<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovimientoCajaRequest;
use App\Models\MovimientoCaja;
use App\Models\SesionCaja;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class MovimientoCajaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:movimientos_caja', only: ['index', 'create', 'store']),
        ];
    }

    public function index()
    {
        $movimientos = MovimientoCaja::with([
            'sesionCaja.caja',
            'sesionCaja.user',
        ])->latest('id')->get();

        return view('movimiento_caja.index', compact('movimientos'));
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
            DB::transaction(function () use ($data) {
                $sesion = SesionCaja::whereKey($data['sesion_caja_id'])
                    ->where('estado_sesion', 'ABIERTA')
                    ->lockForUpdate()
                    ->firstOrFail();

                MovimientoCaja::create([
                    'sesion_caja_id' => $sesion->id,
                    'tipo' => $data['tipo'],
                    'origen' => $data['origen'],
                    'descripcion' => $data['descripcion'],
                    'monto' => $data['monto'],
                    'referencia_type' => $data['referencia_type'] ?? null,
                    'referencia_id' => $data['referencia_id'] ?? null,
                ]);
            });

            return redirect()->route('movimientos-caja.index')->with('success', 'Movimiento registrado correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el movimiento: ' . $e->getMessage()]);
        }
    }
}