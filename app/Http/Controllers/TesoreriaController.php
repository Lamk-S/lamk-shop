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
        $tesorerias = Tesoreria::withTrashed()->orderBy('nombre')->get();
        $tesoreria = $tesorerias->first();

        if (!$tesoreria) {
            return redirect()
                ->route('panel')
                ->with('warning', 'No existe ninguna tesorería registrada.');
        }

        $movimientos = MovimientoTesoreria::with(['user', 'venta', 'compra', 'sesionCaja.caja'])
            ->where('tesoreria_id', $tesoreria->id)
            ->latest('id')
            ->limit(20)
            ->get();

        return view('tesoreria.index', compact('tesoreria', 'tesorerias', 'movimientos'));
    }
}