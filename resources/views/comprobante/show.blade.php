@extends('layouts.app')
@section('title', 'Detalle de Comprobante')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 d-print-none">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Detalle de Comprobante</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('comprobantes.index') }}" class="text-decoration-none text-muted">Comprobantes</a></li>
                <li class="breadcrumb-item active text-dark fw-medium">Configuración de Serie</li>
            </ol>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('comprobantes.index') }}" class="btn btn-light border shadow-sm text-secondary px-4 fw-medium">
                <i class="fas fa-arrow-left me-2"></i> Regresar
            </a>
        </div>
    </div>

    <!-- Header exclusivo de impresión -->
    <div class="d-none d-print-block text-center mb-4 pb-2 border-bottom border-2 border-dark">
        <h1 class="fw-bold mb-1 fs-3">LAMK SPORTS</h1>
        <p class="text-uppercase small text-muted mb-0">Especificaciones Técnicas de Control Tributario e Interno</p>
    </div>

    @php
        $tipoVal = $comprobante->tipo_comprobante->value ?? $comprobante->tipo_comprobante;
        $usoVal  = $comprobante->uso_comprobante->value ?? $comprobante->uso_comprobante;
        $ambVal  = $comprobante->ambiente->value ?? $comprobante->ambiente;
        
        $isVenta  = $usoVal === 'VENTA';
        $usoColor = $isVenta ? 'success' : 'warning';
        $usoIcon  = $isVenta ? 'fa-cart-arrow-down' : 'fa-clipboard-check';
        
        $isProd   = $ambVal === 'PRODUCCION';
        $ambColor = $isProd ? 'danger' : 'secondary';
        $ambIcon  = $isProd ? 'fa-circle-dot text-danger' : 'fa-vial';
    @endphp

    <div class="card border-0 shadow-sm rounded-4 mx-auto overflow-hidden" style="max-width: 1000px;">
        <div class="card-header bg-light bg-opacity-50 border-bottom p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                <div class="d-flex align-items-center gap-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 65px; height: 65px;">
                        <i class="fa-solid fa-receipt fs-3"></i>
                    </div>
                    <div>
                        <span class="text-primary fw-bold text-uppercase small">Tipo de Comprobante</span>
                        <h3 class="mb-0 fw-bolder text-dark text-uppercase mt-1">{{ str_replace('_', ' ', $tipoVal) }}</h3>
                        <div class="text-muted small mt-1 d-flex align-items-center gap-2 flex-wrap fw-medium">
                            <span><i class="fas fa-fingerprint me-1"></i> Serie: <b class="text-dark">{{ $comprobante->serie }}</b></span>
                            <span>•</span>
                            <span><i class="fas fa-exchange-alt me-1"></i> Flujo: <b class="text-dark">{{ $usoVal }}</b></span>
                        </div>
                    </div>
                </div>
                <div class="text-md-end d-flex flex-column align-items-md-end gap-2">
                    <span class="badge bg-light text-secondary border px-3 py-2 font-monospace fs-7 shadow-sm">ID REGISTRO: #{{ str_pad($comprobante->id, 6, '0', STR_PAD_LEFT) }}</span>
                    @if(!$comprobante->trashed() && (int) $comprobante->estado === 1)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill"><i class="fas fa-circle-check me-1"></i> Operativo / Activo</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill"><i class="fas fa-circle-xmark me-1"></i> Desactivado / Inactivo</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            
            <h6 class="fw-bold text-muted text-uppercase small mb-4 d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h"></i> Propiedades de Configuración
                <hr class="flex-grow-1 ms-2 mb-0 border-secondary opacity-25">
            </h6>
            
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-white border shadow-sm rounded-4 h-100 border-start border-4 border-primary">
                        <div class="card-body p-4">
                            <div class="text-muted small fw-bold text-uppercase mb-2"><i class="fas fa-file-invoice text-primary me-1"></i> Categoría</div>
                            <div class="fs-5 fw-bold text-dark text-capitalize">{{ mb_strtolower(str_replace('_', ' ', $tipoVal)) }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-white border shadow-sm rounded-4 h-100 border-start border-4 border-info">
                        <div class="card-body p-4">
                            <div class="text-muted small fw-bold text-uppercase mb-2"><i class="fas fa-barcode text-info me-1"></i> Prefijo Serie</div>
                            <div class="fs-4 fw-bold text-primary font-monospace">{{ $comprobante->serie }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card bg-white border shadow-sm rounded-4 h-100 border-start border-4 border-{{ $usoColor }}">
                        <div class="card-body p-4">
                            <div class="text-muted small fw-bold text-uppercase mb-2"><i class="fas {{ $usoIcon }} text-{{ $usoColor }} me-1"></i> Dirección</div>
                            <div class="fs-5 fw-bold text-{{ $usoColor }}">{{ $isVenta ? 'Ventas' : 'Compras' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-white border shadow-sm rounded-4 h-100 border-start border-4 border-{{ $ambColor }}">
                        <div class="card-body p-4">
                            <div class="text-muted small fw-bold text-uppercase mb-2"><i class="fas {{ $ambIcon }} me-1"></i> Entorno SUNAT</div>
                            <div class="fs-5 fw-bold text-{{ $isProd ? 'dark' : 'muted' }}">{{ $isProd ? 'Producción' : 'Simulación' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-muted text-uppercase small mb-4 d-flex align-items-center gap-2">
                <i class="fas fa-arrow-up-1-9"></i> Estado de la Numeración
                <hr class="flex-grow-1 ms-2 mb-0 border-secondary opacity-25">
            </h6>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="bg-light bg-opacity-50 border border-secondary border-opacity-25 rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
                        <div class="text-muted small fw-bold text-uppercase mb-2"><i class="fas fa-calculator text-primary me-1"></i> Siguiente Correlativo Disponible</div>
                        <div class="fs-1 fw-bolder text-primary font-monospace">{{ str_pad($comprobante->correlativo_actual, 8, '0', STR_PAD_LEFT) }}</div>
                        <div class="small text-muted mt-2">Este identificador numérico será asignado de forma atómica a la próxima transacción generada.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="bg-white border shadow-sm rounded-4 p-4 h-100 border-start border-4 border-dark">
                        <div class="text-dark small fw-bold text-uppercase mb-3"><i class="fas fa-eye text-primary me-1"></i> Previsualización del Formato Impreso</div>
                        <div class="bg-dark text-white p-3 rounded-3 font-monospace text-center shadow-sm fs-4 tracking-wider">
                            {{ $comprobante->serie }}-{{ str_pad($comprobante->correlativo_actual, 8, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="small text-muted mt-3">Estructura final estándar requerida para la visualización del cliente en formatos PDF o tickets físicos.</div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-muted text-uppercase small mb-4 d-flex align-items-center gap-2">
                <i class="fas fa-clock-rotate-left"></i> Línea de Tiempo Interna
                <hr class="flex-grow-1 ms-2 mb-0 border-secondary opacity-25">
            </h6>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="bg-light bg-opacity-50 border rounded-4 p-3 d-flex align-items-center gap-3">
                        <div class="bg-white border rounded-circle d-flex align-items-center justify-content-center text-primary shadow-sm" style="width: 45px; height: 45px;">
                            <i class="fas fa-calendar-plus fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small text-uppercase fw-bold">Fecha de Registro Inicial</span>
                            <span class="text-dark fw-semibold">{{ $comprobante->created_at?->format('d \d\e F, Y — H:i') ?? 'Sin registro' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light bg-opacity-50 border rounded-4 p-3 d-flex align-items-center gap-3">
                        <div class="bg-white border rounded-circle d-flex align-items-center justify-content-center text-warning shadow-sm" style="width: 45px; height: 45px;">
                            <i class="fas fa-pen-to-square fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small text-uppercase fw-bold">Última Modificación</span>
                            <span class="text-dark fw-semibold">{{ $comprobante->updated_at?->format('d \d\e F, Y — H:i') ?? 'Sin modificaciones' }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection