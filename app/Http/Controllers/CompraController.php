<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\CompraProducto;
use App\Models\Comprobante;
use App\Models\EmpresaConfiguracion;
use App\Models\Kardex;
use App\Models\MovimientoTesoreria;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Proveedor;
use App\Models\Tesoreria;
use App\Models\Talla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller implements HasMiddleware
{
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
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $optionsMetodosPago = [
            'EFECTIVO' => 'Efectivo',
            'TARJETA' => 'Tarjeta',
            'TRANSFERENCIA' => 'Transferencia',
            'CREDITO' => 'Crédito',
        ];

        return view('compra.create', compact('proveedores', 'comprobantes', 'variantes', 'optionsMetodosPago'));
    }

    public function store(StoreCompraRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $proveedor = Proveedor::with(['persona.documento'])
                    ->whereKey($data['proveedor_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $empresa = EmpresaConfiguracion::first();
                $igv = (float) ($empresa?->igv_porcentaje ?? 18);

                $comprobante = null;
                $serie = null;
                $correlativo = null;
                $tipoComprobante = null;

                if (!empty($data['comprobante_id'])) {
                    $comprobante = Comprobante::whereKey($data['comprobante_id'])
                        ->where('uso_comprobante', 'COMPRA')
                        ->where('estado', 1)
                        ->lockForUpdate()
                        ->firstOrFail();

                    [$tipoComprobante, $serie, $correlativo] = $this->generarDatosComprobante($comprobante);
                }

                $lineas = [];
                $subtotal = 0.0;
                $descuentoTotal = 0.0;
                $impuestoTotal = 0.0;
                $total = 0.0;

                foreach ($data['detalles'] as $row) {
                    $variante = ProductoVariante::with(['producto', 'talla'])
                        ->whereKey($row['producto_variante_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $producto = Producto::whereKey($variante->producto_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $cantidad = (int) $row['cantidad'];
                    $costoUnitario = (float) $row['costo_unitario'];
                    $descuento = (float) ($row['descuento'] ?? 0);

                    $base = round(($cantidad * $costoUnitario) - $descuento, 2);
                    $impuesto = $producto->afecto_igv ? round($base * $igv / 100, 2) : 0.00;
                    $lineaTotal = round($base + $impuesto, 2);

                    $lineas[] = [
                        'variante_id' => $variante->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'costo_unitario' => $costoUnitario,
                        'descuento' => $descuento,
                        'subtotal' => round($cantidad * $costoUnitario, 2),
                        'impuesto' => $impuesto,
                        'total' => $lineaTotal,
                        'producto_codigo' => $producto->codigo,
                        'producto_nombre' => $producto->nombre,
                        'talla_codigo' => $variante->talla->codigo,
                        'talla_nombre' => $variante->talla->nombre,
                        'precio_venta' => (float) ($row['precio_venta'] ?? 0),
                    ];

                    $subtotal += round($cantidad * $costoUnitario, 2);
                    $descuentoTotal += $descuento;
                    $impuestoTotal += $impuesto;
                    $total += $lineaTotal;
                }

                $compra = Compra::create([
                    'proveedor_id' => $proveedor->id,
                    'user_id' => Auth::id(),
                    'comprobante_id' => $comprobante?->id,
                    'tipo_comprobante' => $tipoComprobante,
                    'serie' => $serie,
                    'correlativo' => $correlativo,
                    'proveedor_tipo_documento' => $proveedor->persona?->documento?->codigo,
                    'proveedor_numero_documento' => $proveedor->persona?->numero_documento,
                    'proveedor_nombre' => $proveedor->persona?->razon_social
                        ?? trim(($proveedor->persona?->nombres ?? '') . ' ' . ($proveedor->persona?->apellidos ?? '')),
                    'proveedor_direccion' => $proveedor->persona?->direccion,
                    'metodo_pago' => $data['metodo_pago'],
                    'moneda' => $data['moneda'] ?? 'PEN',
                    'fecha_emision' => $data['fecha_emision'] ?? now(),
                    'subtotal' => round($subtotal, 2),
                    'descuento_total' => round($descuentoTotal, 2),
                    'impuesto_total' => round($impuestoTotal, 2),
                    'total' => round($total, 2),
                    'estado_documento' => 'RECEPCIONADA',
                    'observacion' => $data['observacion'] ?? null,
                    'motivo_anulacion' => null,
                    'anulado_at' => null,
                ]);

                foreach ($lineas as $linea) {
                    $variante = ProductoVariante::whereKey($linea['variante_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $producto = Producto::whereKey($linea['producto_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $saldoAnterior = (int) $variante->stock_actual;
                    $saldoPosterior = $saldoAnterior + $linea['cantidad'];

                    $variante->update([
                        'stock_actual' => $saldoPosterior,
                        'stock_minimo' => $variante->stock_minimo,
                    ]);

                    CompraProducto::create([
                        'compra_id' => $compra->id,
                        'producto_variante_id' => $variante->id,
                        'cantidad' => $linea['cantidad'],
                        'costo_unitario' => $linea['costo_unitario'],
                        'descuento' => $linea['descuento'],
                        'subtotal' => $linea['subtotal'],
                        'impuesto' => $linea['impuesto'],
                        'total' => $linea['total'],
                        'producto_codigo' => $linea['producto_codigo'],
                        'producto_nombre' => $linea['producto_nombre'],
                        'talla_codigo' => $linea['talla_codigo'],
                        'talla_nombre' => $linea['talla_nombre'],
                    ]);

                    $producto->update([
                        'precio_compra' => $linea['costo_unitario'],
                        'precio_venta' => $linea['precio_venta'] > 0 ? $linea['precio_venta'] : $producto->precio_venta,
                        'stock_total' => $producto->variantes()->where('estado', 1)->sum('stock_actual'),
                    ]);

                    Kardex::create([
                        'producto_variante_id' => $variante->id,
                        'tipo_transaccion' => 'COMPRA',
                        'origen_type' => Compra::class,
                        'origen_id' => $compra->id,
                        'descripcion' => 'Compra #' . $compra->id,
                        'entrada' => $linea['cantidad'],
                        'salida' => 0,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                        'costo_unitario' => $linea['costo_unitario'],
                        'costo_total' => round($linea['cantidad'] * $linea['costo_unitario'], 2),
                        'user_id' => Auth::id(),
                    ]);
                }

                if (in_array($data['metodo_pago'], ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA'], true)) {
                    $tesoreriaCodigo = $data['metodo_pago'] === 'EFECTIVO' ? 'TES-EFECTIVO' : 'TES-BANCO';
                    $tesoreria = Tesoreria::where('codigo', $tesoreriaCodigo)->lockForUpdate()->first();

                    if (!$tesoreria) {
                        $tesoreria = Tesoreria::create([
                            'codigo' => $tesoreriaCodigo,
                            'nombre' => $data['metodo_pago'] === 'EFECTIVO' ? 'Caja General' : 'Banco Principal',
                            'tipo_cuenta' => $data['metodo_pago'] === 'EFECTIVO' ? 'EFECTIVO' : 'BANCO',
                            'saldo_actual' => 0,
                            'estado' => 1,
                        ]);
                    }

                    $saldoAnterior = (float) $tesoreria->saldo_actual;
                    $saldoPosterior = round($saldoAnterior - $compra->total, 2);

                    if ($saldoPosterior < 0) {
                        throw new Exception('Saldo insuficiente en tesorería para registrar la compra.');
                    }

                    $tesoreria->update([
                        'saldo_actual' => $saldoPosterior,
                    ]);

                    MovimientoTesoreria::create([
                        'tesoreria_id' => $tesoreria->id,
                        'user_id' => Auth::id(),
                        'sesion_caja_id' => null,
                        'venta_id' => null,
                        'compra_id' => $compra->id,
                        'tipo' => 'EGRESO',
                        'medio' => $data['metodo_pago'] === 'EFECTIVO' ? 'EFECTIVO' : 'BANCO',
                        'origen' => 'COMPRA_PRODUCTO',
                        'descripcion' => 'Pago de compra #' . $compra->id,
                        'monto' => $compra->total,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                        'numero_operacion' => null,
                        'referencia' => null,
                    ]);
                } elseif ($data['metodo_pago'] === 'CREDITO') {
                    // No hay movimiento de tesorería en compras a crédito.
                } else {
                    throw new ValidationException(validator([], []));
                }
            });

            return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la compra: ' . $e->getMessage()])->withInput();
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

    public function destroy(string $id)
    {
        return app(AnulacionCompraController::class)->destroy(request(), $id);
    }

    private function generarDatosComprobante(Comprobante $comprobante): array
    {
        $nuevoCorrelativo = (int) $comprobante->correlativo_actual + 1;
        $comprobante->update(['correlativo_actual' => $nuevoCorrelativo]);

        return [
            $comprobante->tipo_comprobante,
            $comprobante->serie,
            str_pad((string) $nuevoCorrelativo, 8, '0', STR_PAD_LEFT),
        ];
    }
}