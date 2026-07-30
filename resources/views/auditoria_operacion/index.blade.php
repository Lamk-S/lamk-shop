@extends('layouts.app')
@section('title', 'Auditoría de Operaciones')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0 fs-3">Auditoría de Operaciones</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item active fw-medium text-dark">Registro de Eventos</li>
        </ol>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 bg-light">
            <form method="GET" action="{{ route('auditoria-operaciones.index') }}" id="filtro-form" class="row g-3">
                <div class="col-md-3">
                    <label for="usuario_id" class="form-label text-muted small fw-bold text-uppercase">Usuario</label>
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
                    <label for="modulo" class="form-label text-muted small fw-bold text-uppercase">Módulo Afectado</label>
                    <select name="modulo" id="modulo" class="form-select shadow-sm">
                        <option value="">Todos los módulos</option>
                        @foreach($modulos as $modulo)
                            <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>{{ $modulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="accion" class="form-label text-muted small fw-bold text-uppercase">Tipo Acción</label>
                    <select name="accion" id="accion" class="form-select shadow-sm">
                        <option value="">Todas</option>
                        @foreach($acciones as $accion)
                            <option value="{{ $accion }}" @selected(request('accion') === $accion)>{{ $accion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="fecha" class="form-label text-muted small fw-bold text-uppercase">Fecha Exacta</label>
                    <input type="date" name="fecha" id="fecha" class="form-control shadow-sm" value="{{ request('fecha') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <a href="{{ route('auditoria-operaciones.index') }}" class="btn btn-outline-secondary shadow-sm w-100" title="Limpiar Filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Datos -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center gap-3">
            <div class="bg-dark bg-opacity-10 text-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-clipboard-list fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Historial de Trazabilidad</h5>
                <div class="text-muted small">Registro inmutable de actividades en el sistema.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-secondary small text-uppercase">Fecha y Hora</th>
                            <th class="text-secondary small text-uppercase">Operador</th>
                            <th class="text-secondary small text-uppercase">Módulo</th>
                            <th class="text-center text-secondary small text-uppercase">Acción</th>
                            <th class="text-center text-secondary small text-uppercase">ID</th>
                            <th class="text-secondary small text-uppercase">IP Origen</th>
                            <th class="text-secondary small text-uppercase">Ruta</th>
                            <th class="text-center text-secondary small text-uppercase pe-4">Detalle</th>
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
                                <td class="ps-4 text-muted font-monospace" style="font-size: 0.85rem;">
                                    <div class="fw-bold text-dark">{{ $item->created_at?->format('d/m/Y') }}</div>
                                    <div>{{ $item->created_at?->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><i class="fas fa-user text-muted me-1"></i> {{ $item->user?->name ?? 'Sistema' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1">{{ $item->entidad ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} border border-{{ $badge }} px-2 py-1">
                                        {{ $item->accion }}
                                    </span>
                                </td>
                                <td class="text-center font-monospace">{{ $item->entidad_id ?? '—' }}</td>
                                <td class="font-monospace text-muted small">{{ $item->ip ?? '—' }}</td>
                                <td style="max-width: 200px;">
                                    <div class="text-truncate text-muted small" title="{{ $item->ruta ?? '—' }}">
                                        {{ $item->ruta ?? '—' }}
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('auditoria-operaciones.show', $item) }}" class="btn btn-sm btn-light border text-primary shadow-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-center text-muted">
                                    <i class="fas fa-search fs-1 text-light mb-3"></i>
                                    <h5 class="fw-semibold text-dark">Sin registros</h5>
                                    <p class="mb-0">No se encontraron eventos para los filtros seleccionados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                    <select name="per_page" form="filtro-form" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                        @foreach([10, 15, 25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small ms-2">
                        Mostrando <strong>{{ $auditorias->firstItem() ?? 0 }}</strong> - <strong>{{ $auditorias->lastItem() ?? 0 }}</strong> de <strong>{{ $auditorias->total() }}</strong>
                    </span>
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
        form.querySelectorAll('select:not(#per_page), input[type="date"]').forEach(element => {
            element.addEventListener('change', () => form.submit());
        });
    });
</script>
@endpush