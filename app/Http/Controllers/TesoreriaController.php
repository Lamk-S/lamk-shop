<?php

namespace App\Http\Controllers;

use App\Models\MovimientoTesoreria;
use App\Models\Tesoreria;
use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        $tesorerias = Tesoreria::withTrashed()
            ->orderBy('tipo_cuenta')
            ->orderBy('nombre')
            ->get();

        if ($tesorerias->isEmpty()) {
            return redirect()
                ->route('panel')
                ->with('warning', 'No existe ninguna tesorería registrada.');
        }

        $tesoreriaEfectivo = $tesorerias->firstWhere('tipo_cuenta', 'EFECTIVO');
        $tesoreriaBanco = $tesorerias->firstWhere('tipo_cuenta', 'BANCO');

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50], true) ? $perPage : 10;

        $movimientos = MovimientoTesoreria::with([
                'tesoreria',
                'user',
                'venta',
                'compra',
                'sesionCaja.caja',
            ])
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('tesoreria.index', compact(
            'tesorerias',
            'tesoreriaEfectivo',
            'tesoreriaBanco',
            'movimientos',
            'perPage'
        ));
    }
}