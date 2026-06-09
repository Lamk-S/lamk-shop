<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Producto;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KardexController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver_kardex', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = Kardex::with([
                'productoVariante.producto.marca',
                'productoVariante.talla',
                'user',
            ])
            ->latest('id');

        if ($request->filled('producto_variante_id')) {
            $query->where('producto_variante_id', $request->producto_variante_id);
        }

        if ($request->filled('tipo_transaccion')) {
            $query->where('tipo_transaccion', $request->tipo_transaccion);
        }

        if ($request->filled('producto_id')) {
            $query->whereHas('productoVariante', function ($q) use ($request) {
                $q->where('producto_id', $request->producto_id);
            });
        }

        $kardex = $query->get();

        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $productoVariantes = ProductoVariante::with(['producto', 'talla'])
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        return view('kardex.index', compact('kardex', 'productos', 'productoVariantes'));
    }

    public function show(Kardex $kardex)
    {
        $kardex->load([
            'productoVariante.producto.marca',
            'productoVariante.talla',
            'user',
        ]);

        return view('kardex.show', compact('kardex'));
    }
}