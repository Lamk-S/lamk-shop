<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        $tesoreria = Tesoreria::firstOrCreate(
            ['nombre' => 'Tesorería Principal'],
            [
                'saldo_efectivo' => 0,
                'saldo_banco' => 0,
                'estado' => 1,
            ]
        );

        return view('panel.index', [
            'tesoreria' => $tesoreria,
        ]);
    }
}