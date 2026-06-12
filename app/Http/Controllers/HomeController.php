<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        $tesoreria = null;
        if (Gate::allows('gestionar_tesoreria')) {
            $tesoreria = Tesoreria::where('estado', 1)->orderBy('id')->first();
        }

        $kpis = [
            'ventas_hoy' => Venta::whereDate('fecha_emision', today())
                ->where('estado_documento', '!=', 'ANULADA')
                ->sum('total'),
            'compras_hoy' => Compra::whereDate('fecha_emision', today())
                ->where('estado_documento', '!=', 'ANULADA')
                ->sum('total'),
            'ventas_mes' => Venta::whereMonth('fecha_emision', now()->month)
                ->whereYear('fecha_emision', now()->year)
                ->where('estado_documento', '!=', 'ANULADA')
                ->sum('total'),
            'compras_mes' => Compra::whereMonth('fecha_emision', now()->month)
                ->whereYear('fecha_emision', now()->year)
                ->where('estado_documento', '!=', 'ANULADA')
                ->sum('total'),
            'sesiones_activas' => SesionCaja::where('estado_sesion', 'ABIERTA')->count(),
            'productos_stock_bajo' => Producto::where('estado', 1)->get()->filter(fn ($producto) => $producto->stock_total <= 10)->count(),
        ];

        $ventasCompras = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $ventasCompras[] = [
                'fecha' => $fecha->format('d/m'),
                'ventas' => Venta::whereDate('fecha_emision', $fecha)
                    ->where('estado_documento', '!=', 'ANULADA')
                    ->sum('total'),
                'compras' => Compra::whereDate('fecha_emision', $fecha)
                    ->where('estado_documento', '!=', 'ANULADA')
                    ->sum('total'),
            ];
        }

        $ventasPorMetodoRaw = DB::table('pagos_venta')
            ->join('ventas', 'pagos_venta.venta_id', '=', 'ventas.id')
            ->where('ventas.estado_documento', '!=', 'ANULADA')
            ->selectRaw('pagos_venta.metodo_pago, SUM(pagos_venta.monto) as total')
            ->groupBy('pagos_venta.metodo_pago')
            ->pluck('total', 'metodo_pago');

        $comprasPorMetodoRaw = DB::table('compras')
            ->where('estado_documento', '!=', 'ANULADA')
            ->selectRaw('metodo_pago, SUM(total) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        $metodosVentas = ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'];
        $metodosCompras = ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'CREDITO', 'MIXTO'];

        $metodosPagoVentas = collect($metodosVentas)->map(function ($metodo) use ($ventasPorMetodoRaw) {
            return [
                'name' => ucfirst(strtolower($metodo)),
                'value' => (float) ($ventasPorMetodoRaw[$metodo] ?? 0),
            ];
        })->values()->all();

        $metodosPagoCompras = collect($metodosCompras)->map(function ($metodo) use ($comprasPorMetodoRaw) {
            return [
                'name' => ucfirst(strtolower($metodo)),
                'value' => (float) ($comprasPorMetodoRaw[$metodo] ?? 0),
            ];
        })->values()->all();

        $stockBajo = Producto::where('estado', 1)
            ->get()
            ->filter(fn ($producto) => $producto->stock_total <= 10)
            ->sortBy('stock_total')
            ->take(10)
            ->values();

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