@extends('layouts.app')
@section('title', 'Auditoría de Operaciones')

@push('css')
<style>
    .table-soft thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .78rem; letter-spacing: .04em; white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .14); }
    .filter-box { background: #fff; border: 1px solid rgba(148, 163, 184, .16); border-radius: 1rem; padding: 1rem; }
    .truncate-route { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Auditoría de Operaciones</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Auditoría</li>
            </ol>
        </div>
    </div>

    <div class="card card-soft mb-4">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Registro de eventos</h5>
                    <div class="text-muted small">Consulta de acciones realizadas por usuarios y por el sistema</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('auditoria-operaciones.index') }}" class="filter-box mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="usuario_id" class="form-label fw-medium text-secondary">Usuario</label>
                        <select name="usuario_id" id="usuario_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected(request('usuario_id') == $usuario->id)>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="modulo" class="form-label fw-medium text-secondary">Módulo</label>
                        <select name="modulo" id="modulo" class="form-select">
                            <option value="">Todos</option>
                            @foreach($modulos as $modulo)
                                <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>{{ $modulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="accion" class="form-label fw-medium text-secondary">Acción</label>
                        <select name="accion" id="accion" class="form-select">
                            <option value="">Todas</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion }}" @selected(request('accion') === $accion)>{{ $accion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="fecha" class="form-label fw-medium text-secondary">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" value="{{ request('fecha') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="per_page" class="form-label fw-medium text-secondary">Mostrar</label>
                        <select name="per_page" id="per_page" class="form-select">
                            @foreach([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <a href="{{ route('auditoria-operaciones.index') }}" class="btn btn-light">Limpiar</a>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Entidad ID</th>
                            <th>IP</th>
                            <th>Ruta</th>
                            <th class="text-center">Detalle</th>
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
                                <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="fw-semibold text-dark">{{ $item->user?->name ?? 'Sistema' }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                                        {{ $item->entidad ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} border border-{{ $badge }} border-opacity-25 px-3 py-2 rounded-pill">
                                        {{ $item->accion }}
                                    </span>
                                </td>
                                <td>{{ $item->entidad_id ?? '—' }}</td>
                                <td>{{ $item->ip ?? '—' }}</td>
                                <td>
                                    <span class="truncate-route" title="{{ $item->ruta ?? '—' }}">
                                        {{ $item->ruta ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('auditoria-operaciones.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-clipboard-list text-secondary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No hay registros de auditoría</h5>
                                        <p class="text-muted mb-0">Todavía no se han guardado operaciones para mostrar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4">
                <div class="text-muted small">
                    Mostrando {{ $auditorias->firstItem() ?? 0 }} - {{ $auditorias->lastItem() ?? 0 }} de {{ $auditorias->total() }} registros
                </div>
                <div>
                    {{ $auditorias->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection