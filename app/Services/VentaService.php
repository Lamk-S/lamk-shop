<?php

namespace App\Services;

use App\Enums\EstadoDocumentoVenta;
use App\Enums\EstadoSesion;
use App\Enums\MetodoPago;
use App\Enums\OrigenMovimientoCaja;
use App\Enums\OrigenMovimientoTesoreria;
use App\Enums\TipoComprobante;
use App\Enums\TipoCuenta;
use App\Enums\TipoMovimiento;
use App\Enums\TipoTransaccionKardex;
use App\Enums\UsoComprobante;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\EmpresaConfiguracion;
use App\Models\MovimientoCaja;
use App\Models\PagoVenta;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\ProductoVenta;
use App\Models\SesionCaja;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class VentaService
{
    public function __construct(
        protected InventarioService $inventarioService,
        protected TesoreriaService $tesoreriaService,
        protected CajaService $cajaService,
        protected ComprobanteService $comprobanteService,
        protected AuditoriaService $auditoriaService
    ) {}

    public function registrar(array $data, User $user, ?Request $request = null): Venta
    {
        return DB::transaction(function () use ($data, $user, $request) {
            $sesionAbierta = SesionCaja::query()
                ->where('user_id', $user->id)
                ->where('estado_sesion', EstadoSesion::ABIERTA)
                ->lockForUpdate()
                ->firstOrFail();

            $empresa = EmpresaConfiguracion::query()->first();
            $igv = (float) ($empresa?->igv_porcentaje ?? 18.00);

            $cliente = null;
            if (!empty($data['cliente_id'])) {
                $cliente = Cliente::query()
                    ->with(['persona.documento'])
                    ->whereKey($data['cliente_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $comprobante = null;
            $datosComprobante = [
                'comprobante_id' => null,
                'tipo_comprobante' => null,
                'serie' => null,
                'correlativo' => null,
            ];

            if (!empty($data['comprobante_id'])) {
                $comprobante = Comprobante::query()
                    ->whereKey($data['comprobante_id'])
                    ->where('uso_comprobante', UsoComprobante::VENTA)
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->firstOrFail();

                $datosComprobante = $this->comprobanteService->reservarCorrelativo($comprobante);
            } else {
                $ticket = $this->comprobanteService->obtenerDisponibleParaVenta(null);
                if ($ticket) {
                    $datosComprobante = $this->comprobanteService->reservarCorrelativo($ticket);
                    $comprobante = $ticket;
                }
            }

            if (($datosComprobante['tipo_comprobante'] ?? null) === TipoComprobante::FACTURA->value && !$cliente) {
                throw new RuntimeException('La factura requiere un cliente identificado.');
            }

            if (($datosComprobante['tipo_comprobante'] ?? null) === TipoComprobante::FACTURA->value && $cliente?->persona?->documento?->codigo !== 'RUC') {
                throw new RuntimeException('La factura solo puede emitirse a un cliente con RUC.');
            }

            $lineas = [];
            $subtotal = 0.0;
            $descuentoTotal = 0.0;
            $impuestoTotal = 0.0;
            $total = 0.0;

            foreach ($data['detalles'] as $row) {
                $variante = ProductoVariante::query()
                    ->with(['producto', 'talla'])
                    ->whereKey($row['producto_variante_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $producto = Producto::query()
                    ->whereKey($variante->producto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $cantidad = (int) $row['cantidad'];
                $precioUnitario = (float) $row['precio_unitario'];
                $descuento = (float) ($row['descuento'] ?? 0);

                $this->inventarioService->validarStockDisponible($variante, $cantidad);

                $costoUnitario = $this->inventarioService->obtenerCostoSalida($variante);

                $totalLinea = round(($cantidad * $precioUnitario) - $descuento, 2);

                if ($producto->afecto_igv) {
                    $base = round($totalLinea / (1 + ($igv / 100)), 2); 
                    $impuesto = round($totalLinea - $base, 2);
                } else {
                    $base = $totalLinea;
                    $impuesto = 0.00;
                }

                $lineas[] = [
                    'variante' => $variante,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento' => $descuento,
                    'subtotal' => $base,
                    'impuesto' => $impuesto,
                    'total' => $totalLinea,
                    'costo_unitario' => $costoUnitario,
                    'producto_codigo' => $producto->codigo,
                    'producto_nombre' => $producto->nombre,
                    'talla_codigo' => $variante->talla->codigo,
                    'talla_nombre' => $variante->talla->nombre,
                ];

                $subtotal += $base;
                $descuentoTotal += $descuento;
                $impuestoTotal += $impuesto;
                $total += $totalLinea;
            }

            $paymentRows = collect($data['pagos'] ?? [])
                ->map(function ($payment) {
                    return [
                        'metodo_pago' => strtoupper(trim((string) ($payment['metodo_pago'] ?? ''))),
                        'monto' => (float) ($payment['monto'] ?? 0),
                        'referencia_operacion' => $payment['referencia_operacion'] ?? null,
                    ];
                })
                ->filter(fn($payment) => $payment['metodo_pago'] !== '' && $payment['monto'] > 0)
                ->values();

            $metodoPagoPrincipal = strtoupper((string) ($data['metodo_pago'] ?? ''));

            if ($paymentRows->isEmpty()) {
                if ($metodoPagoPrincipal === MetodoPago::MIXTO->value) {
                    throw new RuntimeException('Para una venta mixta debes enviar el detalle de pagos.');
                }

                if ($metodoPagoPrincipal === '') {
                    throw new RuntimeException('Debes registrar al menos un método de pago.');
                }

                $paymentRows = collect([
                    [
                        'metodo_pago' => $metodoPagoPrincipal,
                        'monto' => (float) ($data['monto_recibido'] ?? $total),
                        'referencia_operacion' => $data['referencia_operacion'] ?? null,
                    ],
                ]);
            }

            $pagosTotal = round((float) $paymentRows->sum('monto'), 2);
            $totalVenta = round($total, 2);

            $soloUnPagoEfectivo = $paymentRows->count() === 1 && strtoupper($paymentRows->first()['metodo_pago']) === MetodoPago::EFECTIVO->value;

            // Validación de montos
            if ($soloUnPagoEfectivo) {
                if ($pagosTotal < $totalVenta) {
                    throw new RuntimeException("El monto recibido en efectivo (S/ {$pagosTotal}) no puede ser menor al total de la venta (S/ {$totalVenta}).");
                }
            } else {
                if ($pagosTotal !== $totalVenta) {
                    throw new RuntimeException("El total de pagos (S/ {$pagosTotal}) debe coincidir exactamente con el total de la venta (S/ {$totalVenta}).");
                }
            }

            // Cálculo del vuelto solo si es efectivo
            $vueltoEntregado = $soloUnPagoEfectivo
                ? round(max(0, $pagosTotal - $totalVenta), 2)
                : 0.00;

            $venta = Venta::create([
                'cliente_id' => $cliente?->id,
                'user_id' => $user->id,
                'sesion_caja_id' => $sesionAbierta->id,
                'comprobante_id' => $datosComprobante['comprobante_id'],
                'tipo_comprobante' => $datosComprobante['tipo_comprobante'],
                'serie' => $datosComprobante['serie'],
                'correlativo' => $datosComprobante['correlativo'],
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
                'estado_documento' => EstadoDocumentoVenta::EMITIDA,
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
                $this->inventarioService->registrarSalida(
                    $linea['variante'],
                    $linea['cantidad'],
                    $linea['costo_unitario'],
                    'Venta #' . $venta->id,
                    $venta,
                    $user,
                    TipoTransaccionKardex::VENTA
                );

                ProductoVenta::create([
                    'venta_id' => $venta->id,
                    'producto_variante_id' => $linea['variante']->id,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                    'descuento' => $linea['descuento'],
                    'subtotal' => $linea['subtotal'],
                    'impuesto' => $linea['impuesto'],
                    'total' => $linea['total'],
                    'costo_unitario' => $linea['costo_unitario'],
                    'producto_codigo' => $linea['producto_codigo'],
                    'producto_nombre' => $linea['producto_nombre'],
                    'talla_codigo' => $linea['talla_codigo'],
                    'talla_nombre' => $linea['talla_nombre'],
                ]);
            }

            foreach ($paymentRows as $payment) {
                $metodoPago = strtoupper($payment['metodo_pago']);
                $montoPago = (float) $payment['monto'];
                $referencia = $payment['referencia_operacion'] ?? null;

                PagoVenta::create([
                    'venta_id' => $venta->id,
                    'metodo_pago' => $metodoPago,
                    'monto' => $montoPago,
                    'referencia_operacion' => $referencia,
                    'moneda' => $data['moneda'] ?? 'PEN',
                    'estado' => 1,
                ]);

                if ($metodoPago === MetodoPago::EFECTIVO->value) {
                    MovimientoCaja::create([
                        'sesion_caja_id' => $sesionAbierta->id,
                        'tipo' => TipoMovimiento::INGRESO,
                        'origen' => OrigenMovimientoCaja::VENTA,
                        'descripcion' => 'Cobro de venta #' . $venta->id,
                        'monto' => $montoPago,
                        'referencia_type' => Venta::class,
                        'referencia_id' => $venta->id,
                    ]);

                    if ($soloUnPagoEfectivo && $vueltoEntregado > 0) {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesionAbierta->id,
                            'tipo' => TipoMovimiento::EGRESO,
                            'origen' => OrigenMovimientoCaja::VENTA,
                            'descripcion' => 'Vuelto de venta #' . $venta->id,
                            'monto' => $vueltoEntregado,
                            'referencia_type' => Venta::class,
                            'referencia_id' => $venta->id,
                        ]);
                    }
                } else {
                    $this->tesoreriaService->registrarVentaIngreso(
                        $venta,
                        $metodoPago,
                        $montoPago,
                        $referencia,
                        $sesionAbierta,
                        $user
                    );
                }
            }

            $this->cajaService->recalcularSaldoEsperado($sesionAbierta);

            $this->auditoriaService->registrar(
                'Venta',
                $venta->id,
                'CREAR',
                [],
                $venta->fresh()->toArray(),
                $user,
                $request
            );

            return $venta->fresh([
                'comprobante',
                'cliente.persona.documento',
                'user',
                'sesionCaja.caja',
                'detalles.productoVariante.producto.marca',
                'detalles.productoVariante.talla',
                'pagos',
            ]);
        });
    }

    public function anular(Venta $venta, string $motivo, User $user , ?Request $request = null): Venta
    {
        return DB::transaction(function () use ($venta, $motivo, $user, $request) {
            $venta = Venta::query()
                ->with(['detalles.productoVariante.producto', 'pagos', 'sesionCaja'])
                ->whereKey($venta->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($venta->estado_documento === EstadoDocumentoVenta::ANULADA) {
                return $venta;
            }

            foreach ($venta->detalles as $detalle) {
                $this->inventarioService->revertirSalida(
                    $detalle->productoVariante,
                    (int) $detalle->cantidad,
                    (float) $detalle->costo_unitario,
                    'Anulación de venta #' . $venta->id,
                    $venta,
                    $user
                );
            }

            $sesion = $venta->sesionCaja;
            $sesionActiva = $sesion && $sesion->estado_sesion === EstadoSesion::ABIERTA;

            foreach ($venta->pagos as $pago) {
                $metodoPago = strtoupper($pago->metodo_pago);
                $montoPago = (float) $pago->monto;

                if ($metodoPago === MetodoPago::EFECTIVO->value) {
                    if ($sesionActiva) {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesion->id,
                            'tipo' => TipoMovimiento::EGRESO,
                            'origen' => OrigenMovimientoCaja::ANULACION,
                            'descripcion' => 'Anulación de venta #' . $venta->id,
                            'monto' => $montoPago,
                            'referencia_type' => Venta::class,
                            'referencia_id' => $venta->id,
                        ]);
                    } else {
                        $this->tesoreriaService->registrarAnulacion(
                            TipoCuenta::EFECTIVO->value,
                            $montoPago,
                            OrigenMovimientoTesoreria::ANULACION->value,
                            'Anulación de venta #' . $venta->id,
                            $user->id,
                            $sesion?->id,
                            $venta->id,
                            null,
                            $pago->referencia_operacion
                        );
                    }
                } else {
                    $this->tesoreriaService->registrarAnulacion(
                        TipoCuenta::BANCO->value,
                        $montoPago,
                        in_array($metodoPago, [MetodoPago::TARJETA->value], true) 
                            ? OrigenMovimientoTesoreria::VENTA_TARJETA->value 
                            : OrigenMovimientoTesoreria::VENTA_TRANSFERENCIA->value,
                        'Anulación de venta #' . $venta->id,
                        $user->id,
                        $sesion?->id,
                        $venta->id,
                        null,
                        $pago->referencia_operacion
                    );
                }
            }

            if ($sesionActiva) {
                $this->cajaService->recalcularSaldoEsperado($sesion);
            }

            $venta->update([
                'estado_documento' => EstadoDocumentoVenta::ANULADA,
                'motivo_anulacion' => $motivo,
                'anulado_at' => now(),
                'sunat_estado' => 'ANULADO',
                'sunat_mensaje' => 'Documento anulado internamente',
            ]);

            $this->auditoriaService->registrar(
                'Venta',
                $venta->id,
                'ANULAR',
                [],
                $venta->fresh()->toArray(),
                $user,
                $request
            );

            return $venta->fresh([
                'comprobante',
                'cliente.persona.documento',
                'user',
                'sesionCaja.caja',
                'detalles.productoVariante.producto.marca',
                'detalles.productoVariante.talla',
                'pagos',
            ]);
        });
    }
}