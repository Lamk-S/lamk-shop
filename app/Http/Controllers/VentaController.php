<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Http\Requests\StoreAnulacionVentaRequest;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\ProductoVariante;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller implements HasMiddleware
{
    public function __construct(protected VentaService $ventaService) { }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:registrar_ventas|anular_ventas', only: ['index', 'show']),
            new Middleware('permission:registrar_ventas', only: ['create', 'store']),
            new Middleware('permission:anular_ventas', only: ['destroy']),
        ];
    }

    public function index()
    {
        $ventas = Venta::with([
                'comprobante',
                'cliente.persona.documento',
                'user',
                'sesionCaja.caja',
                'detalles.productoVariante.producto.marca',
                'detalles.productoVariante.talla',
                'pagos',
            ])
            ->latest('id')
            ->get();

        return view('venta.index', compact('ventas'));
    }

    public function create()
    {
        $sesionAbierta = SesionCaja::where('user_id', Auth::id())
            ->where('estado_sesion', 'ABIERTA')
            ->with('caja', 'user')
            ->first();

        if (!$sesionAbierta) {
            return redirect()
                ->route('sesiones-caja.index')
                ->with('warning', 'Debes abrir una sesión de caja antes de registrar una venta.');
        }

        $variantes = ProductoVariante::with(['producto.marca', 'talla'])
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->where('stock_actual', '>', 0)
            ->orderBy('id')
            ->get();

        $clientes = Cliente::with('persona.documento')
            ->whereHas('persona', fn ($q) => $q->where('estado', 1))
            ->orderBy('id')
            ->get();

        $comprobantes = Comprobante::where('estado', 1)
            ->where('uso_comprobante', 'VENTA')
            ->orderBy('serie')
            ->get();

        $optionsMetodosPago = [
            'EFECTIVO' => 'Efectivo',
            'TARJETA' => 'Tarjeta',
            'TRANSFERENCIA' => 'Transferencia',
            'YAPE' => 'Yape',
            'PLIN' => 'Plin',
            'OTRO' => 'Otro',
        ];

        return view('venta.create', [
            'variantes' => $variantes,
            'productos' => $variantes,
            'clientes' => $clientes,
            'comprobantes' => $comprobantes,
            'sesionAbierta' => $sesionAbierta,
            'optionsMetodosPago' => $optionsMetodosPago,
        ]);
    }

    public function store(StoreVentaRequest $request)
    {
        try {
            $venta = $this->ventaService->registrar($request->validated(), $request->user());

            return redirect()
                ->route('ventas.index')
                ->with('success', 'Venta registrada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar la venta: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load([
            'comprobante',
            'cliente.persona.documento',
            'user',
            'sesionCaja.caja',
            'detalles.productoVariante.producto.marca',
            'detalles.productoVariante.talla',
            'pagos',
        ]);

        return view('venta.show', compact('venta'));
    }

    public function destroy(StoreAnulacionVentaRequest $request, Venta $venta)
    {
        try {
            $this->ventaService->anular($venta, $request->validated()['motivo_anulacion'], $request->user());

            return redirect()
                ->route('ventas.index')
                ->with('success', 'Venta anulada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al anular la venta: ' . $e->getMessage(),
            ]);
        }
    }
}