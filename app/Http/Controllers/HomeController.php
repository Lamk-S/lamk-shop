<?php

namespace App\Http\Controllers;

use App\Enums\EstadoDocumentoCompra;
use App\Enums\EstadoDocumentoVenta;
use App\Enums\EstadoSesion;
use App\Enums\MetodoPago;
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

        $hoy = today();
        $inicioMes = now()->startOfMonth();

        $stockBajoQuery = Producto::query()
            ->select('productos.id', 'productos.nombre', 'productos.codigo')
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN producto_variantes.estado = 1
                            AND producto_variantes.deleted_at IS NULL
                            THEN producto_variantes.stock_actual
                            ELSE 0
                        END
                    ),0
                ) as stock_total_calc
            ")
            ->leftJoin('producto_variantes', 'producto_variantes.producto_id', '=', 'productos.id')
            ->where('productos.estado', 1)
            ->whereNull('productos.deleted_at')
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
            ->having('stock_total_calc', '<=', 10);

        $conteoStockBajo = (clone $stockBajoQuery)->count();

        $kpis = [
            'ventas_hoy' => Venta::whereDate('fecha_emision', $hoy)->where('estado_documento', '!=', EstadoDocumentoVenta::ANULADA)->sum('total'),
            'compras_hoy' => Compra::whereDate('fecha_emision', $hoy)->where('estado_documento', '!=', EstadoDocumentoCompra::ANULADA)->sum('total'),
            'ventas_mes' => Venta::where('fecha_emision', '>=', $inicioMes)->where('estado_documento', '!=', EstadoDocumentoVenta::ANULADA)->sum('total'),
            'compras_mes' => Compra::where('fecha_emision', '>=', $inicioMes)->where('estado_documento', '!=', EstadoDocumentoCompra::ANULADA)->sum('total'),
            'sesiones_activas' => SesionCaja::where('estado_sesion', EstadoSesion::ABIERTA)->count(),
            'productos_stock_bajo' => $conteoStockBajo,
        ];

        $hace7Dias = Carbon::today()->subDays(6);
        
        $ventas7Dias = Venta::selectRaw('DATE(fecha_emision) as fecha, SUM(total) as total')
            ->where('fecha_emision', '>=', $hace7Dias)
            ->where('estado_documento', '!=', EstadoDocumentoVenta::ANULADA)
            ->groupBy('fecha')->pluck('total', 'fecha');

        $compras7Dias = Compra::selectRaw('DATE(fecha_emision) as fecha, SUM(total) as total')
            ->where('fecha_emision', '>=', $hace7Dias)
            ->where('estado_documento', '!=', EstadoDocumentoCompra::ANULADA)
            ->groupBy('fecha')->pluck('total', 'fecha');

        $ventasCompras = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = Carbon::today()->subDays($i);
            $dateString = $dateObj->toDateString();
            $ventasCompras[] = [
                'fecha' => $dateObj->format('d/m'),
                'ventas' => round($ventas7Dias[$dateString] ?? 0, 2),
                'compras' => round($compras7Dias[$dateString] ?? 0, 2),
            ];
        }

        $metodos = collect(MetodoPago::opciones())
            ->keys()
            ->reject(fn($m) => $m === MetodoPago::MIXTO->value)
            ->toArray();

        $ventasPorMetodoRaw = $this->calcularVentasPorMetodoRaw($metodos);
        $comprasPorMetodoRaw = $this->calcularComprasPorMetodo($metodos);

        $metodosPagoVentas = collect($metodos)
            ->map(fn($m) => ['name' => MetodoPago::opciones()[$m] ?? $m, 'value' => (float)($ventasPorMetodoRaw[$m] ?? 0)])
            ->filter(fn($item) => $item['value'] > 0)
            ->values()
            ->all();

        $metodosPagoCompras = collect($metodos)
            ->map(fn($m) => ['name' => MetodoPago::opciones()[$m] ?? $m, 'value' => (float)($comprasPorMetodoRaw[$m] ?? 0)])
            ->filter(fn($item) => $item['value'] > 0)
            ->values()
            ->all();

        $stockBajo = (clone $stockBajoQuery)
            ->orderBy('stock_total_calc')
            ->limit(10)
            ->get();

        return view('panel.index', compact(
            'tesoreriaEfectivo', 'tesoreriaBanco', 'kpis', 'ventasCompras', 
            'metodosPagoVentas', 'metodosPagoCompras', 'stockBajo'
        ));
    }

    private function calcularVentasPorMetodoRaw(array $metodos): array
    {
        $totales = array_fill_keys($metodos, 0.0);
        
        $raw = DB::table('pagos_venta')
            ->join('ventas', 'pagos_venta.venta_id', '=', 'ventas.id')
            ->where('pagos_venta.estado', 1)
            ->where('ventas.estado_documento', '!=', EstadoDocumentoVenta::ANULADA)
            ->selectRaw('pagos_venta.metodo_pago, SUM(pagos_venta.monto) as total')
            ->groupBy('pagos_venta.metodo_pago')
            ->pluck('total', 'metodo_pago');

        $totalVueltos = DB::table('ventas')
            ->where('estado_documento', '!=', EstadoDocumentoVenta::ANULADA)
            ->sum('vuelto_entregado');

        foreach ($metodos as $metodo) {
            $monto = (float) ($raw[$metodo] ?? 0);
            if ($metodo === MetodoPago::EFECTIVO->value) {
                $monto -= (float) $totalVueltos;
            }
            $totales[$metodo] = round(max($monto, 0), 2);
        }
        
        return $totales;
    }

    private function calcularComprasPorMetodo(array $metodos): array
    {
        $totales = array_fill_keys($metodos, 0.0);
        $raw = DB::table('pagos_compra')
            ->join('compras', 'pagos_compra.compra_id', '=', 'compras.id')
            ->where('pagos_compra.estado', 1)
            ->where('compras.estado_documento', '!=', EstadoDocumentoCompra::ANULADA)
            ->selectRaw('UPPER(pagos_compra.metodo_pago) as metodo_pago, SUM(pagos_compra.monto) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        foreach ($metodos as $metodo) {
            $totales[$metodo] = round((float) ($raw[$metodo] ?? 0), 2);
        }
        return $totales;
    }
}