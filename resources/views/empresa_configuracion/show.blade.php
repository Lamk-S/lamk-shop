@extends('layouts.app')
@section('title', 'Configuración de Empresa')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Perfil de Empresa</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Configuración Global</li>
            </ol>
        </div>
        <a href="{{ route('empresa-configuracion.edit', $empresaConfiguracion) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-edit me-2"></i>Editar Datos
        </a>
    </div>

    <!-- Tarjeta de Detalles -->
    <div class="card border-0 shadow-sm rounded-3 mx-auto" style="max-width: 1000px;">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-building text-primary me-2"></i>Información Registrada
            </h5>
            <span class="badge bg-light text-secondary border font-monospace">ID: {{ $empresaConfiguracion->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row g-5">
                <!-- Columna Izquierda: Identidad -->
                <div class="col-lg-4 text-center border-end">
                    <div class="mb-4">
                        @if(!empty($empresaConfiguracion->logo_path))
                            <img src="{{ asset('storage/' . $empresaConfiguracion->logo_path) }}" alt="Logo empresa" class="img-fluid rounded-3 border shadow-sm p-2" style="max-height: 200px; object-fit: contain;">
                        @else
                            <div class="bg-light rounded-3 border d-flex flex-column align-items-center justify-content-center mx-auto" style="height: 200px; width: 200px;">
                                <i class="fas fa-image fa-3x mb-2 text-muted opacity-50"></i>
                                <span class="text-muted small fw-medium">Sin logotipo</span>
                            </div>
                        @endif
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $empresaConfiguracion->nombre_comercial }}</h5>
                    <p class="text-muted small mb-3">POS Principal</p>

                    @if((int) $empresaConfiguracion->estado === 1)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-4 py-2 rounded-pill fw-bold">Sistema Activo</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-4 py-2 rounded-pill fw-bold">Sistema Suspendido</span>
                    @endif
                </div>

                <!-- Columna Derecha: Datos Legales y Fiscales -->
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3 fs-7">Identidad Fiscal</h6>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Razón Social</span>
                            <span class="fw-bold text-dark">{{ $empresaConfiguracion->razon_social }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">RUC</span>
                            <span class="fw-bold text-dark font-monospace">{{ $empresaConfiguracion->ruc }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Dirección Fiscal</span>
                            <span class="text-dark">{{ $empresaConfiguracion->direccion_fiscal ?? 'No especificada' }}</span>
                        </div>

                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3 fs-7">Configuración Comercial</h6>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Impuesto (IGV)</span>
                            <span class="fw-bold text-dark fs-5">{{ number_format($empresaConfiguracion->igv_porcentaje ?? 18, 2) }}%</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Moneda Base</span>
                            <span class="badge bg-light border text-dark fs-6">{{ $empresaConfiguracion->moneda ?? 'PEN' }}</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Teléfono Soporte</span>
                            <span class="text-dark">{{ $empresaConfiguracion->telefono ?? '—' }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Correo Notificaciones</span>
                            <span class="text-primary">{{ $empresaConfiguracion->email ?? '—' }}</span>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="bg-light p-3 rounded-3 border border-dashed">
                                <span class="text-muted small fw-bold text-uppercase d-block mb-2">Mensaje en Ticket POS</span>
                                <span class="fst-italic text-dark">{{ $empresaConfiguracion->mensaje_ticket ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="col-12 mt-2 d-flex justify-content-between text-muted small">
                            <span><i class="fas fa-calendar-plus me-1"></i> Creado: {{ $empresaConfiguracion->created_at?->format('d/m/Y') }}</span>
                            <span><i class="fas fa-history me-1"></i> Modificado: {{ $empresaConfiguracion->updated_at?->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection