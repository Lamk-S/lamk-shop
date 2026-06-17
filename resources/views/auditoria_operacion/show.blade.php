@extends('layouts.app')
@section('title', 'Detalle de Auditoría')

@push('css')
<style>
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .14); }
    .json-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; font-size: 0.875rem; max-height: 400px; overflow-y: auto; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Detalle de Operación</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('auditoria-operaciones.index') }}" class="text-decoration-none">Auditoría</a></li>
                <li class="breadcrumb-item active">Detalle</li>
            </ol>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('auditoria-operaciones.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="card card-soft mb-4">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Información General</h5>
                    <div class="text-muted small">Datos capturados durante la petición</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-medium d-block">Usuario</span>
                    <span class="fw-semibold text-dark">{{ $auditoria->user?->name ?? 'Sistema' }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-medium d-block">Módulo (Entidad)</span>
                    <span class="badge bg-light text-secondary border px-2 py-1">{{ $auditoria->entidad }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-medium d-block">Acción</span>
                    <span class="fw-semibold">{{ $auditoria->accion }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-medium d-block">Fecha</span>
                    <span class="fw-semibold">{{ $auditoria->created_at?->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-medium d-block">IP</span>
                    <span>{{ $auditoria->ip ?? '—' }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-medium d-block">Entidad ID</span>
                    <span>{{ $auditoria->entidad_id ?? '—' }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-medium d-block">Método HTTP</span>
                    <span>{{ $auditoria->metodo_http ?? '—' }}</span>
                </div>
                <div class="col-12">
                    <span class="text-muted small fw-medium d-block">Ruta</span>
                    <span class="text-break">{{ $auditoria->ruta ?? '—' }}</span>
                </div>
                <div class="col-12">
                    <span class="text-muted small fw-medium d-block">User Agent</span>
                    <span class="text-break small">{{ $auditoria->user_agent ?? '—' }}</span>
                </div>
            </div>

            <hr class="border-secondary border-opacity-25 my-4">

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-clock-rotate-left text-secondary me-2"></i>
                        <h6 class="mb-0 fw-bold">Estado Anterior (Antes)</h6>
                    </div>
                    <div class="json-box">
                        @if(!empty($auditoria->antes))
                            <pre class="mb-0"><code>{{ json_encode($auditoria->antes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        @else
                            <span class="text-muted fst-italic">No hay datos previos registrados.</span>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-file-signature text-primary me-2"></i>
                        <h6 class="mb-0 fw-bold">Estado Nuevo (Después)</h6>
                    </div>
                    <div class="json-box">
                        @if(!empty($auditoria->despues))
                            <pre class="mb-0"><code>{{ json_encode($auditoria->despues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        @else
                            <span class="text-muted fst-italic">No hay datos nuevos registrados.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection