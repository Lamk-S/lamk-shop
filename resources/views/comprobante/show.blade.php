@extends('layouts.app')
@section('title', 'Detalle de Comprobante')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.025em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before { content: "›"; color: #94a3b8; font-size: 1.1rem; vertical-align: middle; }
    .main-card { border: 0; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02); overflow: hidden; background: #ffffff; }
    .card-gradient-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #edf2f7; position: relative; }
    .accent-strip { position: absolute; top: 0; left: 0; right: 0; height: 5px; }
    .accent-strip.active { background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%); }
    .accent-strip.inactive { background: linear-gradient(90deg, #94a3b8 0%, #ef4444 100%); }
    .kpi-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; height: 100%; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; }
    .kpi-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: transparent; transition: all 0.25s ease; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05); border-color: #cbd5e1; }
    .kpi-card.primary:hover::before { background: #3b82f6; }
    .kpi-card.success:hover::before { background: #10b981; }
    .kpi-card.info:hover::before { background: #06b6d4; }
    .kpi-card.warning:hover::before { background: #f59e0b; }
    .kpi-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .08em; color: #64748b; font-weight: 700; margin-bottom: .5rem; display: flex; align-items: center; gap: 0.5rem; }
    .kpi-value { font-size: 1.25rem; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .display-number-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 1.25rem; padding: 2rem; position: relative; }
    .display-number-title { font-size: .8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .05em; }
    .display-number-value { font-family: monospace; font-size: 2.25rem; font-weight: 700; color: #1e3a8a; letter-spacing: 0.05em; }
    .section-divider-title { font-size: .85rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .1em; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; }
    .section-divider-title::after { content: ""; flex: 1; height: 1px; background: #e2e8f0; }
    .audit-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; transition: background-color 0.2s ease; }
    .audit-row:hover { background: #f1f5f9; }
    .btn-custom { border-radius: 0.75rem; font-weight: 600; padding: 0.6rem 1.25rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s ease; }
    .btn-custom:hover { transform: translateY(-1px); }
    @media print {
        body { background: #ffffff !important; color: #000000 !important; font-size: 12px; }
        .non-printable, .btn, .btn-custom, footer, .sb-topnav, .sb-sidenav, .breadcrumb { display: none !important; }
        #layoutSidenav_content { padding: 0 !important; margin: 0 !important; width: 100% !important; background: transparent !important; }
        .main-card { border: none !important; box-shadow: none !important; background: transparent !important; }
        .kpi-card { border: 1px solid #000000 !important; box-shadow: none !important; background: transparent !important; break-inside: avoid; }
        .display-number-box { background: transparent !important; border: 1px solid #000000 !important; }
        .display-number-value { color: #000000 !important; }
        .print-header-brand { display: block !important; text-center; border-bottom: 2px solid #000000; padding-bottom: 10px; margin-bottom: 30px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 non-printable">
        <div>
            <h2 class="page-title mb-0">Detalle de Comprobante</h2>
            <ol class="breadcrumb breadcrumb-custom mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('comprobantes.index') }}" class="text-decoration-none text-muted">Comprobantes</a></li>
                <li class="breadcrumb-item active text-dark fw-medium">Configuración de Serie</li>
            </ol>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('comprobantes.index') }}" class="btn btn-light border btn-custom shadow-sm text-secondary">
                <i class="fas fa-arrow-left"></i> Regresar al listado
            </a>
        </div>
    </div>

    <div class="card main-card mx-auto" style="max-width: 1000px;">
        <div class="accent-strip {{ !$comprobante->trashed() && (int)$comprobante->estado === 1 ? 'active' : 'inactive' }}"></div>

        <div class="print-header-brand d-none text-center">
            <h1 class="fw-bold mb-1">LAMK SPORTS</h1>
            <p class="text-uppercase tracking-wider small text-muted mb-0">Especificaciones Técnicas de Control Tributario e Interno</p>
        </div>

        <div class="card-header card-gradient-header p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                <div class="d-flex align-items-center gap-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-inner" style="width: 60px; height: 60px; min-width: 60px;">
                        <i class="fa-solid fa-receipt fs-3"></i>
                    </div>
                    <div>
                        <span class="text-primary fw-bold text-uppercase small tracking-widest">Tipo de Comprobante</span>
                        <h3 class="mb-0 fw-black text-dark text-uppercase mt-1">{{ str_replace('_', ' ', $comprobante->tipo_comprobante) }}</h3>
                        <div class="text-secondary small mt-1 d-flex align-items-center gap-2 flex-wrap">
                            <span><i class="fas fa-fingerprint me-1 text-muted"></i> Serie: <b class="text-dark">{{ $comprobante->serie }}</b></span>
                            <span class="text-slate-300">•</span>
                            <span><i class="fas fa-exchange-alt me-1 text-muted"></i> Flujo: <b class="text-dark">{{ $comprobante->uso_comprobante }}</b></span>
                        </div>
                    </div>
                </div>
                <div class="text-md-end d-flex flex-column align-items-md-end gap-2">
                    <span class="badge bg-slate-100 text-secondary border px-3 py-2 fw-mono fs-7">ID REGISTRO: #{{ str_pad($comprobante->id, 6, '0', STR_PAD_LEFT) }}</span>
                    @if(!$comprobante->trashed() && (int) $comprobante->estado === 1)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill"><i class="fas fa-circle-check me-1"></i> Operativo / Activo</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill"><i class="fas fa-circle-xmark me-1"></i> Desactivado / Inactivo</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            
            <div class="section-divider-title">
                <span><i class="fas fa-sliders-h text-muted me-2"></i>Propiedades de Configuración</span>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card primary">
                        <div class="kpi-label"><i class="fas fa-file-invoice text-primary"></i> Categoría</div>
                        <div class="kpi-value text-capitalize">{{ mb_strtolower(str_replace('_', ' ', $comprobante->tipo_comprobante)) }}</div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card info">
                        <div class="kpi-label"><i class="fas fa-barcode text-info"></i> Prefijo Serie</div>
                        <div class="kpi-value text-primary fw-mono">{{ $comprobante->serie }}</div>
                    </div>
                </div>

                @php
                    $isVenta = $comprobante->uso_comprobante === 'VENTA';
                    $usoColor = $isVenta ? 'success' : 'warning';
                    $usoIcon = $isVenta ? 'fa-cart-arrow-down' : 'fa-clipboard-check';
                @endphp
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card {{ $usoColor }}">
                        <div class="kpi-label"><i class="fas {{ $usoIcon }} text-{{ $usoColor }}"></i> Dirección</div>
                        <div class="kpi-value text-{{ $usoColor }}">{{ $isVenta ? 'Ventas' : 'Compras' }}</div>
                    </div>
                </div>

                @php
                    $isProd = $comprobante->ambiente === 'PRODUCCION';
                    $ambColor = $isProd ? 'success' : 'secondary';
                    $ambIcon = $isProd ? 'fa-circle-dot text-danger animate-pulse' : 'fa-vial';
                @endphp
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-label"><i class="fas {{ $ambIcon }}"></i> Entorno SUNAT</div>
                        <div class="kpi-value text-{{ $isProd ? 'dark' : 'muted' }}">{{ $isProd ? 'Producción' : 'Simulación' }}</div>
                    </div>
                </div>
            </div>

            <div class="section-divider-title">
                <span><i class="fas fa-arrow-up-1-9 text-muted me-2"></i>Estado de la Numeración</span>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="display-number-box h-100 d-flex flex-column justify-content-center">
                        <div class="display-number-title mb-2"><i class="fas fa-calculator text-primary me-1"></i> Siguiente Correlativo Disponible</div>
                        <div class="display-number-value">{{ str_pad($comprobante->correlativo_actual, 8, '0', STR_PAD_LEFT) }}</div>
                        <div class="small text-muted mt-2 mb-0">Este identificador numérico será asignado de forma atómica a la próxima transacción generada.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="kpi-card border-start border-4 border-primary bg-opacity-10">
                        <div class="kpi-label text-dark fw-bold"><i class="fas fa-eye text-primary"></i> Previsualización del Formato Impreso</div>
                        <div class="bg-dark text-white p-3 rounded-3 font-monospace mt-2 text-center shadow-sm fs-4 tracking-wider">
                            {{ $comprobante->serie }}-{{ str_pad($comprobante->correlativo_actual, 8, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="small text-muted mt-3 mb-0">Estructura final estándar requerida para la visualización del cliente en formatos PDF o tickets físicos.</div>
                    </div>
                </div>
            </div>

            <div class="section-divider-title">
                <span><i class="fas fa-clock-rotate-left text-muted me-2"></i>Línea de Tiempo Interna</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="audit-row d-flex align-items-center gap-3">
                        <div class="bg-white border rounded-3 p-2 text-primary shadow-sm">
                            <i class="fas fa-calendar-plus fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small uppercase tracking-wide fw-bold" style="font-size: 0.7rem;">Fecha de Registro Inicial</span>
                            <span class="text-dark fw-semibold">{{ $comprobante->created_at?->format('d \d\e F, Y — H:i') ?? 'Sin registro' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="audit-row d-flex align-items-center gap-3">
                        <div class="bg-white border rounded-3 p-2 text-warning shadow-sm">
                            <i class="fas fa-pen-to-square fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small uppercase tracking-wide fw-bold" style="font-size: 0.7rem;">Última Modificación del Sistema</span>
                            <span class="text-dark fw-semibold">{{ $comprobante->updated_at?->format('d \d\e F, Y — H:i') ?? 'Sin modificaciones' }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection