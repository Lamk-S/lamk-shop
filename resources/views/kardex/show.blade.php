@extends('layouts.app')
@section('title', 'Certificado de Kardex')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .kardex-card { border: 0; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02); overflow: hidden; background: #ffffff; }
    .invoice-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.35rem; }
    .kpi-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; height: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .kpi-value { font-size: 1.5rem; font-weight: 800; font-family: ui-monospace, monospace; letter-spacing: -0.02em; }
    .table-audit th { background-color: transparent; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; border-bottom: 2px solid #e2e8f0; }
    .table-audit td { border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    @media print {
        body { background-color: #fff !important; color: #000 !important; }
        .non-printable, .sb-topnav, .sb-sidenav, footer { display: none !important; }
        #layoutSidenav_content { padding: 0 !important; margin: 0 !important; width: 100% !important; background: transparent !important; }
        .kardex-card { box-shadow: none !important; border: none !important; border-radius: 0 !important; }
        .kpi-box { border: 1px solid #000 !important; box-shadow: none !important; break-inside: avoid; }
        .print-header-brand { display: block !important; text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 30px; }
        .signature-box { display: flex !important; justify-content: space-around; margin-top: 80px; page-break-inside: avoid; }
        .signature-line { border-top: 1px solid #000; width: 40%; text-align: center; padding-top: 8px; font-weight: bold; font-size: 12px; }
    }
</style>
@endpush

@section('content')

@php
    $variante = $kardex->productoVariante;
    $producto = $variante?->producto;
    $talla = $variante?->talla;
    
    $tipo = $kardex->tipo_transaccion;
    $badgeProps = match ($tipo) {
        'COMPRA', 'APERTURA', 'TRANSFERENCIA' => ['color' => 'success', 'icon' => 'fa-arrow-down'],
        'VENTA', 'MERMA', 'VENCIDO' => ['color' => 'danger', 'icon' => 'fa-arrow-up'],
        'AJUSTE', 'ANULACION' => ['color' => 'warning', 'icon' => 'fa-sliders-h'],
        'DEVOLUCION' => ['color' => 'info', 'icon' => 'fa-undo'],
        default => ['color' => 'secondary', 'icon' => 'fa-exchange-alt'],
    };
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 non-printable">
        <div>
            <h2 class="page-title mb-0">Certificado de Movimiento</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kardex.index') }}" class="text-decoration-none text-muted">Kardex</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Documento #{{ str_pad($kardex->id, 6, '0', STR_PAD_LEFT) }}</li>
            </ol>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('kardex.index') }}" class="btn btn-light border shadow-sm fw-medium">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button onclick="window.print()" class="btn btn-dark shadow-sm fw-bold">
                <i class="fas fa-print me-2"></i>Generar Comprobante Físico
            </button>
        </div>
    </div>

    <div class="card kardex-card mx-auto" style="max-width: 950px;">
        <div class="card-body p-4 p-md-5">
            <div class="print-header-brand d-none">
                <h1 class="fw-black mb-1">LAMK SPORTS</h1>
                <p class="text-uppercase tracking-wider small mb-0">Certificado Oficial de Movimiento de Almacén</p>
                <div class="mt-2 text-end small">Fecha Impresión: {{ now()->format('d/m/Y H:i') }}</div>
            </div>

            <div class="row align-items-center pb-4 border-bottom mb-4 g-4">
                <div class="col-md-6 text-center text-md-start">
                    <div class="invoice-label"><i class="fas fa-hashtag text-muted"></i> NÚMERO DE OPERACIÓN</div>
                    <h2 class="fw-black text-dark mb-2" style="font-family: monospace;">#{{ str_pad($kardex->id, 8, '0', STR_PAD_LEFT) }}</h2>
                    <span class="badge bg-{{ $badgeProps['color'] }} bg-opacity-10 text-{{ $badgeProps['color'] }} border border-{{ $badgeProps['color'] }} px-3 py-2 rounded-pill fs-7 tracking-wider">
                        <i class="fas {{ $badgeProps['icon'] }} me-1"></i> {{ $tipo }}
                    </span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="invoice-label justify-content-center justify-content-md-end"><i class="fas fa-box text-muted"></i> FICHA TÉCNICA DEL ARTÍCULO</div>
                    <h4 class="fw-bold text-primary mb-1">{{ $producto?->nombre ?? 'Artículo Invalido/Eliminado' }}</h4>
                    <div class="d-flex justify-content-center justify-content-md-end gap-2 mt-2 flex-wrap">
                        <span class="badge bg-light text-dark border"><i class="fas fa-barcode text-muted me-1"></i> SK: {{ $producto?->codigo ?? 'N/A' }}</span>
                        @if($variante)
                            <span class="badge bg-light text-primary border"><i class="fas fa-tag text-muted me-1"></i> VR: {{ $variante->codigo_variante }}</span>
                            <span class="badge bg-light text-dark border"><i class="fas fa-ruler text-muted me-1"></i> TL: {{ $talla?->nombre ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-light bg-opacity-50 border border-secondary-subtle rounded-4 p-4 mb-5">
                <div class="invoice-label text-dark"><i class="fas fa-align-left text-muted"></i> Justificación / Glosa del Movimiento</div>
                <p class="fs-5 text-dark mb-0 lh-base">{{ $kardex->descripcion }}</p>
            </div>

            <div class="row g-3 mb-5">
                <div class="col-md-3 col-6">
                    <div class="kpi-box border-success border-opacity-25 bg-success bg-opacity-10 text-success">
                        <div class="invoice-label text-success"><i class="fas fa-arrow-down"></i> Ingreso (+. Unds)</div>
                        <div class="kpi-value">{{ number_format($kardex->entrada, 0) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-box border-danger border-opacity-25 bg-danger bg-opacity-10 text-danger">
                        <div class="invoice-label text-danger"><i class="fas fa-arrow-up"></i> Salida (-. Unds)</div>
                        <div class="kpi-value">{{ number_format($kardex->salida, 0) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-box border-dark border-opacity-25 bg-dark text-white">
                        <div class="invoice-label text-white-50"><i class="fas fa-layer-group"></i> Saldo Final Resultante</div>
                        <div class="kpi-value text-white">{{ number_format($kardex->saldo_posterior, 0) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-box">
                        <div class="invoice-label"><i class="fas fa-file-invoice-dollar text-muted"></i> Costo Unitario Ref.</div>
                        <div class="kpi-value text-dark fs-4 mt-1">S/ {{ number_format($kardex->costo_unitario, 2) }}</div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-3 text-dark text-uppercase tracking-wider fs-7"><i class="fas fa-clipboard-check text-muted me-2"></i>Trazabilidad de Sistema</h6>
            <div class="table-responsive border rounded-3">
                <table class="table table-audit mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-4 py-3 bg-light w-25"><i class="fas fa-user-tie me-2 text-muted"></i>Operador Autorizado</td>
                            <td class="pe-4 py-3 fw-bold text-dark">
                                {{ $kardex->user?->name ?? 'Motor del Sistema Automático' }} 
                                <span class="text-muted fw-normal small ms-2">{{ $kardex->user?->email ? '<'.$kardex->user->email.'>' : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 py-3 bg-light"><i class="fas fa-calendar-alt me-2 text-muted"></i>Marca de Tiempo (Timestamp)</td>
                            <td class="pe-4 py-3 fw-medium text-dark">
                                {{ optional($kardex->created_at)->format('d/m/Y') }} a las {{ optional($kardex->created_at)->format('H:i:s') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="signature-box d-none">
                <div class="signature-line">Firma y Sello del Almacenero</div>
                <div class="signature-line">Firma y Sello del Auditor/Supervisor</div>
            </div>
        </div>
    </div>
</div>
@endsection