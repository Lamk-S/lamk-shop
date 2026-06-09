<?php

namespace App\Http\Controllers;

use App\Models\MovimientoTesoreria;
use App\Models\Tesoreria;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TesoreriaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_tesoreria', only: ['index']),
        ];
    }

    public function index()
    {
        $tesoreria = Tesoreria::where('estado', 1)->first();

        if (!$tesoreria) {
            return redirect()
                ->route('panel')
                ->with('warning', 'No existe la tesorería principal.');
        }

        $movimientos = MovimientoTesoreria::with(['user', 'venta', 'compra', 'sesionCaja.caja'])
            ->where('tesoreria_id', $tesoreria->id)
            ->latest('id')
            ->limit(20)
            ->get();

        return view('tesoreria.index', compact('tesoreria', 'movimientos'));
    }
}