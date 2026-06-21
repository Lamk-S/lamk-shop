<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnulacionCompraRequest;
use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Documento;
use App\Models\ProductoVariante;
use App\Models\Proveedor;
use App\Services\CompraService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CompraController extends Controller implements HasMiddleware
{
    public function __construct(protected CompraService $compraService) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:registrar_compras|anular_compras', only: ['index', 'show']),
            new Middleware('permission:registrar_compras', only: ['create', 'store']),
            new Middleware('permission:anular_compras', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Compra::with([
            'comprobante',
            'proveedor.persona.documento',
            'user:id,name',
        ])
        ->withTrashed()
        ->latest('id');

        $query->when($request->filled('proveedor_id'), fn($q) => $q->where('proveedor_id', $request->proveedor_id))
              ->when($request->filled('estado_documento'), fn($q) => $q->where('estado_documento', $request->estado_documento))
              ->when($request->filled('estado_pago'), fn($q) => $q->where('estado_pago', $request->estado_pago))
              ->when($request->filled('metodo_pago'), fn($q) => $q->where('metodo_pago', $request->metodo_pago))
              ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('fecha_emision', '>=', $request->fecha_desde))
              ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('fecha_emision', '<=', $request->fecha_hasta));

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $compras = $query->paginate($perPage)->withQueryString();

        $proveedores = Proveedor::with('persona.documento')
            ->whereHas('persona', fn($q) => $q->where('estado', 1))
            ->orderBy('id', 'desc')
            ->get();

        $optionsEstadoDocumento = [
            'REGISTRADA' => 'Registrada',
            'RECEPCIONADA' => 'Recepcionada',
            'ANULADA' => 'Anulada',
            'PENDIENTE' => 'Pendiente',
        ];

        $optionsEstadoPago = [
            'PENDIENTE' => 'Pendiente',
            'PARCIAL' => 'Parcial',
            'PAGADA' => 'Pagada',
            'ANULADA' => 'Anulada',
        ];

        $optionsMetodoPago = [
            'EFECTIVO' => 'Efectivo',
            'TARJETA' => 'Tarjeta',
            'TRANSFERENCIA' => 'Transferencia',
            'CREDITO' => 'Crédito',
            'MIXTO' => 'Mixto',
        ];

        return view('compra.index', compact(
            'compras', 'proveedores', 'optionsEstadoDocumento',
            'optionsEstadoPago', 'optionsMetodoPago', 'perPage'
        ));
    }

    public function create()
    {
        $proveedores = Proveedor::with('persona.documento')
            ->whereHas('persona', fn($q) => $q->where('estado', 1))
            ->orderBy('id')
            ->get();

        $comprobantes = Comprobante::where('estado', 1)
            ->where('uso_comprobante', 'COMPRA')
            ->orderBy('serie')
            ->get();

        $variantes = ProductoVariante::with(['producto.marca', 'talla'])
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $documentos = Documento::where('estado', 1)
            ->orderBy('codigo')
            ->get();

        $optionsMetodosPago = [
            'EFECTIVO' => 'Efectivo',
            'TARJETA' => 'Tarjeta',
            'TRANSFERENCIA' => 'Transferencia',
            'CREDITO' => 'Crédito',
            'MIXTO' => 'Mixto',
        ];

        return view('compra.create', [
            'proveedores' => $proveedores,
            'documentos' => $documentos,
            'comprobantes' => $comprobantes,
            'variantes' => $variantes,
            'productos' => $variantes,
            'optionsMetodosPago' => $optionsMetodosPago,
        ]);
    }

    public function store(StoreCompraRequest $request)
    {
        try {
            $this->compraService->registrar($request->validated(), $request->user(), $request);

            return redirect()
                ->route('compras.index')
                ->with('success', 'Compra registrada correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withErrors([
                    'error' => 'Error al registrar la compra: ' . $e->getMessage(),
                ])
                ->withInput();
        }
    }

    public function show(Compra $compra)
    {
        $compra->load([
            'comprobante',
            'proveedor.persona.documento',
            'detalles.productoVariante.producto.marca',
            'detalles.productoVariante.talla',
            'cuentaPorPagar.pagos.user',
            'movimientosTesoreria.tesoreria',
            'user',
        ]);

        return view('compra.show', compact('compra'));
    }

    public function destroy(StoreAnulacionCompraRequest $request, Compra $compra)
    {
        try {
            $this->compraService->anular(
                $compra,
                $request->validated()['motivo_anulacion'],
                $request->user(),
                $request
            );

            return redirect()
                ->route('compras.index')
                ->with('success', 'Compra anulada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al anular la compra: ' . $e->getMessage(),
            ]);
        }
    }
}
