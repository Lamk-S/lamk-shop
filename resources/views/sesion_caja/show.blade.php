@extends('layouts.app')
@section('title', 'Ticket de Arqueo de Caja')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .glass-card { border: 0; border-radius: 1.25rem; box-shadow: 0 0.5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; background: #fff; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .summary-box { background: #f8fafc; border: 1px solid rgba(148, 163, 184, .16); border-radius: 1rem; padding: 1.25rem; height: 100%; transition: transform .15s ease; }
    .summary-title { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.4rem; }
    .summary-value { font-size: 1.25rem; font-weight: 800; font-family: monospace; letter-spacing: -0.02em; }
    .section-title { font-size: .9rem; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: .06em; }
    .table-soft thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    @media print {
        body { background: #fff !important; font-size: 11pt; color: #000; }
        .no-print, .navbar, .sidebar, footer, .btn { display: none !important; }
        .glass-card { box-shadow: none !important; border: 1px solid #ddd !important; border-radius: 0 !important; margin-bottom: 20px !important; }
        .summary-box { background: #fff !important; border: 1px solid #ccc !important; border-radius: 0 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
        .container-fluid { padding: 0 !important; }
        .page-title { font-size: 18pt; text-align: center; margin-bottom: 20px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
        <div>
            <h2 class="page-title mb-0">Documento de Arqueo</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none text-muted">Auditoría de Turnos</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Detalle y Cuadre</li>
            </ol>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light fw-bold shadow-sm rounded-pill border px-4">
                <i class="fas fa-arrow-left me-2"></i>Regresar
            </a>
            <button onclick="window.print()" class="btn btn-dark fw-bold shadow-sm rounded-pill px-4">
                <i class="fas fa-print me-2"></i>Emitir Ticket
            </button>
        </div>
    </div>

    @php
        $saldoInicial = (float) $sesionCaja->saldo_inicial;
        $saldoEsperado = (float) ($sesionCaja->saldo_final_esperado ?? 0);
        $saldoDeclarado = (float) ($sesionCaja->saldo_final_declarado ?? 0);
        $diferencia = (float) ($sesionCaja->diferencia ?? 0);
        
        $totalIngresos = (float) $sesionCaja->movimientosCaja->where('tipo', 'INGRESO')->where('origen', '!=', 'APERTURA')->sum('monto');
        $totalEgresos = (float) $sesionCaja->movimientosCaja->where('tipo', 'EGRESO')->sum('monto');
        
        // Ventas consolidadas válidas
        $ventasValidas = $sesionCaja->ventas->where('estado_documento', '!=', 'ANULADA');
        $totalVentas = (float) $ventasValidas->sum('total');
        $totalMovimientos = $sesionCaja->movimientosCaja->count();
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card glass-card h-100">
                <div class="card-header soft-header p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center border border-primary border-opacity-25" style="width: 56px; height: 56px;">
                                <i class="fa-solid fa-cash-register fs-4"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Turno #{{ str_pad($sesionCaja->id, 5, '0', STR_PAD_LEFT) }}</h4>
                                <div class="text-muted small fw-medium mt-1">
                                    <i class="fa-solid fa-store me-1"></i> {{ $sesionCaja->caja?->nombre ?? 'N/A' }} 
                                    <span class="mx-2">|</span> 
                                    <i class="fa-solid fa-user-tie me-1"></i> {{ $sesionCaja->user?->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($sesionCaja->estado_sesion === 'ABIERTA')
                                <span class="badge bg-success px-4 py-2 rounded-pill fs-7 shadow-sm">TURNO ACTIVO</span>
                            @elseif($sesionCaja->estado_sesion === 'CERRADA')
                                <span class="badge bg-secondary px-4 py-2 rounded-pill fs-7 shadow-sm">TURNO CERRADO</span>
                            @else
                                <span class="badge bg-danger px-4 py-2 rounded-pill fs-7 shadow-sm">TURNO ANULADO</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 bg-light bg-opacity-50">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 border bg-white h-100 shadow-sm border-start border-4 border-primary">
                                <div class="text-muted small text-uppercase fw-bold mb-1">Apertura (Base + Anterior)</div>
                                <div class="fs-3 fw-bold text-dark font-monospace">S/ {{ number_format($saldoInicial, 2) }}</div>
                                <div class="text-muted small mt-1">Fecha: {{ $sesionCaja->fecha_hora_apertura ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_apertura)->format('d/m/Y H:i') : '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 border bg-white h-100 shadow-sm border-start border-4 border-info">
                                <div class="text-muted small text-uppercase fw-bold mb-1">Cálculo del Sistema</div>
                                <div class="fs-3 fw-bold text-primary font-monospace">S/ {{ number_format($saldoEsperado, 2) }}</div>
                                <div class="text-muted small mt-1">Lo que debería haber en gaveta</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 border bg-white h-100 shadow-sm border-start border-4 {{ $diferencia === 0.0 ? 'border-success' : 'border-danger' }}">
                                <div class="text-muted small text-uppercase fw-bold mb-1">Declaración del Cajero</div>
                                <div class="fs-3 fw-bold text-dark font-monospace">S/ {{ number_format($saldoDeclarado, 2) }}</div>
                                <div class="text-muted small mt-1">Fecha: {{ $sesionCaja->fecha_hora_cierre ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_cierre)->format('d/m/Y H:i') : 'En curso...' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 border bg-white shadow-sm d-flex justify-content-between align-items-center h-100">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold mb-1">Estado de Cuadre</div>
                                    @if($sesionCaja->estado_sesion === 'ABIERTA')
                                        <div class="fs-4 fw-bold text-muted">Aún no se rinde caja</div>
                                    @else
                                        <div class="fs-4 fw-bold {{ $diferencia === 0.0 ? 'text-success' : 'text-danger' }}">
                                            S/ {{ number_format($diferencia, 2) }}
                                        </div>
                                        <div class="fw-medium small {{ $diferencia === 0.0 ? 'text-success' : 'text-danger' }}">
                                            @if($diferencia === 0.0) <i class="fas fa-check-circle me-1"></i> Exacto, sin diferencias.
                                            @elseif($diferencia > 0) <i class="fas fa-exclamation-triangle me-1"></i> Cajero entregó sobrante.
                                            @else <i class="fas fa-times-circle me-1"></i> Faltante en gaveta (deuda).
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="opacity-25"><i class="fa-solid fa-scale-balanced fa-3x {{ $diferencia === 0.0 ? 'text-success' : 'text-danger' }}"></i></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row g-2 h-100">
                                <div class="col-6">
                                    <div class="summary-box shadow-sm d-flex flex-column justify-content-center">
                                        <div class="summary-title text-success"><i class="fas fa-arrow-up me-1"></i>Ingresos Extra</div>
                                        <div class="summary-value text-success font-monospace">+S/{{ number_format($totalIngresos, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="summary-box shadow-sm d-flex flex-column justify-content-center">
                                        <div class="summary-title text-danger"><i class="fas fa-arrow-down me-1"></i>Egresos Extra</div>
                                        <div class="summary-value text-danger font-monospace">-S/{{ number_format($totalEgresos, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($sesionCaja->observacion_apertura || $sesionCaja->observacion_cierre)
                        <div class="row g-3 mt-1">
                            @if($sesionCaja->observacion_apertura)
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 border bg-white h-100">
                                        <div class="fw-bold text-primary small text-uppercase mb-1"><i class="fas fa-comment-dots me-1"></i>Nota de Apertura</div>
                                        <p class="mb-0 text-muted small fst-italic">{{ $sesionCaja->observacion_apertura }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($sesionCaja->observacion_cierre)
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 border bg-white h-100">
                                        <div class="fw-bold text-danger small text-uppercase mb-1"><i class="fas fa-comment-dots me-1"></i>Nota de Cierre</div>
                                        <p class="mb-0 text-muted small fst-italic">{{ $sesionCaja->observacion_cierre }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card glass-card h-100">
                <div class="card-header bg-dark text-white p-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2"></i>Rendimiento Comercial</h5>
                </div>
                <div class="card-body p-4 bg-light bg-opacity-50 d-flex flex-column gap-3">
                    <div class="summary-box shadow-sm text-center border-primary border-opacity-25">
                        <div class="summary-title text-primary mb-2">Total Facturado (Ventas)</div>
                        <div class="fs-1 fw-bold text-dark font-monospace">S/ {{ number_format($totalVentas, 2) }}</div>
                        <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mt-2 rounded-pill px-3">
                            Incluye todos los medios de pago
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded-3 border shadow-sm mt-auto">
                        <span class="text-muted fw-bold text-uppercase small">Tickets Emitidos</span>
                        <span class="fs-4 fw-bold text-dark font-monospace">{{ $ventasValidas->count() }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded-3 border shadow-sm">
                        <span class="text-muted fw-bold text-uppercase small">Transacciones Caja</span>
                        <span class="fs-4 fw-bold text-dark font-monospace">{{ $totalMovimientos }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card glass-card h-100">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-money-bill-transfer text-primary me-2"></i>Historial de Gaveta</h6>
                    <span class="badge bg-light text-dark border">{{ $totalMovimientos }} Regs.</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-soft mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Operación</th>
                                    <th class="text-center">Nat.</th>
                                    <th class="text-end">Importe</th>
                                    <th class="text-end pe-4">Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sesionCaja->movimientosCaja as $item)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark fs-7">{{ $item->origen }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 180px;" title="{{ $item->descripcion }}">{{ $item->descripcion }}</div>
                                        </td>
                                        <td class="text-center py-3">
                                            @if($item->tipo === 'INGRESO')
                                                <i class="fas fa-arrow-up text-success"></i>
                                            @else
                                                <i class="fas fa-arrow-down text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-end py-3 fw-bold font-monospace fs-7 {{ $item->tipo === 'INGRESO' ? 'text-success' : 'text-danger' }}">
                                            {{ $item->tipo === 'INGRESO' ? '+' : '-' }}{{ number_format((float) $item->monto, 2) }}
                                        </td>
                                        <td class="text-end pe-4 py-3 text-muted font-monospace fs-7">
                                            {{ optional($item->created_at)->format('H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">Sin movimientos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card glass-card h-100">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-shopping-bag text-primary me-2"></i>Ventas Despachadas</h6>
                    <span class="badge bg-light text-dark border">{{ $ventasValidas->count() }} Tickets</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-soft mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Documento</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end pe-4">Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventasValidas as $venta)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark font-monospace fs-7">
                                                {{ trim(($venta->serie ?? '') . '-' . ($venta->correlativo ?? '')) ?: 'TICKET' }}
                                            </div>
                                            <div class="small text-muted">{{ $venta->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'N/A' }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-medium text-dark text-truncate fs-7" style="max-width: 150px;">
                                                {{ $venta->cliente?->persona?->razon_social ?? trim(($venta->cliente?->persona?->nombres ?? '') . ' ' . ($venta->cliente?->persona?->apellidos ?? '')) ?: 'Público General' }}
                                            </div>
                                        </td>
                                        <td class="text-end py-3 fw-bold text-dark font-monospace fs-7">
                                            S/ {{ number_format((float) $venta->total, 2) }}
                                        </td>
                                        <td class="text-end pe-4 py-3 text-muted font-monospace fs-7">
                                            {{ optional($venta->fecha_emision)->format('H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No se registraron ventas en esta sesión</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5 pt-5 d-none d-print-flex">
        <div class="col-6 text-center">
            <div style="border-top: 1px solid #000; margin: 0 40px; padding-top: 10px;">
                <strong>Firma del Cajero</strong><br>
                {{ $sesionCaja->user?->name ?? '_____________________' }}
            </div>
        </div>
        <div class="col-6 text-center">
            <div style="border-top: 1px solid #000; margin: 0 40px; padding-top: 10px;">
                <strong>Firma del Administrador / Tesorería</strong><br>
                {{ $sesionCaja->userCierre?->name ?? '_____________________' }}
            </div>
        </div>
    </div>
</div>
@endsection