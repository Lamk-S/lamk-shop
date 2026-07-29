@extends('layouts.app')
@section('title', 'Arqueo de Caja')

@push('css')
<style>
    .page-title { font-weight: 700; color: #212529; }
    .fs-7 { font-size: 0.875rem; }
    .table-corporate th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; border-bottom: 2px solid #dee2e6; letter-spacing: 0.02em; }
    .table-corporate td { vertical-align: middle; color: #495057; }
    .summary-box { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 0.5rem; padding: 1.25rem; height: 100%; }
    
    @media print {
        body { background: #fff !important; font-size: 10pt; color: #000; }
        .no-print, .navbar, .sidebar, footer { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; margin-bottom: 1rem !important; }
        .summary-box { background: transparent !important; border: 1px solid #ccc !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
        .page-title { font-size: 16pt; text-align: center; margin-bottom: 15px; }
        .container-fluid { padding: 0 !important; }
    }
</style>
@endpush

@section('content')
@php
    $saldoInicial = $sesionCaja->saldo_inicial;
    $saldoEsperado = $sesionCaja->saldo_final_esperado ?? 0;
    $saldoDeclarado = $sesionCaja->saldo_final_declarado ?? 0;
    $diferencia = $sesionCaja->diferencia ?? 0;
@endphp

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
        <div>
            <h2 class="page-title mb-0 fs-3">Documento de Arqueo</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none text-muted">Auditoría</a></li>
                    <li class="breadcrumb-item active fw-medium text-dark">Detalle del Turno</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light shadow-sm border px-3">
                <i class="fas fa-arrow-left me-2"></i>Regresar
            </a>
            <button onclick="window.print()" class="btn btn-dark shadow-sm px-3">
                <i class="fas fa-print me-2"></i>Imprimir Ticket
            </button>
        </div>
    </div>

    <!-- Panel Superior: Identificación del Turno -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 p-md-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-light border text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-cash-register fs-5"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark fs-5">Turno #{{ str_pad($sesionCaja->id, 5, '0', STR_PAD_LEFT) }}</h4>
                    <div class="text-muted small fw-medium mt-1">
                        <i class="fa-solid fa-store me-1"></i> {{ $sesionCaja->caja?->nombre ?? 'N/A' }} 
                        <span class="mx-2 text-light">|</span> 
                        <i class="fa-solid fa-user-tie me-1"></i> Operador: {{ $sesionCaja->user?->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div>
                @if($sesionCaja->estado_sesion->value === 'ABIERTA')
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fs-7">EN CURSO (ABIERTA)</span>
                @elseif($sesionCaja->estado_sesion->value === 'CERRADA')
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2 rounded-pill fs-7">TURNO CERRADO</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fs-7">TURNO ANULADO</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Indicadores Principales del Cuadre -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Saldo Apertura</div>
                    <div class="fs-4 fw-bold text-dark font-monospace">S/ {{ number_format($saldoInicial, 2) }}</div>
                    <div class="text-muted small mt-1">
                        <i class="far fa-clock me-1"></i> {{ $sesionCaja->fecha_hora_apertura ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_apertura)->format('d/m/Y H:i') : '—' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Cálculo del Sistema</div>
                    <div class="fs-4 fw-bold text-primary font-monospace">S/ {{ number_format($saldoEsperado, 2) }}</div>
                    <div class="text-muted small mt-1">
                        Efectivo esperado en gaveta al cierre
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Declarado por Cajero</div>
                    <div class="fs-4 fw-bold text-dark font-monospace">S/ {{ number_format($saldoDeclarado, 2) }}</div>
                    <div class="text-muted small mt-1">
                        <i class="far fa-clock me-1"></i> {{ $sesionCaja->fecha_hora_cierre ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_cierre)->format('d/m/Y H:i') : 'Turno en curso' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Cuadre y Totales -->
    <div class="row g-4 mb-4">
        <!-- Estado de Cuadre -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1">Estado de Cuadre</div>
                        @if($sesionCaja->estado_sesion->value === 'ABIERTA')
                            <div class="fs-4 fw-bold text-secondary">Aún no se rinde caja</div>
                        @else
                            <div class="fs-3 fw-bold {{ $diferencia === 0.0 ? 'text-success' : 'text-danger' }} font-monospace">
                                {{ $diferencia > 0 ? '+' : '' }}S/ {{ number_format($diferencia, 2) }}
                            </div>
                            <div class="fw-medium small mt-1 {{ $diferencia === 0.0 ? 'text-success' : 'text-danger' }}">
                                @if($diferencia === 0.0) 
                                    <i class="fas fa-check-circle me-1"></i> Cuadre exacto.
                                @elseif($diferencia > 0) 
                                    <i class="fas fa-exclamation-triangle me-1"></i> Hay un sobrante en gaveta.
                                @else 
                                    <i class="fas fa-times-circle me-1"></i> Faltante (Deuda del cajero).
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="opacity-25">
                        <i class="fa-solid fa-scale-balanced fa-4x {{ $sesionCaja->estado_sesion->value !== 'ABIERTA' && $diferencia === 0.0 ? 'text-success' : ($sesionCaja->estado_sesion->value !== 'ABIERTA' ? 'text-danger' : 'text-secondary') }}"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rendimiento Comercial -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-dark text-white">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-sm-6 mb-3 mb-sm-0 border-sm-end border-secondary">
                            <div class="text-uppercase small fw-bold text-white-50 mb-1">Total Ventas Facturadas</div>
                            <div class="fs-3 fw-bold font-monospace">S/ {{ number_format($totales['ventas'] ?? 0, 2) }}</div>
                            <div class="small text-white-50 mt-1">Total ingresado por ventas</div>
                        </div>
                        <div class="col-sm-6 ps-sm-4 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white-50 small text-uppercase">Tickets Emitidos:</span>
                                <strong class="font-monospace">{{ $totales['ventas_count'] ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-white-50 small text-uppercase">Movimientos Caja:</span>
                                <strong class="font-monospace">{{ $totales['movimientos_count'] ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notas y Observaciones -->
    @if($sesionCaja->observacion_apertura || $sesionCaja->observacion_cierre)
    <div class="row g-3 mb-4">
        @if($sesionCaja->observacion_apertura)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
                <div class="card-body p-3">
                    <div class="fw-bold text-primary small text-uppercase mb-2"><i class="fas fa-comment-dots me-2"></i>Nota de Apertura</div>
                    <p class="mb-0 text-muted small">{{ $sesionCaja->observacion_apertura }}</p>
                </div>
            </div>
        </div>
        @endif
        @if($sesionCaja->observacion_cierre)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
                <div class="card-body p-3">
                    <div class="fw-bold text-danger small text-uppercase mb-2"><i class="fas fa-comment-dots me-2"></i>Nota de Cierre</div>
                    <p class="mb-0 text-muted small">{{ $sesionCaja->observacion_cierre }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Tablas de Detalle Operativo -->
    <div class="row g-4 mb-4">
        <!-- Movimientos -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-exchange-alt text-muted me-2"></i>Historial de Gaveta</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-corporate mb-0 text-nowrap">
                            <thead>
                                <tr>
                                    <th class="ps-3">Operación</th>
                                    <th class="text-end">Importe</th>
                                    <th class="text-end pe-3">Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sesionCaja->movimientosCaja as $item)
                                    <tr>
                                        <td class="ps-3 py-2">
                                            <div class="fw-medium text-dark fs-7">{{ $item->origen->value ?? $item->origen }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;" title="{{ $item->descripcion }}">{{ $item->descripcion }}</div>
                                        </td>
                                        <td class="text-end py-2 fw-bold font-monospace fs-7 {{ $item->tipo->value === 'INGRESO' ? 'text-success' : 'text-danger' }}">
                                            {{ $item->tipo->value === 'INGRESO' ? '+' : '-' }}S/ {{ number_format((float) $item->monto, 2) }}
                                        </td>
                                        <td class="text-end pe-3 py-2 text-muted font-monospace fs-7">
                                            {{ optional($item->created_at)->format('H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted small">Sin movimientos registrados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-shopping-bag text-muted me-2"></i>Tickets Emitidos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-corporate mb-0 text-nowrap">
                            <thead>
                                <tr>
                                    <th class="ps-3">Documento</th>
                                    <th>Cliente</th>
                                    <th class="text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventasValidas ?? [] as $venta)
                                    <tr>
                                        <td class="ps-3 py-2">
                                            <div class="fw-medium text-dark font-monospace fs-7">
                                                {{ trim(($venta->serie ?? '') . '-' . ($venta->correlativo ?? '')) ?: 'TICKET' }}
                                            </div>
                                            <div class="small text-muted">{{ $venta->pagos->pluck('metodo_pago')->map(fn($m) => $m->value ?? $m)->unique()->implode(', ') ?: 'N/A' }}</div>
                                        </td>
                                        <td class="py-2">
                                            <div class="small text-dark text-truncate" style="max-width: 150px;">
                                                {{ $venta->cliente?->persona?->razon_social ?? trim(($venta->cliente?->persona?->nombres ?? '') . ' ' . ($venta->cliente?->persona?->apellidos ?? '')) ?: 'Público General' }}
                                            </div>
                                        </td>
                                        <td class="text-end pe-3 py-2 fw-bold text-dark font-monospace fs-7">
                                            S/ {{ number_format((float) $venta->total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted small">No se registraron ventas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Firmas (Solo visible en impresión) -->
    <div class="row mt-5 pt-5 d-none d-print-flex">
        <div class="col-6 text-center">
            <div style="border-top: 1px solid #000; margin: 0 40px; padding-top: 10px;">
                <strong>Firma del Cajero</strong><br>
                <span class="small">{{ $sesionCaja->user?->name ?? '_____________________' }}</span>
            </div>
        </div>
        <div class="col-6 text-center">
            <div style="border-top: 1px solid #000; margin: 0 40px; padding-top: 10px;">
                <strong>Administración / Tesorería</strong><br>
                <span class="small">{{ $sesionCaja->userCierre?->name ?? '_____________________' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection