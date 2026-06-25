<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::post('/scanner/receive', function (Request $request) {
    $data = $request->validate([
        'codigo' => ['required', 'string', 'max:255'],
    ]);

    Cache::put('pos.scanner.latest', $data['codigo'], now()->addSeconds(15));

    return response()->json(['ok' => true]);
});

Route::get('/scanner/pull', function () {
    return response()->json([
        'codigo' => Cache::pull('pos.scanner.latest'),
    ]);
});