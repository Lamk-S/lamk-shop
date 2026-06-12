<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Http\Requests\StoreAnulacionCompraRequest;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\ProductoVariante;
use App\Models\Proveedor;
use App\Services\CompraService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CompraController extends Controller implements HasMiddleware
{
    public function __construct(protected CompraService $compraService) { }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:registrar_compras|anular_compras', only: ['index', 'show']),
            new Middleware('permission:registrar_compras', only: ['create', 'store']),
            new Middleware('permission:anular_compras', only: ['destroy']),
        ];
    }

    public function index()
    {
        $compras = Compra::with([
                'comprobante',
                'proveedor.persona.documento',
                'detalles.productoVariante.producto.marca',
                'detalles.productoVariante.talla',
            ])
            ->withTrashed()
            ->latest('id')
            ->get();

        return view('compra.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::with('persona.documento')
            ->whereHas('persona', fn ($q) => $q->where('estado', 1))
            ->orderBy('id')
            ->get();

        $comprobantes = Comprobante::where('estado', 1)
            ->where('uso_comprobante', 'COMPRA')
            ->orderBy('serie')
            ->get();

        $variantes = ProductoVariante::with(['producto.marca', 'talla'])
            ->where('estado', 1)
            ->orderBy('id')
            ->get();

        $optionsMetodosPago = [
            'EFECTIVO' => 'Efectivo',
            'TARJETA' => 'Tarjeta',
            'TRANSFERENCIA' => 'Transferencia',
            'CREDITO' => 'Crédito',
        ];

        return view('compra.create', [
            'proveedores' => $proveedores,
            'comprobantes' => $comprobantes,
            'variantes' => $variantes,
            'productos' => $variantes,
            'optionsMetodosPago' => $optionsMetodosPago,
        ]);
    }

    public function store(StoreCompraRequest $request)
    {
        try {
            $compra = $this->compraService->registrar($request->validated(), $request->user());

            return redirect()
                ->route('compras.index')
                ->with('success', 'Compra registrada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar la compra: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function show(Compra $compra)
    {
        $compra->load([
            'comprobante',
            'proveedor.persona.documento',
            'detalles.productoVariante.producto.marca',
            'detalles.productoVariante.talla',
        ]);

        return view('compra.show', compact('compra'));
    }

    public function destroy(StoreAnulacionCompraRequest $request, Compra $compra)
    {
        try {
            $this->compraService->anular($compra, $request->validated()['motivo_anulacion'], $request->user());

            return redirect()
                ->route('compras.index')
                ->with('success', 'Compra anulada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al anular la compra: ' . $e->getMessage(),
            ]);
        }
    }
}