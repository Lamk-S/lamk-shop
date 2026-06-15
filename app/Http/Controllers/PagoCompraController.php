<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoCompraRequest;
use App\Models\CuentaPorPagar;
use App\Models\Proveedor;
use App\Services\CuentasPorPagarService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PagoCompraController extends Controller implements HasMiddleware
{
    public function __construct(protected CuentasPorPagarService $cuentasPorPagarService) { }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_tesoreria|registrar_compras', only: ['index', 'store']),
        ];
    }

    public function index(Request $request)
    {
        $query = CuentaPorPagar::with([
                'compra.comprobante',
                'compra.detalles.productoVariante.producto.marca',
                'compra.detalles.productoVariante.talla',
                'proveedor.persona.documento',
                'user',
                'pagos.user',
            ])
            ->latest('id');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->boolean('vencidas')) {
            $query->whereIn('estado', ['PENDIENTE', 'PARCIAL'])
                ->whereDate('fecha_vencimiento', '<', today());
        }

        $cuentas = $query->get();
        $proveedores = Proveedor::with('persona.documento')
            ->whereHas('persona', fn ($q) => $q->where('estado', 1))
            ->orderBy('id')
            ->get();

        return view('pago_compra.index', compact('cuentas', 'proveedores'));
    }

    public function store(StorePagoCompraRequest $request, CuentaPorPagar $cuenta_por_pagar)
    {
        $data = $request->validated();

        try {
            $this->cuentasPorPagarService->registrarPago(
                $cuenta_por_pagar,
                [
                    'metodo_pago' => $data['metodo_pago'],
                    'monto' => (float) $data['monto'],
                    'referencia_operacion' => $data['referencia_operacion'] ?? null,
                    'observacion' => $data['observacion'] ?? null,
                ],
                $request->user()
            );

            return redirect()
                ->route('cuentas-por-pagar.index')
                ->with('success', 'Pago de compra registrado correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors([
                    'error' => 'Error al registrar el pago: ' . $e->getMessage(),
                ])
                ->withInput();
        }
    }
}