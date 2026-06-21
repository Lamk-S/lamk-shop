@extends('layouts.app')
@section('title', 'Configuración de Empresa')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="page-title mb-0">Configuración de Empresa</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Configuración</li>
            </ol>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('empresa-configuracion.edit', $empresaConfiguracion) }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 1100px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-building text-primary me-2"></i>{{ $empresaConfiguracion->nombre_comercial }}
            </h5>
            <span class="badge bg-light text-secondary border">ID: {{ $empresaConfiguracion->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row g-4">
                <div class="col-lg-4 text-center">
                    @if(!empty($empresaConfiguracion->logo_path))
                        <img src="{{ asset('storage/' . $empresaConfiguracion->logo_path) }}" alt="Logo empresa" class="img-fluid rounded-4 border shadow-sm mb-3" style="max-height: 220px;">
                    @else
                        <div class="bg-light rounded-4 border d-flex align-items-center justify-content-center mb-3" style="height: 220px;">
                            <div class="text-muted text-center">
                                <i class="fas fa-image fa-4x mb-2 opacity-50"></i>
                                <p class="mb-0">Sin logo</p>
                            </div>
                        </div>
                    @endif

                    @if((int) $empresaConfiguracion->estado === 1)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                    @endif
                </div>

                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Razón social</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->razon_social }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Nombre comercial</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->nombre_comercial }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small mb-1">RUC</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->ruc }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small mb-1">IGV</div>
                            <div class="fw-semibold text-dark">{{ number_format($empresaConfiguracion->igv_porcentaje ?? 18, 2) }}%</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small mb-1">Moneda</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->moneda ?? 'PEN' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Teléfono</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->telefono ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Correo electrónico</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->email ?? '—' }}</div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted small mb-1">Dirección</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->direccion_fiscal ?? '—' }}</div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted small mb-1">Mensaje de ticket</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->mensaje_ticket ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Creado</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->created_at?->format('d/m/Y H:i') }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Última actualización</div>
                            <div class="fw-semibold text-dark">{{ $empresaConfiguracion->updated_at?->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('panel') }}" class="btn btn-light px-4">Volver</a>
                        <a href="{{ route('empresa-configuracion.edit', $empresaConfiguracion) }}" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-edit me-2"></i>Editar configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection