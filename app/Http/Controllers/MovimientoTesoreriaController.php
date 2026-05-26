<?php

namespace App\Http\Controllers;

use App\Models\MovimientoTesoreria;
use App\Models\Tesoreria;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MovimientoTesoreriaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-movimiento-tesoreria', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $query = MovimientoTesoreria::with(['tesoreria', 'user', 'venta', 'compra', 'sesionCaja.caja'])
            ->latest();

        if ($request->filled('tesoreria_id')) {
            $query->where('tesoreria_id', $request->tesoreria_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('origen')) {
            $query->where('origen', $request->origen);
        }

        $movimientos = $query->get();
        $tesorerias = Tesoreria::all();

        return view('movimiento_tesoreria.index', compact('movimientos', 'tesorerias'));
    }
}