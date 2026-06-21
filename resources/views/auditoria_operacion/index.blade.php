@extends('layouts.app')
@section('title', 'Auditoría de Operaciones')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .table-soft thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; background: #fff;}
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .14); }
    .filter-box .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 0.3rem;}
    .truncate-route { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; }
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-0">Auditoría de Operaciones</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Auditoría</li>
            </ol>
        </div>
    </div>

    <div class="card card-soft mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('auditoria-operaciones.index') }}" id="filtro-form" class="filter-box">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="usuario_id" class="form-label">Usuario</label>
                        <select name="usuario_id" id="usuario_id" class="form-select shadow-sm">
                            <option value="">Todos los usuarios</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected(request('usuario_id') == $usuario->id)>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="modulo" class="form-label">Módulo Afectado</label>
                        <select name="modulo" id="modulo" class="form-select shadow-sm">
                            <option value="">Todos los módulos</option>
                            @foreach($modulos as $modulo)
                                <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>{{ $modulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="accion" class="form-label">Tipo de Acción</label>
                        <select name="accion" id="accion" class="form-select shadow-sm">
                            <option value="">Todas</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion }}" @selected(request('accion') === $accion)>{{ $accion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="fecha" class="form-label">Fecha Específica</label>
                        <input type="date" name="fecha" id="fecha" class="form-control shadow-sm" value="{{ request('fecha') }}">
                    </div>

                    <div class="col-md-2 d-flex flex-column gap-2">
                        <label for="per_page" class="form-label">Mostrar</label>
                        <div class="d-flex gap-2">
                            <select name="per_page" id="per_page" class="form-select shadow-sm">
                                @foreach([10, 15, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('auditoria-operaciones.index') }}" class="btn btn-outline-secondary bg-white shadow-sm" title="Limpiar">
                                <i class="fas fa-eraser"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Registro de Eventos</h5>
                    <div class="text-muted small">Trazabilidad de acciones del personal.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Usuario Operador</th>
                            <th>Módulo (Entidad)</th>
                            <th class="text-center">Acción</th>
                            <th class="text-center">ID Entidad</th>
                            <th>IP Origen</th>
                            <th>Ruta HTTP</th>
                            <th class="text-center pe-4">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditorias as $item)
                            @php
                                $accion = strtoupper((string) $item->accion);
                                $badge = match ($accion) {
                                    'CREAR', 'CREADO', 'INSERTAR' => 'success',
                                    'EDITAR', 'ACTUALIZAR', 'UPDATE' => 'warning',
                                    'ELIMINAR', 'ANULAR', 'BORRAR' => 'danger',
                                    default => 'primary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted font-monospace fs-7">
                                    <div>{{ $item->created_at?->format('d/m/Y') }}</div>
                                    <div>{{ $item->created_at?->format('H:i:s') }}</div>
                                </td>
                                <td class="fw-bold text-dark">{{ $item->user?->name ?? 'Sistema Automático' }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill shadow-sm fs-7">
                                        {{ $item->entidad ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} border border-{{ $badge }} border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                        {{ $item->accion }}
                                    </span>
                                </td>
                                <td class="text-center font-monospace">{{ $item->entidad_id ?? '—' }}</td>
                                <td class="font-monospace fs-7 text-muted">{{ $item->ip ?? '—' }}</td>
                                <td>
                                    <span class="text-muted small truncate-route" title="{{ $item->ruta ?? '—' }}">
                                        {{ $item->ruta ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('auditoria-operaciones.show', $item) }}" class="btn btn-sm btn-light border text-primary shadow-sm" title="Inspeccionar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-clipboard-list text-muted fs-2 opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Sin historial de auditoría</h6>
                                        <p class="text-muted small mb-0">No se encontraron eventos para los filtros seleccionados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small fw-medium">
                    Mostrando del <span class="fw-bold text-dark">{{ $auditorias->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $auditorias->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $auditorias->total() }}</span> registros
                </div>
                <div class="pagination-custom">
                    {{ $auditorias->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filtro-form');
        form.querySelectorAll('select, input[type="date"]').forEach(element => {
            element.addEventListener('change', () => form.submit());
        });
    });
</script>
@endpush