<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\MovimientoTesoreria;
use App\Models\PagoVenta;
use App\Models\SesionCaja;
use App\Models\Tesoreria;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoVentaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_tesoreria', only: ['index']),
            new Middleware('permission:registrar_ventas', only: ['store']),
        ];
    }

    public function index(Request $request)
    {
        $query = PagoVenta::with([
                'venta.comprobante',
                'venta.cliente.persona.documento',
                'venta.user',
                'venta.sesionCaja.caja',
            ])
            ->latest('id');

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('venta_id')) {
            $query->where('venta_id', $request->venta_id);
        }

        $pagos = $query->get();

        return view('pago_venta.index', compact('pagos'));
    }

    public function store(Request $request, Venta $venta)
    {
        $data = $request->validate([
            'metodo_pago' => ['required', 'in:EFECTIVO,TARJETA,TRANSFERENCIA,YAPE,PLIN,OTRO'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'referencia_operacion' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            DB::transaction(function () use ($venta, $data) {
                $venta = Venta::with(['pagos', 'sesionCaja'])
                    ->whereKey($venta->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($venta->estado_documento === 'ANULADA') {
                    throw new Exception('No se puede registrar un pago sobre una venta anulada.');
                }

                $pagado = (float) $venta->pagos->sum('monto');
                $restante = round((float) $venta->total - $pagado, 2);

                if ((float) $data['monto'] > $restante) {
                    throw new Exception('El monto del pago supera el saldo pendiente.');
                }

                $venta->pagos()->create([
                    'metodo_pago' => strtoupper($data['metodo_pago']),
                    'monto' => $data['monto'],
                    'referencia_operacion' => $data['referencia_operacion'] ?? null,
                    'moneda' => $venta->moneda ?? 'PEN',
                    'estado' => 1,
                ]);

                $nuevoPagado = round($pagado + (float) $data['monto'], 2);

                $venta->update([
                    'monto_recibido' => $nuevoPagado,
                    'vuelto_entregado' => max(0, $nuevoPagado - (float) $venta->total),
                ]);

                $metodo = strtoupper($data['metodo_pago']);

                if ($metodo === 'EFECTIVO') {
                    $sesion = SesionCaja::whereKey($venta->sesion_caja_id)
                        ->lockForUpdate()
                        ->first();

                    if ($sesion && $sesion->estado_sesion === 'ABIERTA') {
                        MovimientoCaja::create([
                            'sesion_caja_id' => $sesion->id,
                            'tipo' => 'INGRESO',
                            'origen' => 'VENTA',
                            'descripcion' => 'Pago de venta #' . $venta->id,
                            'monto' => $data['monto'],
                            'referencia_type' => Venta::class,
                            'referencia_id' => $venta->id,
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
                    $saldoPosterior = round($saldoAnterior + (float) $data['monto'], 2);

                    $tesoreria->update([
                        'saldo_actual' => $saldoPosterior,
                    ]);

                    MovimientoTesoreria::create([
                        'tesoreria_id' => $tesoreria->id,
                        'user_id' => Auth::id(),
                        'sesion_caja_id' => $venta->sesion_caja_id,
                        'venta_id' => $venta->id,
                        'compra_id' => null,
                        'tipo' => 'INGRESO',
                        'medio' => 'BANCO',
                        'origen' => in_array($metodo, ['TARJETA'], true) ? 'VENTA_TARJETA' : 'VENTA_TRANSFERENCIA',
                        'descripcion' => 'Pago de venta #' . $venta->id,
                        'monto' => $data['monto'],
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_posterior' => $saldoPosterior,
                        'numero_operacion' => $data['referencia_operacion'] ?? null,
                        'referencia' => null,
                    ]);
                }
            });

            return redirect()
                ->route('ventas.show', $venta)
                ->with('success', 'Pago registrado correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar el pago: ' . $e->getMessage(),
            ]);
        }
    }
}