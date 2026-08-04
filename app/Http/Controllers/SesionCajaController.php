<?php

namespace App\Http\Controllers;

use App\Enums\EstadoSesion;
use App\Http\Requests\StoreSesionCajaRequest;
use App\Models\Caja;
use App\Models\EmpresaConfiguracion;
use App\Models\SesionCaja;
use App\Models\User;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SesionCajaController extends Controller implements HasMiddleware
{
    public function __construct(protected CajaService $cajaService)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_cajas|abrir_caja|cerrar_caja', only: ['index', 'show']),
            new Middleware('permission:abrir_caja', only: ['create', 'store']),
            new Middleware('permission:cerrar_caja', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = SesionCaja::with(['caja:id,nombre', 'user:id,name'])->latest('id');

        $query->when($request->filled('estado_sesion'), fn($q) => $q->where('estado_sesion', $request->estado_sesion))
              ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
              ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('fecha_hora_apertura', '>=', $request->fecha_desde))
              ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('fecha_hora_apertura', '<=', $request->fecha_hasta));

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50], true) ? $perPage : 10;

        $sesiones = $query->paginate($perPage)->withQueryString();

        $cajeros = User::whereHas('sesionesCaja')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('sesion_caja.index', compact('sesiones', 'perPage', 'cajeros'));
    }

    public function create()
    {
        $cajas = Caja::where('estado', 1)
            ->whereDoesntHave('sesionesCaja', function ($query) {
                $query->where('estado_sesion', EstadoSesion::ABIERTA);
            })->orderBy('nombre')->get();

        return view('sesion_caja.create', compact('cajas'));
    }

    public function store(StoreSesionCajaRequest $request)
    {
        $data = $request->validated();

        try {
            $this->cajaService->abrirCaja(
                $request->user(),
                (int) $data['caja_id'],
                isset($data['saldo_inicial']) ? (float) $data['saldo_inicial'] : null,
                $data['observacion_apertura'] ?? null
            );

            return redirect()
                ->route('sesiones-caja.index')
                ->with('success', 'Sesión de caja abierta correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show(SesionCaja $sesionCaja)
    {
        $sesionCaja->load([
            'caja',
            'user',
            'userCierre',
            'movimientosCaja',
            'ventas.comprobante',
            'ventas.cliente.persona.documento',
            'ventas.user',
            'ventas.pagos',
        ]);

        $movimientos = $sesionCaja->movimientosCaja;
        $ventasValidas = $sesionCaja->ventas->where('estado_documento', '!=', 'ANULADA');

        $totales = [
            'ingresos' => $movimientos->filter(fn($m) => ($m->tipo->value ?? $m->tipo) === 'INGRESO' && ($m->origen->value ?? $m->origen) !== 'APERTURA')->sum('monto'),
            'egresos'  => $movimientos->filter(fn($m) => ($m->tipo->value ?? $m->tipo) === 'EGRESO')->sum('monto'),
            'ventas'   => $ventasValidas->sum('total'),
            'movimientos_count' => $movimientos->count(),
            'ventas_count'      => $ventasValidas->count(),
        ];

        return view('sesion_caja.show', compact('sesionCaja', 'totales', 'ventasValidas'));
    }

    public function destroy(Request $request, SesionCaja $sesion_caja)
    {
        $data = $request->validate([
            'saldo_final_declarado' => ['required', 'numeric', 'min:0'],
            'observacion_cierre' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->cajaService->cerrarCaja(
                $sesion_caja,
                (float) $data['saldo_final_declarado'],
                $data['observacion_cierre'] ?? null,
                $request->user()
            );

            return redirect()
                ->route('sesiones-caja.index')
                ->with('success', 'Sesión de caja cerrada correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al cerrar la sesión: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function ticket(SesionCaja $sesionCaja)
    {
        $sesionCaja->load([
            'caja', 'user', 'userCierre', 'movimientosCaja',
            'ventas.comprobante', 'ventas.pagos',
        ]);

        $movimientos = $sesionCaja->movimientosCaja;
        $ventasValidas = $sesionCaja->ventas->where('estado_documento', '!=', 'ANULADA');

        $pagos = collect();
        foreach ($ventasValidas as $venta) {
            foreach ($venta->pagos as $pago) {
                $pagos->push($pago);
            }
        }

        $pagosPorMetodo = $pagos->groupBy(function($p) {
            return strtoupper($p->metodo_pago->value ?? $p->metodo_pago);
        })->map->sum('monto');

        $ventasEfectivoBruto = $pagosPorMetodo->get('EFECTIVO', 0);
        $vueltosTotales      = $ventasValidas->sum('vuelto_entregado');
        $pagosDigitales      = $pagosPorMetodo->except(['EFECTIVO']);

        $ingresosManuales = $movimientos->filter(function($m) {
            $tipo = strtoupper($m->tipo->value ?? $m->tipo);
            $origen = strtoupper($m->origen->value ?? $m->origen);
            return $tipo === 'INGRESO' && !in_array($origen, ['APERTURA', 'VENTA']);
        })->sum('monto');

        $egresosManuales = $movimientos->filter(function($m) {
            $tipo = strtoupper($m->tipo->value ?? $m->tipo);
            $origen = strtoupper($m->origen->value ?? $m->origen);
            
            return $tipo === 'EGRESO' && !in_array($origen, ['VENTA', 'ANULACION']);
        })->sum('monto');

        $totales = [
            'ventas_efectivo_bruto' => $ventasEfectivoBruto,
            'vueltos_totales'       => $vueltosTotales,
            'ingresos_manuales'     => $ingresosManuales,
            'egresos_manuales'      => $egresosManuales,
            'pagos_digitales'       => $pagosDigitales,
            'total_digital'         => $pagosDigitales->sum(),
            'ventas_global'         => $ventasValidas->sum('total'),
            'ventas_count'          => $ventasValidas->count(),
            'movimientos_count'     => $movimientos->count(),
        ];

        $empresa = EmpresaConfiguracion::where('estado', 1)->first();

        return view('sesion_caja.ticket', compact('sesionCaja', 'totales', 'empresa'));
    }
}