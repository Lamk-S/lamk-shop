<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CompraProducto;
use App\Models\Comprobante;
use App\Models\EmpresaConfiguracion;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompraService
{
    public function __construct(
        protected InventarioService $inventarioService,
        protected TesoreriaService $tesoreriaService,
        protected ComprobanteService $comprobanteService,
        protected AuditoriaService $auditoriaService
    ) {
    }

    public function registrar(array $data, User $user): Compra
    {
        return DB::transaction(function () use ($data, $user) {
            $empresa = EmpresaConfiguracion::query()->first();
            $igv = (float) ($empresa?->igv_porcentaje ?? 18.00);

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
                    ->where('uso_comprobante', 'COMPRA')
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->firstOrFail();

                $datosComprobante = $this->comprobanteService->reservarCorrelativo($comprobante);
            }

            $proveedorId = $data['proveedor_id'];

            $subtotal = 0.0;
            $descuentoTotal = 0.0;
            $impuestoTotal = 0.0;
            $total = 0.0;
            $lineas = [];

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
                $costoUnitario = (float) $row['costo_unitario'];
                $descuento = (float) ($row['descuento'] ?? 0);

                $base = round(($cantidad * $costoUnitario) - $descuento, 2);
                $impuesto = $producto->afecto_igv ? round($base * $igv / 100, 2) : 0.00;
                $totalLinea = round($base + $impuesto, 2);

                $lineas[] = [
                    'variante' => $variante,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costoUnitario,
                    'descuento' => $descuento,
                    'subtotal' => round($cantidad * $costoUnitario, 2),
                    'impuesto' => $impuesto,
                    'total' => $totalLinea,
                    'producto_codigo' => $producto->codigo,
                    'producto_nombre' => $producto->nombre,
                    'talla_codigo' => $variante->talla->codigo,
                    'talla_nombre' => $variante->talla->nombre,
                ];

                $subtotal += round($cantidad * $costoUnitario, 2);
                $descuentoTotal += $descuento;
                $impuestoTotal += $impuesto;
                $total += $totalLinea;
            }

            $compra = Compra::create([
                'proveedor_id' => $proveedorId,
                'user_id' => $user->id,
                'comprobante_id' => $datosComprobante['comprobante_id'],
                'tipo_comprobante' => $datosComprobante['tipo_comprobante'],
                'serie' => $datosComprobante['serie'],
                'correlativo' => $datosComprobante['correlativo'],
                'proveedor_tipo_documento' => null,
                'proveedor_numero_documento' => null,
                'proveedor_nombre' => null,
                'proveedor_direccion' => null,
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
                $this->inventarioService->registrarEntrada(
                    $linea['variante'],
                    $linea['cantidad'],
                    $linea['costo_unitario'],
                    'Compra #' . $compra->id,
                    $compra,
                    $user
                );

                CompraProducto::create([
                    'compra_id' => $compra->id,
                    'producto_variante_id' => $linea['variante']->id,
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

                $linea['producto']->update([
                    'precio_compra' => $linea['costo_unitario'],
                    'precio_venta' => (!empty($data['actualizar_precio_venta']) && !empty($data['precio_venta']))
                        ? $data['precio_venta']
                        : $linea['producto']->precio_venta,
                ]);
            }

            if (in_array($data['metodo_pago'], ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA'], true)) {
                $this->tesoreriaService->registrarCompraPago(
                    $compra,
                    $data['metodo_pago'],
                    $compra->total,
                    null,
                    $user
                );
            }

            $this->auditoriaService->registrar(
                'Compra',
                $compra->id,
                'CREAR',
                [],
                $compra->fresh()->toArray(),
                $user
            );

            return $compra->fresh(['detalles', 'comprobante', 'proveedor.persona.documento']);
        });
    }

    public function anular(Compra $compra, string $motivo, User $user): Compra
    {
        return DB::transaction(function () use ($compra, $motivo, $user) {
            $compra = Compra::query()
                ->with(['detalles.productoVariante.producto', 'comprobante'])
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($compra->estado_documento === 'ANULADA') {
                return $compra;
            }

            foreach ($compra->detalles as $detalle) {
                $this->inventarioService->revertirEntrada(
                    $detalle->productoVariante,
                    (int) $detalle->cantidad,
                    (float) $detalle->costo_unitario,
                    'Anulación de compra #' . $compra->id,
                    $compra,
                    $user
                );
            }

            if (in_array($compra->metodo_pago, ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA'], true)) {
                $this->tesoreriaService->registrarAnulacion(
                    $compra->metodo_pago,
                    (float) $compra->total,
                    'ANULACION',
                    'Anulación de compra #' . $compra->id,
                    $user->id,
                    null,
                    null,
                    $compra->id
                );
            }

            $compra->update([
                'estado_documento' => 'ANULADA',
                'motivo_anulacion' => $motivo,
                'anulado_at' => now(),
            ]);

            $this->auditoriaService->registrar(
                'Compra',
                $compra->id,
                'ANULAR',
                [],
                $compra->fresh()->toArray(),
                $user
            );

            return $compra->fresh(['detalles', 'comprobante', 'proveedor.persona.documento']);
        });
    }
}