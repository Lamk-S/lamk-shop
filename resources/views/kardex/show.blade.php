@extends('layouts.app')
@section('title', 'Certificado de Kardex')

@push('css')
<style>
    @media print {
        body { background-color: #fff !important; color: #000 !important; font-size: 11pt; }
        .page-break-avoid { page-break-inside: avoid; }
    }
</style>
@endpush

@section('content')
@php
    $variante = $kardex->productoVariante;
    $producto = $variante?->producto;
    $talla = $variante?->talla;
    
    $tipo = $kardex->tipo_transaccion->value ?? $kardex->tipo_transaccion;
    
    $badgeProps = match ($tipo) {
        'COMPRA', 'APERTURA', 'TRANSFERENCIA' => ['color' => 'success', 'icon' => 'fa-arrow-down'],
        'VENTA', 'MERMA', 'VENCIDO' => ['color' => 'danger', 'icon' => 'fa-arrow-up'],
        'AJUSTE', 'ANULACION' => ['color' => 'warning', 'icon' => 'fa-sliders-h'],
        'DEVOLUCION' => ['color' => 'info', 'icon' => 'fa-undo'],
        default => ['color' => 'secondary', 'icon' => 'fa-exchange-alt'],
    };
@endphp

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado UI (Oculto en impresión) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 d-print-none">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Certificado de Movimiento</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kardex.index') }}" class="text-decoration-none text-muted">Kardex</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Doc #{{ str_pad($kardex->id, 6, '0', STR_PAD_LEFT) }}</li>
            </ol>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('kardex.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button onclick="window.print()" class="btn btn-dark shadow-sm">
                <i class="fas fa-print me-2"></i>Generar Comprobante
            </button>
        </div>
    </div>

    <!-- Documento a imprimir / visualizar -->
    <div class="card border-0 shadow-sm rounded-3 mx-auto border-print" style="max-width: 900px;">
        <div class="card-body p-4 p-md-5">
            <!-- Cabecera Impresión (Oculta en UI normal) -->
            <div class="d-none d-print-block text-center border-bottom border-dark pb-3 mb-4">
                <h1 class="fw-bold mb-1">LAMK SPORTS</h1>
                <p class="text-uppercase small mb-0">Certificado Oficial de Movimiento de Almacén</p>
                <div class="text-end small mt-2">Impreso: {{ now()->format('d/m/Y H:i') }}</div>
            </div>

            <!-- Ficha Técnica -->
            <div class="row align-items-center pb-4 border-bottom mb-4 g-4">
                <div class="col-md-6 text-center text-md-start">
                    <div class="text-muted small fw-bold text-uppercase mb-1"><i class="fas fa-hashtag me-1"></i> N° OPERACIÓN</div>
                    <h2 class="fw-bold text-dark mb-2 font-monospace">#{{ str_pad($kardex->id, 8, '0', STR_PAD_LEFT) }}</h2>
                    <span class="badge bg-{{ $badgeProps['color'] }} bg-opacity-10 text-{{ $badgeProps['color'] }} border border-{{ $badgeProps['color'] }} px-3 py-2">
                        <i class="fas {{ $badgeProps['icon'] }} me-1"></i> {{ $tipo }}
                    </span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="text-muted small fw-bold text-uppercase mb-1"><i class="fas fa-box me-1"></i> ARTÍCULO</div>
                    <h4 class="fw-bold text-dark mb-2">{{ $producto?->nombre ?? 'Artículo Invalido' }}</h4>
                    <div class="d-flex justify-content-center justify-content-md-end gap-2 flex-wrap font-monospace small">
                        <span class="border rounded px-2 py-1 bg-light">SKU: {{ $producto?->codigo ?? 'N/A' }}</span>
                        @if($variante)
                            <span class="border border-primary text-primary rounded px-2 py-1 bg-light">VAR: {{ $variante->codigo_variante }}</span>
                            <span class="border rounded px-2 py-1 bg-light">TALLA: {{ $talla?->nombre ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Glosa -->
            <div class="bg-light border rounded-3 p-4 mb-4 page-break-avoid">
                <div class="text-muted small fw-bold text-uppercase mb-2"><i class="fas fa-align-left me-1"></i> Justificación / Glosa</div>
                <p class="fs-6 text-dark mb-0">{{ $kardex->descripcion }}</p>
            </div>

            <!-- KPIs -->
            <div class="row g-3 mb-4 page-break-avoid">
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-light h-100 border-start border-4 border-success">
                        <div class="text-success small fw-bold text-uppercase mb-1"><i class="fas fa-arrow-down me-1"></i> Ingreso</div>
                        <div class="fs-4 fw-bold font-monospace text-dark">{{ number_format($kardex->entrada, 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-light h-100 border-start border-4 border-danger">
                        <div class="text-danger small fw-bold text-uppercase mb-1"><i class="fas fa-arrow-up me-1"></i> Salida</div>
                        <div class="fs-4 fw-bold font-monospace text-dark">{{ number_format($kardex->salida, 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-dark text-white h-100 border-start border-4 border-secondary">
                        <div class="text-white-50 small fw-bold text-uppercase mb-1"><i class="fas fa-layer-group me-1"></i> Saldo Final</div>
                        <div class="fs-4 fw-bold font-monospace text-white">{{ number_format($kardex->saldo_posterior, 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 p-3 bg-light h-100 border-start border-4 border-info">
                        <div class="text-muted small fw-bold text-uppercase mb-1"><i class="fas fa-coins me-1"></i> Costo Ref.</div>
                        <div class="fs-5 fw-bold font-monospace text-dark mt-2">S/ {{ number_format($kardex->costo_unitario, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Trazabilidad -->
            <div class="page-break-avoid">
                <h6 class="fw-bold mb-3 text-dark text-uppercase small"><i class="fas fa-clipboard-check text-muted me-2"></i>Trazabilidad de Sistema</h6>
                <div class="table-responsive border rounded-3">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td class="ps-4 py-3 bg-light w-25 text-muted small fw-bold text-uppercase">Operador</td>
                                <td class="pe-4 py-3 fw-medium text-dark">
                                    {{ $kardex->user?->name ?? 'Sistema Automático' }} 
                                    <span class="text-muted ms-2 small">{{ $kardex->user?->email ? '<'.$kardex->user->email.'>' : '' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 py-3 bg-light text-muted small fw-bold text-uppercase">Marca de Tiempo</td>
                                <td class="pe-4 py-3 fw-medium font-monospace text-dark">
                                    {{ optional($kardex->created_at)->format('d/m/Y') }} - {{ optional($kardex->created_at)->format('H:i:s') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Firmas (Solo visibles al imprimir) -->
            <div class="row mt-5 pt-5 d-none d-print-flex page-break-avoid">
                <div class="col-6 text-center">
                    <div style="border-top: 1px solid #000; margin: 0 40px; padding-top: 10px;">
                        <strong class="small text-uppercase">Almacén / Logística</strong>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div style="border-top: 1px solid #000; margin: 0 40px; padding-top: 10px;">
                        <strong class="small text-uppercase">Auditor / Supervisor</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection