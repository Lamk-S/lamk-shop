<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnulacionCompraRequest;
use App\Models\Compra;
use App\Models\CompraProducto;
use App\Models\Kardex;
use App\Models\MovimientoTesoreria;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Tesoreria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnulacionCompraController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:anular_compras', only: ['destroy']),
        ];
    }

    public function destroy(StoreAnulacionCompraRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            DB::transaction(function () use ($id, $data) {
                $compra = Compra::with([
                        'detalles.productoVariante.producto',
                        'comprobante',
                    ])
                    ->withTrashed()
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($compra->estado_documento === 'ANULADA') {
                    return;
                }

                foreach ($compra->detalles as $detalle) {
                    $variante = ProductoVariante::whereKey($detalle->producto_variante_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $producto = Producto::whereKey($variante->producto_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($variante->stock_actual < $detalle->cantidad) {
                        throw new Exception('No se puede anular la compra porque una o más variantes ya no tienen stock suficiente para revertir el ingreso.');
                    }

                    $saldoAnterior = (int) $variante->stock_actual;
                    $saldoPosterior = $saldoAnterior - (int) $detalle->cantidad;

                    $variante->update([
                        'stock_actual' => $saldoPosterior,
                    ]);

                    Kardex::create([
                        'producto_variante_id' => $variante->id,
                        'tipo_transaccion' => 'ANULACION',
                        'origen_type' => Compra::class,
                        'origen_id' => $compra->id,
                        'descripcion' => 'Anulación de compra #' . $compra->id,
                        'entrada' => 0,
                        'salida' => $detalle->cantidad,
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

                if (in_array($compra->metodo_pago, ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA'], true)) {
                    $tesoreriaCodigo = $compra->metodo_pago === 'EFECTIVO' ? 'TES-EFECTIVO' : 'TES-BANCO';
                    $tesoreria = Tesoreria::where('codigo', $tesoreriaCodigo)->lockForUpdate()->first();

                    if (!$tesoreria) {
                        $tesoreria = Tesoreria::create([
                            'codigo' => $tesoreriaCodigo,
                            'nombre' => $compra->metodo_pago === 'EFECTIVO' ? 'Caja General' : 'Banco Principal',
                            'tipo_cuenta' => $compra->metodo_pago === 'EFECTIVO' ? 'EFECTIVO' : 'BANCO',
                            'saldo_actual' => 0,
                            'estado' => 1,
                        ]);
                    }

                    $saldoAnterior = (float) $tesoreria->saldo_actual;
                    $saldoPosterior = round($saldoAnterior + $compra->total, 2);

                    $tesoreria->update([
                        'saldo_actual' => $saldoPosterior,
                    ]);

                    MovimientoTesoreria::create([
                        'tesoreria_id' => $tesoreria->id,
                        'user_id' => Auth::id(),
                        'sesion_caja_id' => null,
                        'venta_id' => null,
                        'compra_id' => $compra->id,
                        'tipo' => 'INGRESO',
                        'medio' => $compra->metodo_pago === 'EFECTIVO' ? 'EFECTIVO' : 'BANCO',
                        'origen' => 'AJUSTE',
                        'descripcion' => 'Anulación de compra #' . $compra->id,
                        'monto' => $compra->total,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                        'numero_operacion' => null,
                        'referencia' => null,
                    ]);
                }

                $compra->update([
                    'estado_documento' => 'ANULADA',
                    'motivo_anulacion' => $data['motivo_anulacion'] ?? 'Anulación manual',
                    'anulado_at' => now(),
                ]);
            });

            return redirect()->route('compras.index')->with('success', 'Compra anulada correctamente');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al anular la compra: ' . $e->getMessage()]);
        }
    }
}