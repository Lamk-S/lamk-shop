<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\BarcodeScanned;

Route::post('/scanner/push', function (Request $request) {
    $request->validate([
        'codigo' => 'required|string',
    ]);

    broadcast(new BarcodeScanned($request->codigo));

    return response()->json(['status' => 'success', 'message' => 'Código emitido']);
});