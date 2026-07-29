@extends('layouts.app')
@section('title', 'Detalle de Auditoría')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Detalle de Operación</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('auditoria-operaciones.index') }}" class="text-decoration-none text-muted">Auditoría</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Inspección de Registro</li>
            </ol>
        </div>
        <a href="{{ route('auditoria-operaciones.index') }}" class="btn btn-light border shadow-sm">
            <i class="fa-solid fa-arrow-left me-2"></i> Volver al listado
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom p-4">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-circle-info text-primary me-2"></i>Metadatos de la Petición
            </h5>
        </div>

        <div class="card-body p-4">
            <!-- Grid de info general -->
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Operador</span>
                    <span class="fw-bold text-dark fs-6">{{ $auditoria->user?->name ?? 'Sistema Automático' }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Módulo (Entidad)</span>
                    <span class="badge bg-light text-secondary border px-2 py-1 fs-6">{{ $auditoria->entidad }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Acción</span>
                    <span class="fw-bold text-dark fs-6">{{ $auditoria->accion }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Fecha de Registro</span>
                    <span class="fw-bold text-dark font-monospace">{{ $auditoria->created_at?->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Dirección IP</span>
                    <span class="font-monospace text-dark">{{ $auditoria->ip ?? '—' }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">ID Entidad</span>
                    <span class="font-monospace text-dark">{{ $auditoria->entidad_id ?? '—' }}</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Método HTTP</span>
                    <span class="font-monospace text-dark">{{ $auditoria->metodo_http ?? '—' }}</span>
                </div>
                <div class="col-12">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Ruta Solicitada</span>
                    <span class="text-break font-monospace text-primary bg-light p-1 rounded">{{ $auditoria->ruta ?? '—' }}</span>
                </div>
                <div class="col-12">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">User Agent (Navegador)</span>
                    <span class="text-break small text-muted">{{ $auditoria->user_agent ?? '—' }}</span>
                </div>
            </div>

            <hr class="border-secondary border-opacity-25 my-4">

            <!-- Diffs JSON -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-clock-rotate-left text-danger me-2"></i>
                        <h6 class="mb-0 fw-bold">Estado Anterior (Antes)</h6>
                    </div>
                    <div class="bg-light border rounded-3 p-3 overflow-auto" style="max-height: 250px;">
                        @if(!empty($auditoria->antes))
                            <pre class="mb-0 font-monospace" style="font-size: 0.85rem;"><code class="text-dark">{{ json_encode($auditoria->antes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        @else
                            <span class="text-muted fst-italic small">No hay datos previos registrados (o fue una creación).</span>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-file-signature text-success me-2"></i>
                        <h6 class="mb-0 fw-bold">Estado Nuevo (Después)</h6>
                    </div>
                    <div class="bg-light border rounded-3 p-3 overflow-auto" style="max-height: 250px;">
                        @if(!empty($auditoria->despues))
                            <pre class="mb-0 font-monospace" style="font-size: 0.85rem;"><code class="text-dark">{{ json_encode($auditoria->despues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        @else
                            <span class="text-muted fst-italic small">No hay datos nuevos registrados (o fue una eliminación).</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection