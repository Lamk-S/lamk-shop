<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Kardex;
use App\Models\MovimientoCaja;
use App\Models\MovimientoTesoreria;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\Venta;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', only: ['index']),
            new Middleware('permission:crear-venta', only: ['create', 'store']),
            new Middleware('permission:mostrar-venta', only: ['show']),
            new Middleware('permission:eliminar-venta', only: ['destroy']),
        ];
    }

    public function index()
    {
        $ventas = Venta::with('comprobante', 'cliente.persona', 'user', 'sesionCaja.caja')
            ->latest()
            ->get();

        return view('venta.index', compact('ventas'));
    }

    public function create()
    {
        $sesionAbierta = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 1)
            ->first();

        if (!$sesionAbierta) {
            return redirect()
                ->route('sesiones-caja.index')
                ->with('warning', 'Debes abrir una sesión de caja antes de registrar una venta.');
        }

        $productos = Producto::where('estado', 1)
            ->where('stock', '>', 0)
            ->get();

        $clientes = Cliente::whereHas('persona', fn($q) => $q->where('estado', 1))->get();
        $comprobantes = Comprobante::where('estado', 1)->get();

        return view('venta.create', compact('productos', 'clientes', 'comprobantes', 'sesionAbierta'));
    }

    public function store(StoreVentaRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $sesionAbierta = SesionCaja::where('user_id', Auth::id())
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->firstOrFail();

                $vueltoEntregado = max(0, (float) $request->monto_recibido - (float) $request->total);

                $venta = Venta::create([
                    'cliente_id' => $request->cliente_id,
                    'user_id' => Auth::id(),
                    'sesion_caja_id' => $sesionAbierta->id,
                    'comprobante_id' => $request->comprobante_id,
                    'numero_comprobante' => $request->numero_comprobante,
                    'fecha_hora' => $request->fecha_hora,
                    'subtotal' => $request->subtotal,
                    'impuesto' => $request->impuesto,
                    'total' => $request->total,
                    'monto_recibido' => $request->monto_recibido,
                    'vuelto_entregado' => $vueltoEntregado,
                    'estado' => 1,
                ]);

                $ids = $request->get('arrayidproducto', []);
                $cantidades = $request->get('arraycantidad', []);
                $preciosVenta = $request->get('arrayprecioventa', []);
                $descuentos = $request->get('arraydescuento', []);

                foreach ($ids as $i => $productoId) {
                    $cantidad = (int) ($cantidades[$i] ?? 0);
                    $precioVenta = (float) ($preciosVenta[$i] ?? 0);
                    $descuento = (float) ($descuentos[$i] ?? 0);

                    $producto = Producto::whereKey($productoId)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($producto->stock < $cantidad) {
                        throw new Exception("Stock insuficiente para el producto {$producto->nombre}");
                    }

                    $venta->productos()->attach($producto->id, [
                        'cantidad' => $cantidad,
                        'precio_venta' => $precioVenta,
                        'descuento' => $descuento,
                    ]);

                    $producto->update([
                        'stock' => $producto->stock - $cantidad,
                    ]);

                    $ultimoSaldo = Kardex::where('producto_id', $producto->id)
                        ->lockForUpdate()
                        ->latest('id')
                        ->value('saldo') ?? 0;

                    Kardex::create([
                        'producto_id' => $producto->id,
                        'tipo_transaccion' => 'VENTA',
                        'descripcion' => 'Venta #' . $venta->id,
                        'entrada' => 0,
                        'salida' => $cantidad,
                        'saldo' => $ultimoSaldo - $cantidad,
                        'costo_unitario' => $producto->precio_compra,
                        'user_id' => Auth::id(),
                    ]);
                }

                $venta->pagos()->create([
                    'metodo_pago' => $request->metodo_pago,
                    'monto' => $request->monto_recibido - $vueltoEntregado,
                    'referencia_operacion' => $request->referencia_operacion ?? null,
                ]);

                $metodo = strtoupper($request->metodo_pago);

                if ($metodo === 'EFECTIVO') {
                    MovimientoCaja::create([
                        'sesion_caja_id' => $sesionAbierta->id,
                        'tipo' => 'INGRESO',
                        'descripcion' => 'Pago de venta #' . $venta->id,
                        'monto' => $venta->monto_recibido,
                    ]);

                    if ($vueltoEntregado > 0) {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesionAbierta->id,
                            'tipo' => 'EGRESO',
                            'descripcion' => 'Vuelto por venta #' . $venta->id,
                            'monto' => $vueltoEntregado,
                        ]);
                    }

                    $this->recalcularSaldoEsperadoSesion($sesionAbierta->id);
                } else {
                    $tesoreria = Tesoreria::withTrashed()
                        ->lockForUpdate()
                        ->first();

                    $campoSaldo = in_array($metodo, ['TARJETA', 'TRANSFERENCIA'], true) ? 'saldo_banco' : 'saldo_efectivo';
                    $saldoAnterior = (float) $tesoreria->{$campoSaldo};
                    $saldoPosterior = $saldoAnterior + (float) $venta->total;

                    $tesoreria->update([
                        $campoSaldo => $saldoPosterior,
                    ]);

                    MovimientoTesoreria::create([
                        'tesoreria_id' => $tesoreria->id,
                        'user_id' => Auth::id(),
                        'venta_id' => $venta->id,
                        'tipo' => 'INGRESO',
                        'origen' => $metodo === 'TARJETA' ? 'VENTA_TARJETA' : 'VENTA_TRANSFERENCIA',
                        'descripcion' => 'Cobro de venta #' . $venta->id,
                        'monto' => $venta->total,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                    ]);
                }
            });

            return redirect()->route('ventas.index')->with('success', 'Venta exitosa');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()]);
        }
    }

    public function show(Venta $venta)
    {
        $venta->load('comprobante', 'cliente.persona', 'user', 'sesionCaja.caja', 'productos', 'pagos');
        return view('venta.show', compact('venta'));
    }

    public function destroy(string $id)
    {
        try {
            $venta = Venta::with(['productos', 'pagos', 'sesionCaja'])->findOrFail($id);

            if ((int) $venta->estado !== 1) {
                return redirect()->route('ventas.index')->with('success', 'La venta ya estaba anulada');
            }

            DB::transaction(function () use ($venta) {

                foreach ($venta->productos as $producto) {
                    $cantidad = (int) $producto->pivot->cantidad;

                    $producto->update([
                        'stock' => $producto->stock + $cantidad,
                    ]);

                    $ultimoSaldo = Kardex::where('producto_id', $producto->id)
                        ->latest('id')
                        ->value('saldo') ?? 0;

                    Kardex::create([
                        'producto_id' => $producto->id,
                        'tipo_transaccion' => 'ANULACION',
                        'descripcion' => 'Anulación de venta #' . $venta->id,
                        'entrada' => $cantidad,
                        'salida' => 0,
                        'saldo' => $ultimoSaldo + $cantidad,
                        'costo_unitario' => $producto->precio_compra,
                        'user_id' => Auth::id(),
                    ]);
                }

                $metodo = strtoupper((string) optional($venta->pagos->first())->metodo_pago);

                if ($metodo === 'EFECTIVO') {
                    $sesion = $venta->sesionCaja;

                    if ($sesion && (int) $sesion->estado === 1) {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesion->id,
                            'tipo' => 'EGRESO',
                            'descripcion' => 'Anulación de venta #' . $venta->id,
                            'monto' => $venta->monto_recibido,
                        ]);

                        if ((float) $venta->vuelto_entregado > 0) {
                            MovimientoCaja::create([
                                'sesion_caja_id' => $sesion->id,
                                'tipo' => 'INGRESO',
                                'descripcion' => 'Reverso de vuelto por anulación venta #' . $venta->id,
                                'monto' => $venta->vuelto_entregado,
                            ]);
                        }

                        $this->recalcularSaldoEsperadoSesion($sesion->id);
                    } else {
                        $tesoreria = Tesoreria::withTrashed()
                            ->lockForUpdate()
                            ->first();

                        $saldoAnterior = (float) $tesoreria->saldo_efectivo;
                        $montoAjuste = (float) $venta->total;

                        if ($saldoAnterior < $montoAjuste) {
                            throw new Exception('Saldo insuficiente en tesorería efectivo para anular la venta.');
                        }

                        $saldoPosterior = round($saldoAnterior - $montoAjuste, 2);

                        $tesoreria->update([
                            'saldo_efectivo' => $saldoPosterior,
                        ]);

                        MovimientoTesoreria::create([
                            'tesoreria_id' => $tesoreria->id,
                            'user_id' => Auth::id(),
                            'venta_id' => $venta->id,
                            'tipo' => 'EGRESO',
                            'origen' => 'AJUSTE',
                            'descripcion' => 'Anulación de venta #' . $venta->id,
                            'monto' => $montoAjuste,
                            'saldo_anterior' => $saldoAnterior,
                            'saldo_posterior' => $saldoPosterior,
                        ]);
                    }
                } else {
                    $tesoreria = Tesoreria::withTrashed()->firstOrCreate(
                        ['nombre' => 'Tesorería Principal'],
                        [
                            'saldo_efectivo' => 1000,
                            'saldo_banco' => 1000,
                            'estado' => 1,
                        ]
                    );

                    $tesoreria = Tesoreria::whereKey($tesoreria->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $campoSaldo = in_array($metodo, ['TARJETA', 'TRANSFERENCIA'], true)
                        ? 'saldo_banco'
                        : 'saldo_efectivo';

                    $saldoAnterior = (float) $tesoreria->{$campoSaldo};

                    if ($saldoAnterior < (float) $venta->total) {
                        throw new Exception("Saldo insuficiente en tesorería ({$campoSaldo}) para anular la venta.");
                    }

                    $saldoPosterior = round($saldoAnterior - (float) $venta->total, 2);

                    $tesoreria->update([
                        $campoSaldo => $saldoPosterior,
                    ]);

                    MovimientoTesoreria::create([
                        'tesoreria_id' => $tesoreria->id,
                        'user_id' => Auth::id(),
                        'venta_id' => $venta->id,
                        'tipo' => 'EGRESO',
                        'origen' => 'AJUSTE',
                        'descripcion' => 'Anulación de venta #' . $venta->id,
                        'monto' => $venta->total,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                    ]);
                }

                $venta->update(['estado' => 0]);
            });

            return redirect()->route('ventas.index')->with('success', 'Venta anulada');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar la venta: ' . $e->getMessage()]);
        }
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
