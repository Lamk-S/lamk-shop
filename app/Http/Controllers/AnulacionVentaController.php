<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnulacionVentaRequest;
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

class AnulacionVentaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:anular_ventas', only: ['destroy']),
        ];
    }

    public function destroy(StoreAnulacionVentaRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($id, $data) {
                $venta = Venta::with([
                        'detalles.productoVariante.producto',
                        'pagos',
                        'sesionCaja',
                    ])
                    ->withTrashed()
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($venta->estado_documento === 'ANULADA') {
                    return;
                }

                foreach ($venta->detalles as $detalle) {
                    $variante = ProductoVariante::whereKey($detalle->producto_variante_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $producto = Producto::whereKey($variante->producto_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $saldoAnterior = (int) $variante->stock_actual;
                    $saldoPosterior = $saldoAnterior + (int) $detalle->cantidad;

                    $variante->update([
                        'stock_actual' => $saldoPosterior,
                    ]);

                    Kardex::create([
                        'producto_variante_id' => $variante->id,
                        'tipo_transaccion' => 'ANULACION',
                        'origen_type' => Venta::class,
                        'origen_id' => $venta->id,
                        'descripcion' => 'Anulación de venta #' . $venta->id,
                        'entrada' => $detalle->cantidad,
                        'salida' => 0,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                        'costo_unitario' => $detalle->costo_unitario,
                        'costo_total' => round($detalle->cantidad * $detalle->costo_unitario, 2),
                        'user_id' => Auth::id(),
                    ]);

                    $producto->update([
                        'stock_total' => $producto->variantes()->where('estado', 1)->sum('stock_actual'),
                    ]);
                }

                $sesion = $venta->sesionCaja;
                $sesionActiva = $sesion && $sesion->estado_sesion === 'ABIERTA';

                foreach ($venta->pagos as $pago) {
                    $metodoPago = strtoupper($pago->metodo_pago);
                    $montoPago = (float) $pago->monto;

                    if ($metodoPago === 'EFECTIVO') {
                        if ($sesionActiva) {
                            MovimientoCaja::create([
                                'sesion_caja_id' => $sesion->id,
                                'tipo' => 'EGRESO',
                                'origen' => 'AJUSTE',
                                'descripcion' => 'Anulación de venta #' . $venta->id,
                                'monto' => $montoPago,
                                'referencia_type' => Venta::class,
                                'referencia_id' => $venta->id,
                            ]);
                        } else {
                            $tesoreria = Tesoreria::where('codigo', 'TES-EFECTIVO')->lockForUpdate()->first();

                            if (!$tesoreria) {
                                $tesoreria = Tesoreria::create([
                                    'codigo' => 'TES-EFECTIVO',
                                    'nombre' => 'Caja General',
                                    'tipo_cuenta' => 'EFECTIVO',
                                    'saldo_actual' => 0,
                                    'estado' => 1,
                                ]);
                            }

                            $saldoAnterior = (float) $tesoreria->saldo_actual;
                            $saldoPosterior = round($saldoAnterior - $montoPago, 2);

                            if ($saldoPosterior < 0) {
                                throw new Exception('Saldo insuficiente en tesorería efectivo para anular la venta.');
                            }

                            $tesoreria->update([
                                'saldo_actual' => $saldoPosterior,
                            ]);

                            MovimientoTesoreria::create([
                                'tesoreria_id' => $tesoreria->id,
                                'user_id' => Auth::id(),
                                'sesion_caja_id' => $sesion?->id,
                                'venta_id' => $venta->id,
                                'compra_id' => null,
                                'tipo' => 'EGRESO',
                                'medio' => 'EFECTIVO',
                                'origen' => 'AJUSTE',
                                'descripcion' => 'Anulación de venta #' . $venta->id,
                                'monto' => $montoPago,
                                'saldo_anterior' => $saldoAnterior,
                                'saldo_posterior' => $saldoPosterior,
                                'numero_operacion' => null,
                                'referencia' => null,
                            ]);
                        }
                    } else {
                        $tesoreria = Tesoreria::where('codigo', 'TES-BANCO')->lockForUpdate()->first();

                        if (!$tesoreria) {
                            $tesoreria = Tesoreria::create([
                                'codigo' => 'TES-BANCO',
                                'nombre' => 'Banco Principal',
                                'tipo_cuenta' => 'BANCO',
                                'saldo_actual' => 0,
                                'estado' => 1,
                            ]);
                        }

                        $saldoAnterior = (float) $tesoreria->saldo_actual;
                        $saldoPosterior = round($saldoAnterior - $montoPago, 2);

                        if ($saldoPosterior < 0) {
                            throw new Exception('Saldo insuficiente en tesorería bancaria para anular la venta.');
                        }

                        $tesoreria->update([
                            'saldo_actual' => $saldoPosterior,
                        ]);

                        MovimientoTesoreria::create([
                            'tesoreria_id' => $tesoreria->id,
                            'user_id' => Auth::id(),
                            'sesion_caja_id' => $sesion?->id,
                            'venta_id' => $venta->id,
                            'compra_id' => null,
                            'tipo' => 'EGRESO',
                            'medio' => 'BANCO',
                            'origen' => in_array($metodoPago, ['TARJETA'], true) ? 'VENTA_TARJETA' : 'VENTA_TRANSFERENCIA',
                            'descripcion' => 'Anulación de venta #' . $venta->id,
                            'monto' => $montoPago,
                            'saldo_anterior' => $saldoAnterior,
                            'saldo_posterior' => $saldoPosterior,
                            'numero_operacion' => $pago->referencia_operacion,
                            'referencia' => null,
                        ]);
                    }
                }

                if ($sesionActiva) {
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

                $venta->update([
                    'estado_documento' => 'ANULADA',
                    'motivo_anulacion' => $data['motivo_anulacion'] ?? 'Anulación manual',
                    'anulado_at' => now(),
                    'sunat_estado' => 'ANULADO',
                    'sunat_mensaje' => 'Documento anulado internamente',
                ]);
            });

            return redirect()->route('ventas.index')->with('success', 'Venta anulada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al anular la venta: ' . $e->getMessage()]);
        }
    }
}