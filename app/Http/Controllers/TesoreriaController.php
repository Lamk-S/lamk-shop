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

        $query = MovimientoTesoreria::with([
                'tesoreria',
                'user:id,name',
                'venta:id,serie,correlativo',
                'compra:id,correlativo',
                'sesionCaja.caja:id,nombre',
            ])
            ->latest('id');

        $query->when($request->filled('tesoreria_id'), fn($q) => $q->where('tesoreria_id', $request->tesoreria_id))
              ->when($request->filled('tipo'), fn($q) => $q->where('tipo', $request->tipo))
              ->when($request->filled('medio'), fn($q) => $q->where('medio', $request->medio))
              ->when($request->filled('origen'), fn($q) => $q->where('origen', $request->origen));

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50], true) ? $perPage : 15;

        $movimientos = $query->paginate($perPage)->withQueryString();

        return view('tesoreria.index', compact(
            'tesorerias',
            'tesoreriaEfectivo',
            'tesoreriaBanco',
            'movimientos',
            'perPage'
        ));
    }
}