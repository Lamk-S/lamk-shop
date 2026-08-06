<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Events\BarcodeScanned;
use App\Models\ProductoVariante;
use App\Models\Producto;

Route::post('/scanner/push', function (Request $request) {
    $request->validate([
        'codigo' => 'required|string',
    ]);

    $codigo = $request->codigo;

    broadcast(new BarcodeScanned($codigo));

    $variante = ProductoVariante::with(['producto', 'talla'])
        ->where('codigo_variante', $codigo)
        ->first();

    $nombre = null;
    $talla = null;
    $imagen_url = null;

    if ($variante) {
        $nombre = $variante->producto->nombre;
        $talla = $variante->talla ? $variante->talla->nombre : null; 
        
        if ($variante->producto->img_path) {
            $imagen_url = asset('storage/' . $variante->producto->img_path);
        }
    } else {
        $producto = Producto::where('codigo', $codigo)->first();
        
        if ($producto) {
            $nombre = $producto->nombre;
            $talla = 'Única';
            
            if ($producto->img_path) {
                $imagen_url = asset('storage/' . $producto->img_path);
            }
        } else {
            return response()->json([
                'status' => 'error', 
                'message' => 'Producto no encontrado en la base de datos'
            ], 404);
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Producto registrado en caja',
        'nombre' => $nombre,
        'talla' => $talla,
        'imagen_url' => $imagen_url
    ]);
});