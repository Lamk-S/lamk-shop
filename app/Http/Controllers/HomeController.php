<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        $tesoreria = null;

        if (Gate::allows('ver-tesoreria')) {
            $tesoreria = Tesoreria::first();
        }

        $kpis = [
            'ventas_hoy' => Venta::whereDate('created_at', today())->where('estado', 1)->sum('total'),
            'compras_hoy' => Compra::whereDate('created_at', today())->where('estado', 1)->sum('total'),
            'ventas_mes' => Venta::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('estado', 1)->sum('total'),
            'compras_mes' => Compra::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('estado', 1)->sum('total'),
            'sesiones_activas' => SesionCaja::where('estado', 1)->count(),
            'productos_stock_bajo' => Producto::where('estado', 1)->where('stock', '<=', 10)->count(),
        ];

        $ventasCompras = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);

            $ventasCompras[] = [
                'fecha' => $fecha->format('d/m'),
                'ventas' => Venta::whereDate('created_at', $fecha)->where('estado', 1)->sum('total'),
                'compras' => Compra::whereDate('created_at', $fecha)->where('estado', 1)->sum('total'),
            ];
        }

        $ventasPorMetodoRaw = DB::table('pagos_venta')
            ->join('ventas', 'pagos_venta.venta_id', '=', 'ventas.id')
            ->where('ventas.estado', 1)
            ->selectRaw('pagos_venta.metodo_pago, SUM(pagos_venta.monto) as total')
            ->groupBy('pagos_venta.metodo_pago')
            ->pluck('total', 'metodo_pago');

        $comprasPorMetodoRaw = DB::table('compras')
            ->where('estado', 1)
            ->selectRaw('metodo_pago, SUM(total) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        $metodos = ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA'];

        $metodosPagoVentas = collect($metodos)->map(function ($metodo) use ($ventasPorMetodoRaw) {
            return [
                'name' => ucfirst(strtolower($metodo)),
                'value' => (float) ($ventasPorMetodoRaw[$metodo] ?? 0),
            ];
        })->values()->all();

        $metodosPagoCompras = collect($metodos)->map(function ($metodo) use ($comprasPorMetodoRaw) {
            return [
                'name' => ucfirst(strtolower($metodo)),
                'value' => (float) ($comprasPorMetodoRaw[$metodo] ?? 0),
            ];
        })->values()->all();

        $stockBajo = Producto::select('id', 'nombre', 'stock')
            ->where('estado', 1)
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->limit(10)
            ->get();

        return view('panel.index', compact(
            'tesoreria',
            'kpis',
            'ventasCompras',
            'metodosPagoVentas',
            'metodosPagoCompras',
            'stockBajo'
        ));
    }
}