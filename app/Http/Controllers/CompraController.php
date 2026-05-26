<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Kardex;
use App\Models\MovimientoCaja;
use App\Models\MovimientoTesoreria;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\Proveedor;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-compra|crear-compra|mostrar-compra|eliminar-compra', only: ['index']),
            new Middleware('permission:crear-compra', only: ['create', 'store']),
            new Middleware('permission:mostrar-compra', only: ['show']),
            new Middleware('permission:eliminar-compra', only: ['destroy']),
        ];
    }

    public function index()
    {
        $compras = Compra::with('comprobante', 'proveedor.persona', 'productos')
            ->latest()
            ->get();

        return view('compra.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::with('persona')
            ->whereHas('persona', fn ($q) => $q->where('estado', 1))
            ->get();

        $comprobantes = Comprobante::where('estado', 1)->get();
        $productos = Producto::where('estado', 1)->get();

        return view('compra.create', compact('proveedores', 'comprobantes', 'productos'));
    }

    public function store(StoreCompraRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $compra = Compra::create([
                    'proveedor_id' => $request->proveedor_id,
                    'user_id' => Auth::id(),
                    'comprobante_id' => $request->comprobante_id,
                    'numero_comprobante' => $request->numero_comprobante,
                    'metodo_pago' => $request->metodo_pago,
                    'fecha_hora' => $request->fecha_hora,
                    'subtotal' => $request->subtotal,
                    'impuesto' => $request->impuesto,
                    'total' => $request->total,
                    'estado' => 1,
                ]);

                $ids = $request->get('arrayidproducto', []);
                $cantidades = $request->get('arraycantidad', []);
                $preciosCompra = $request->get('arraypreciocompra', []);
                $preciosVenta = $request->get('arrayprecioventa', []);

                foreach ($ids as $i => $productoId) {
                    $cantidad = (int) ($cantidades[$i] ?? 0);
                    $precioCompra = (float) ($preciosCompra[$i] ?? 0);
                    $precioVenta = (float) ($preciosVenta[$i] ?? 0);

                    $producto = Producto::whereKey($productoId)->lockForUpdate()->firstOrFail();

                    $compra->productos()->attach($producto->id, [
                        'cantidad' => $cantidad,
                        'precio_compra' => $precioCompra,
                        'precio_venta' => $precioVenta,
                    ]);

                    $producto->update([
                        'stock' => $producto->stock + $cantidad,
                        'precio_compra' => $precioCompra,
                        'precio_venta' => $precioVenta,
                    ]);

                    $ultimoSaldo = Kardex::where('producto_id', $producto->id)
                        ->lockForUpdate()
                        ->latest('id')
                        ->value('saldo') ?? 0;

                    Kardex::create([
                        'producto_id' => $producto->id,
                        'tipo_transaccion' => 'COMPRA',
                        'descripcion' => 'Compra #' . $compra->id,
                        'entrada' => $cantidad,
                        'salida' => 0,
                        'saldo' => $ultimoSaldo + $cantidad,
                        'costo_unitario' => $precioCompra,
                        'user_id' => Auth::id(),
                    ]);
                }

                $tesoreria = Tesoreria::withTrashed()->firstOrCreate(
                    ['nombre' => 'Tesorería Principal'],
                    ['saldo_efectivo' => 0, 'saldo_banco' => 0, 'estado' => 1]
                );

                $medio = strtoupper($request->metodo_pago);
                $campoSaldo = $medio === 'EFECTIVO' ? 'saldo_efectivo' : 'saldo_banco';

                $saldoAnterior = (float) $tesoreria->{$campoSaldo};
                $saldoPosterior = $saldoAnterior - (float) $compra->total;

                $tesoreria->update([
                    $campoSaldo => $saldoPosterior,
                ]);

                MovimientoTesoreria::create([
                    'tesoreria_id' => $tesoreria->id,
                    'user_id' => Auth::id(),
                    'tipo' => 'EGRESO',
                    'origen' => 'COMPRA_PRODUCTO',
                    'descripcion' => 'Pago de compra #' . $compra->id,
                    'monto' => $compra->total,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_posterior' => $saldoPosterior,
                ]);

                if ($medio === 'EFECTIVO') {
                    $sesionAbierta = SesionCaja::where('user_id', Auth::id())
                        ->where('estado', 1)
                        ->lockForUpdate()
                        ->first();

                    if ($sesionAbierta) {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesionAbierta->id,
                            'tipo' => 'EGRESO',
                            'descripcion' => 'Compra #' . $compra->id,
                            'monto' => $compra->total,
                        ]);
                    }
                }
            });

            return redirect()->route('compras.index')->with('success', 'Compra exitosa');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la compra: ' . $e->getMessage()]);
        }
    }

    public function show(Compra $compra)
    {
        $compra->load('comprobante', 'proveedor.persona', 'productos');
        return view('compra.show', compact('compra'));
    }

    public function destroy(string $id)
    {
        try {
            $compra = Compra::with('productos')->findOrFail($id);

            if ((int) $compra->estado === 1) {
                DB::transaction(function () use ($compra) {
                    foreach ($compra->productos as $producto) {
                        $cantidad = (int) $producto->pivot->cantidad;

                        $producto->update([
                            'stock' => $producto->stock - $cantidad,
                        ]);

                        $ultimoSaldo = Kardex::where('producto_id', $producto->id)
                            ->latest('id')
                            ->value('saldo') ?? 0;

                        Kardex::create([
                            'producto_id' => $producto->id,
                            'tipo_transaccion' => 'ANULACION',
                            'descripcion' => 'Anulación de compra #' . $compra->id,
                            'entrada' => 0,
                            'salida' => $cantidad,
                            'saldo' => $ultimoSaldo - $cantidad,
                            'costo_unitario' => $producto->precio_compra,
                            'user_id' => Auth::id(),
                        ]);
                    }

                    $tesoreria = Tesoreria::withTrashed()->firstOrCreate(
                        ['nombre' => 'Tesorería Principal'],
                        ['saldo_efectivo' => 0, 'saldo_banco' => 0, 'estado' => 1]
                    );

                    $medio = strtoupper($compra->metodo_pago);
                    $campoSaldo = $medio === 'EFECTIVO' ? 'saldo_efectivo' : 'saldo_banco';

                    $saldoAnterior = (float) $tesoreria->{$campoSaldo};
                    $saldoPosterior = $saldoAnterior + (float) $compra->total;

                    $tesoreria->update([
                        $campoSaldo => $saldoPosterior,
                    ]);

                    MovimientoTesoreria::create([
                        'tesoreria_id' => $tesoreria->id,
                        'user_id' => Auth::id(),
                        'tipo' => 'INGRESO',
                        'origen' => 'AJUSTE',
                        'descripcion' => 'Anulación de compra #' . $compra->id,
                        'monto' => $compra->total,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                    ]);

                    $compra->update(['estado' => 0]);
                });

                $message = 'Compra anulada';
            } else {
                $message = 'La compra ya estaba anulada';
            }

            return redirect()->route('compras.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la compra: ' . $e->getMessage()]);
        }
    }
}