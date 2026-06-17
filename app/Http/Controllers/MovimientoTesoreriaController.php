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
            new Middleware('permission:gestionar_tesoreria', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $query = MovimientoTesoreria::with([
                'tesoreria',
                'user',
                'venta',
                'compra',
                'sesionCaja.caja',
            ])
            ->latest('id');

        if ($request->filled('tesoreria_id')) {
            $query->where('tesoreria_id', $request->tesoreria_id);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('medio')) {
            $query->where('medio', $request->medio);
        }
        if ($request->filled('origen')) {
            $query->where('origen', $request->origen);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $movimientos = $query->paginate($perPage)->withQueryString();
        $tesorerias = Tesoreria::orderBy('nombre')->get();

        return view('movimiento_tesoreria.index', compact('movimientos', 'tesorerias', 'perPage'));
    }
}