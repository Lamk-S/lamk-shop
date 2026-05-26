<?php

namespace App\Http\Controllers;

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
            new Middleware('permission:ver-movimiento-caja|crear-movimiento-caja', only: ['index']),
            new Middleware('permission:crear-movimiento-caja', only: ['create', 'store']),
        ];
    }

    public function index()
    {
        $movimientos = MovimientoCaja::with('sesionCaja.caja', 'sesionCaja.user')->latest()->get();
        return view('movimiento_caja.index', compact('movimientos'));
    }

    public function create()
    {
        $sesionesAbiertas = SesionCaja::where('estado', 1)->with('caja', 'user')->get();
        return view('movimiento_caja.create', compact('sesionesAbiertas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sesion_caja_id' => 'required|exists:sesiones_caja,id',
            'tipo' => 'required|in:INGRESO,EGRESO',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $sesion = SesionCaja::whereKey($data['sesion_caja_id'])
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->firstOrFail();

                MovimientoCaja::create($data);
            });

            return redirect()->route('movimientos-caja.index')->with('success', 'Movimiento registrado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el movimiento: ' . $e->getMessage()]);
        }
    }
}