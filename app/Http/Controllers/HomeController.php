<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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

        $tesoreriaEfectivo = null;
        $tesoreriaBanco = null;

        if (Gate::allows('gestionar_tesoreria')) {
            $tesorerias = Tesoreria::whereIn('codigo', ['TES-EFECTIVO', 'TES-BANCO'])
                ->where('estado', 1)
                ->get()
                ->keyBy('codigo');
                
            $tesoreriaEfectivo = $tesorerias->get('TES-EFECTIVO');
            $tesoreriaBanco = $tesorerias->get('TES-BANCO');
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

            'productos_stock_bajo' => Producto::where('estado', 1)
                ->whereHas('variantes', function($q) {
                    $q->where('estado', 1)
                      ->havingRaw('SUM(stock_actual) <= 10');
                })->count(),
        ];

        $ventasCompras = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $ventasCompras[] = [
                'fecha' => $fecha->format('d/m'),
                'ventas' => Venta::whereDate('fecha_emision', $fecha)->where('estado_documento', '!=', 'ANULADA')->sum('total'),
                'compras' => Compra::whereDate('fecha_emision', $fecha)->where('estado_documento', '!=', 'ANULADA')->sum('total'),
            ];
        }

        $metodosVentas = ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'];
        $metodosCompras = ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'CREDITO', 'MIXTO'];

        $ventasMetodo = Venta::select(['id', 'total', 'vuelto_entregado'])
            ->where('estado_documento', '!=', 'ANULADA')
            ->with(['pagos' => fn ($q) => $q->select('id', 'venta_id', 'metodo_pago', 'monto', 'estado')->where('estado', 1)])
            ->get();

        $ventasPorMetodoRaw = $this->calcularVentasPorMetodo($ventasMetodo, $metodosVentas);
        $comprasPorMetodoRaw = $this->calcularComprasPorMetodo($metodosCompras);

        $metodosPagoVentas = collect($metodosVentas)->map(fn($m) => ['name' => ucfirst(strtolower($m)), 'value' => (float)($ventasPorMetodoRaw[$m] ?? 0)])->values()->all();
        $metodosPagoCompras = collect($metodosCompras)->map(fn($m) => ['name' => ucfirst(strtolower($m)), 'value' => (float)($comprasPorMetodoRaw[$m] ?? 0)])->values()->all();

        $stockBajo = Producto::select('productos.id', 'productos.nombre')
            ->selectRaw('COALESCE(SUM(CASE WHEN producto_variantes.estado = 1 AND producto_variantes.deleted_at IS NULL THEN producto_variantes.stock_actual ELSE 0 END), 0) as stock_total_calc')
            ->leftJoin('producto_variantes', 'producto_variantes.producto_id', '=', 'productos.id')
            ->where('productos.estado', 1)
            ->groupBy('productos.id', 'productos.nombre')
            ->havingRaw('COALESCE(SUM(CASE WHEN producto_variantes.estado = 1 AND producto_variantes.deleted_at IS NULL THEN producto_variantes.stock_actual ELSE 0 END), 0) <= ?', [10])
            ->orderBy('stock_total_calc')
            ->limit(10)
            ->get();

        return view('panel.index', compact(
            'tesoreriaEfectivo', 'tesoreriaBanco', 'kpis', 'ventasCompras', 
            'metodosPagoVentas', 'metodosPagoCompras', 'stockBajo'
        ));
    }

    private function calcularVentasPorMetodo(Collection $ventas, array $metodos): array
    {
        $totales = array_fill_keys($metodos, 0.0);

        foreach ($ventas as $venta) {
            $ventaTotal = round((float) ($venta->total ?? 0), 2);
            if ($ventaTotal <= 0) continue;

            $pagos = $venta->pagos->filter(fn($pago) => in_array(strtoupper((string) $pago->metodo_pago), $metodos, true) && (float) $pago->monto > 0)->values();
            if ($pagos->isEmpty()) continue;

            $esEfectivoConVuelto = $pagos->count() === 1 && strtoupper((string) $pagos->first()->metodo_pago) === 'EFECTIVO' && (float) ($venta->vuelto_entregado ?? 0) > 0;

            if ($esEfectivoConVuelto) {
                $totales['EFECTIVO'] = round($totales['EFECTIVO'] + $ventaTotal, 2);
                continue;
            }

            foreach ($pagos as $pago) {
                $metodo = strtoupper((string) $pago->metodo_pago);
                if (array_key_exists($metodo, $totales)) {
                    $totales[$metodo] = round($totales[$metodo] + (float) $pago->monto, 2);
                }
            }
        }
        return $totales;
    }

    private function calcularComprasPorMetodo(array $metodos): array
    {
        $totales = array_fill_keys($metodos, 0.0);
        $raw = DB::table('pagos_compra')
            ->join('compras', 'pagos_compra.compra_id', '=', 'compras.id')
            ->where('pagos_compra.estado', 1)
            ->where('compras.estado_documento', '!=', 'ANULADA')
            ->selectRaw('UPPER(pagos_compra.metodo_pago) as metodo_pago, SUM(pagos_compra.monto) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        foreach ($metodos as $metodo) {
            $totales[$metodo] = round((float) ($raw[$metodo] ?? 0), 2);
        }
        return $totales;
    }
}