<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\PagoVenta;
use App\Models\SesionCaja;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class PagoVentaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-pago-venta|crear-pago-venta', only: ['index']),
            new Middleware('permission:crear-pago-venta', only: ['store']),
        ];
    }

    public function index()
    {
        $pagos = PagoVenta::with('venta')->latest()->get();
        return view('pago_venta.index', compact('pagos'));
    }

    public function store(Request $request, Venta $venta)
    {
        $data = $request->validate([
            'metodo_pago' => 'required|string|max:50',
            'monto' => 'required|numeric|min:0.01',
            'referencia_operacion' => 'nullable|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($venta, $data) {
                $venta = Venta::with(['pagos', 'sesionCaja'])->lockForUpdate()->findOrFail($venta->id);

                $pagado = $venta->pagos->sum('monto');
                $restante = $venta->total - $pagado;

                if ($data['monto'] > $restante) {
                    throw new Exception('El monto del pago supera el saldo pendiente.');
                }

                $venta->pagos()->create([
                    'metodo_pago' => $data['metodo_pago'],
                    'monto' => $data['monto'],
                    'referencia_operacion' => $data['referencia_operacion'] ?? null,
                ]);

                $nuevoPagado = $pagado + $data['monto'];

                $venta->update([
                    'monto_recibido' => $nuevoPagado,
                    'vuelto_entregado' => max(0, $nuevoPagado - $venta->total),
                ]);

                if (strtoupper($data['metodo_pago']) === 'EFECTIVO') {
                    $sesion = SesionCaja::whereKey($venta->sesion_caja_id)->lockForUpdate()->first();

                    if ($sesion && (int) $sesion->estado === 1) {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesion->id,
                            'tipo' => 'INGRESO',
                            'descripcion' => 'Pago de venta #' . $venta->id,
                            'monto' => $data['monto'],
                        ]);
                    }
                }
            });

            return redirect()->route('ventas.show', $venta)->with('success', 'Pago registrado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }
}