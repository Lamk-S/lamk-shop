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
            new Middleware('permission:ver-kardex', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = Kardex::with('producto', 'user')->latest();

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('tipo_transaccion')) {
            $query->where('tipo_transaccion', $request->tipo_transaccion);
        }

        $kardex = $query->get();
        $productos = Producto::where('estado', 1)->get();

        return view('kardex.index', compact('kardex', 'productos'));
    }

    public function show(Kardex $kardex)
    {
        $kardex->load('producto', 'user');
        return view('kardex.show', compact('kardex'));
    }
}