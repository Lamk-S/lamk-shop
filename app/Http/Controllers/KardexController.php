<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Producto;
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
        $query = Kardex::query()
            ->with([
                'productoVariante.producto.marca',
                'productoVariante.talla',
                'user:id,name',
            ])
            ->latest('id');

        $query->when($request->filled('q'), function ($q) use ($request) {
            $search = trim((string) $request->input('q'));
            $q->where(function ($subQ) use ($search) {
                $subQ->where('descripcion', 'like', "%{$search}%")
                    ->orWhereHas('productoVariante', function ($qv) use ($search) {
                        $qv->where('codigo_variante', 'like', "%{$search}%")
                            ->orWhere('codigo_barra', 'like', "%{$search}%")
                            ->orWhereHas('producto', function ($qp) use ($search) {
                                $qp->where('codigo', 'like', "%{$search}%")
                                    ->orWhere('nombre', 'like', "%{$search}%");
                            })
                            ->orWhereHas('talla', function ($qt) use ($search) {
                                $qt->where('codigo', 'like', "%{$search}%")
                                    ->orWhere('nombre', 'like', "%{$search}%");
                            });
                    });
            });
        });

        $query->when($request->filled('producto_id'), function ($q) use ($request) {
            $q->whereHas('productoVariante', function ($qv) use ($request) {
                $qv->where('producto_id', $request->input('producto_id'));
            });
        });

        $query->when($request->filled('producto_variante_id'), function ($q) use ($request) {
            $q->where('producto_variante_id', $request->input('producto_variante_id'));
        });

        $query->when($request->filled('tipo_transaccion'), function ($q) use ($request) {
            $q->where('tipo_transaccion', $request->input('tipo_transaccion'));
        });

        $query->when($request->filled('fecha'), function ($q) use ($request) {
            $q->whereDate('created_at', $request->input('fecha'));
        });

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100], true) ? $perPage : 15;

        $kardex = $query->paginate($perPage)->withQueryString();

        $productos = Producto::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        return view('kardex.index', compact('kardex', 'productos', 'perPage'));
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