<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\EmpresaConfiguracion;
use App\Models\Kardex;
use App\Models\MovimientoCaja;
use App\Models\MovimientoTesoreria;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\ProductoVariante;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller implements HasMiddleware
{
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

        return view('venta.create', compact('variantes', 'clientes', 'comprobantes', 'sesionAbierta', 'optionsMetodosPago'));
    }

    public function store(StoreVentaRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $request) {
                $sesionAbierta = SesionCaja::where('user_id', Auth::id())
                    ->where('estado_sesion', 'ABIERTA')
                    ->lockForUpdate()
                    ->firstOrFail();

                $empresa = EmpresaConfiguracion::first();
                $igv = (float) ($empresa?->igv_porcentaje ?? 18);

                $cliente = null;
                if (!empty($data['cliente_id'])) {
                    $cliente = Cliente::with(['persona.documento'])
                        ->whereKey($data['cliente_id'])
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                $comprobante = null;
                $tipoComprobante = null;
                $serie = null;
                $correlativo = null;

                if (!empty($data['comprobante_id'])) {
                    $comprobante = Comprobante::whereKey($data['comprobante_id'])
                        ->where('uso_comprobante', 'VENTA')
                        ->where('estado', 1)
                        ->lockForUpdate()
                        ->firstOrFail();

                    [$tipoComprobante, $serie, $correlativo] = $this->generarDatosComprobante($comprobante);
                } else {
                    $ticket = Comprobante::where('uso_comprobante', 'VENTA')
                        ->where('tipo_comprobante', 'TICKET')
                        ->where('estado', 1)
                        ->lockForUpdate()
                        ->first();

                    if ($ticket) {
                        $comprobante = $ticket;
                        [$tipoComprobante, $serie, $correlativo] = $this->generarDatosComprobante($ticket);
                    }
                }

                if ($tipoComprobante === 'FACTURA' && !$cliente) {
                    throw ValidationException::withMessages([
                        'cliente_id' => 'La factura requiere un cliente registrado.',
                    ]);
                }

                if ($tipoComprobante === 'FACTURA' && $cliente && $cliente->persona?->documento?->codigo !== 'RUC') {
                    throw ValidationException::withMessages([
                        'cliente_id' => 'La factura solo puede emitirse a un cliente con RUC.',
                    ]);
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
                    $precioUnitario = (float) $row['precio_unitario'];
                    $descuento = (float) ($row['descuento'] ?? 0);

                    if ((int) $variante->stock_actual < $cantidad) {
                        throw new Exception("Stock insuficiente para el producto {$producto->nombre} - {$variante->talla->nombre}");
                    }

                    $base = round(($cantidad * $precioUnitario) - $descuento, 2);
                    $impuesto = $producto->afecto_igv ? round($base * $igv / 100, 2) : 0.00;
                    $lineaTotal = round($base + $impuesto, 2);

                    $lineas[] = [
                        'variante_id' => $variante->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'descuento' => $descuento,
                        'subtotal' => round($cantidad * $precioUnitario, 2),
                        'impuesto' => $impuesto,
                        'total' => $lineaTotal,
                        'producto_codigo' => $producto->codigo,
                        'producto_nombre' => $producto->nombre,
                        'talla_codigo' => $variante->talla->codigo,
                        'talla_nombre' => $variante->talla->nombre,
                    ];

                    $subtotal += round($cantidad * $precioUnitario, 2);
                    $descuentoTotal += $descuento;
                    $impuestoTotal += $impuesto;
                    $total += $lineaTotal;
                }

                $paymentRows = collect($data['pagos'] ?? []);

                if ($paymentRows->isEmpty()) {
                    if (empty($data['metodo_pago'])) {
                        throw ValidationException::withMessages([
                            'metodo_pago' => 'Debes registrar al menos un método de pago.',
                        ]);
                    }

                    $paymentRows = collect([
                        [
                            'metodo_pago' => $data['metodo_pago'],
                            'monto' => (float) ($data['monto_recibido'] ?? $total),
                            'referencia_operacion' => $data['referencia_operacion'] ?? null,
                        ],
                    ]);
                }

                $pagosTotal = round($paymentRows->sum('monto'), 2);

                if ($pagosTotal < $total) {
                    throw ValidationException::withMessages([
                        'pagos' => 'El total de pagos no cubre el total de la venta.',
                    ]);
                }

                $soloUnPagoEfectivo = $paymentRows->count() === 1 && strtoupper($paymentRows->first()['metodo_pago']) === 'EFECTIVO';
                $vueltoEntregado = $soloUnPagoEfectivo
                    ? round(max(0, (float) $paymentRows->first()['monto'] - $total), 2)
                    : 0.00;

                $venta = Venta::create([
                    'cliente_id' => $cliente?->id,
                    'user_id' => Auth::id(),
                    'sesion_caja_id' => $sesionAbierta->id,
                    'comprobante_id' => $comprobante?->id,
                    'tipo_comprobante' => $tipoComprobante,
                    'serie' => $serie,
                    'correlativo' => $correlativo,
                    'cliente_tipo_documento' => $cliente?->persona?->documento?->codigo,
                    'cliente_numero_documento' => $cliente?->persona?->numero_documento,
                    'cliente_nombre' => $cliente
                        ? (
                            $cliente->persona?->razon_social
                            ?? trim(($cliente->persona?->nombres ?? '') . ' ' . ($cliente->persona?->apellidos ?? ''))
                        )
                        : 'CONSUMIDOR FINAL',
                    'cliente_direccion' => $cliente?->persona?->direccion,
                    'cliente_email' => $cliente?->persona?->email,
                    'moneda' => $data['moneda'] ?? 'PEN',
                    'fecha_emision' => $data['fecha_emision'] ?? now(),
                    'subtotal' => round($subtotal, 2),
                    'descuento_total' => round($descuentoTotal, 2),
                    'impuesto_total' => round($impuestoTotal, 2),
                    'total' => round($total, 2),
                    'monto_recibido' => $pagosTotal,
                    'vuelto_entregado' => $vueltoEntregado,
                    'estado_documento' => 'EMITIDA',
                    'observacion' => $data['observacion'] ?? null,
                    'motivo_anulacion' => null,
                    'anulado_at' => null,
                    'sunat_estado' => 'SIMULADO',
                    'sunat_mensaje' => 'Documento generado internamente',
                    'xml_path' => null,
                    'pdf_path' => null,
                    'qr_payload' => null,
                    'hash_resumen' => null,
                ]);

                foreach ($lineas as $linea) {
                    $variante = ProductoVariante::whereKey($linea['variante_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $producto = Producto::whereKey($linea['producto_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($variante->stock_actual < $linea['cantidad']) {
                        throw new Exception("Stock insuficiente para el producto {$producto->nombre} - {$linea['talla_nombre']}");
                    }

                    $saldoAnterior = (int) $variante->stock_actual;
                    $saldoPosterior = $saldoAnterior - $linea['cantidad'];

                    $variante->update([
                        'stock_actual' => $saldoPosterior,
                    ]);

                    ProductoVenta::create([
                        'venta_id' => $venta->id,
                        'producto_variante_id' => $variante->id,
                        'cantidad' => $linea['cantidad'],
                        'precio_unitario' => $linea['precio_unitario'],
                        'descuento' => $linea['descuento'],
                        'subtotal' => $linea['subtotal'],
                        'impuesto' => $linea['impuesto'],
                        'total' => $linea['total'],
                        'costo_unitario' => $producto->precio_compra,
                        'producto_codigo' => $linea['producto_codigo'],
                        'producto_nombre' => $linea['producto_nombre'],
                        'talla_codigo' => $linea['talla_codigo'],
                        'talla_nombre' => $linea['talla_nombre'],
                    ]);

                    $producto->update([
                        'stock_total' => $producto->variantes()->where('estado', 1)->sum('stock_actual'),
                    ]);

                    Kardex::create([
                        'producto_variante_id' => $variante->id,
                        'tipo_transaccion' => 'VENTA',
                        'origen_type' => Venta::class,
                        'origen_id' => $venta->id,
                        'descripcion' => 'Venta #' . $venta->id,
                        'entrada' => 0,
                        'salida' => $linea['cantidad'],
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                        'costo_unitario' => $producto->precio_compra,
                        'costo_total' => round($linea['cantidad'] * $producto->precio_compra, 2),
                        'user_id' => Auth::id(),
                    ]);
                }

                foreach ($paymentRows as $payment) {
                    $metodoPago = strtoupper($payment['metodo_pago']);
                    $montoPago = (float) $payment['monto'];
                    $referencia = $payment['referencia_operacion'] ?? null;

                    $venta->pagos()->create([
                        'metodo_pago' => $metodoPago,
                        'monto' => $montoPago,
                        'referencia_operacion' => $referencia,
                        'moneda' => $data['moneda'] ?? 'PEN',
                        'estado' => 1,
                    ]);

                    if ($metodoPago === 'EFECTIVO') {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesionAbierta->id,
                            'tipo' => 'INGRESO',
                            'origen' => 'VENTA',
                            'descripcion' => 'Cobro de venta #' . $venta->id,
                            'monto' => $montoPago,
                            'referencia_type' => Venta::class,
                            'referencia_id' => $venta->id,
                        ]);

                        if ($soloUnPagoEfectivo && $vueltoEntregado > 0) {
                            MovimientoCaja::create([
                                'sesion_caja_id' => $sesionAbierta->id,
                                'tipo' => 'EGRESO',
                                'origen' => 'VENTA',
                                'descripcion' => 'Vuelto de venta #' . $venta->id,
                                'monto' => $vueltoEntregado,
                                'referencia_type' => Venta::class,
                                'referencia_id' => $venta->id,
                            ]);
                        }
                    } else {
                        $tesoreriaCodigo = in_array($metodoPago, ['TARJETA'], true) ? 'TES-BANCO' : 'TES-BANCO';
                        $tesoreria = Tesoreria::where('codigo', $tesoreriaCodigo)->lockForUpdate()->first();

                        if (!$tesoreria) {
                            $tesoreria = Tesoreria::create([
                                'codigo' => $tesoreriaCodigo,
                                'nombre' => 'Banco Principal',
                                'tipo_cuenta' => 'BANCO',
                                'saldo_actual' => 0,
                                'estado' => 1,
                            ]);
                        }

                        $saldoAnterior = (float) $tesoreria->saldo_actual;
                        $saldoPosterior = round($saldoAnterior + $montoPago, 2);

                        $tesoreria->update([
                            'saldo_actual' => $saldoPosterior,
                        ]);

                        MovimientoTesoreria::create([
                            'tesoreria_id' => $tesoreria->id,
                            'user_id' => Auth::id(),
                            'sesion_caja_id' => $sesionAbierta->id,
                            'venta_id' => $venta->id,
                            'compra_id' => null,
                            'tipo' => 'INGRESO',
                            'medio' => 'BANCO',
                            'origen' => in_array($metodoPago, ['TARJETA'], true) ? 'VENTA_TARJETA' : 'VENTA_TRANSFERENCIA',
                            'descripcion' => 'Cobro de venta #' . $venta->id,
                            'monto' => $montoPago,
                            'saldo_anterior' => $saldoAnterior,
                            'saldo_posterior' => $saldoPosterior,
                            'numero_operacion' => $referencia,
                            'referencia' => null,
                        ]);
                    }
                }

                $this->recalcularSaldoEsperadoSesion($sesionAbierta->id);
            });

            return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()])->withInput();
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

    public function destroy(string $id)
    {
        return app(AnulacionVentaController::class)->destroy(request(), $id);
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

    private function recalcularSaldoEsperadoSesion(int $sesionCajaId): void
    {
        $sesion = SesionCaja::find($sesionCajaId);

        if (!$sesion) {
            return;
        }

        $ingresos = MovimientoCaja::where('sesion_caja_id', $sesion->id)
            ->where('tipo', 'INGRESO')
            ->sum('monto');

        $egresos = MovimientoCaja::where('sesion_caja_id', $sesion->id)
            ->where('tipo', 'EGRESO')
            ->sum('monto');

        $saldoEsperado = (float) $sesion->saldo_inicial + (float) $ingresos - (float) $egresos;

        $sesion->update([
            'saldo_final_esperado' => $saldoEsperado,
        ]);
    }
}