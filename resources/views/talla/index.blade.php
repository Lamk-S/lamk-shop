@extends('layouts.app')
@section('title', 'Tallas')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .8rem; white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .filters-row .form-label { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
    .table-actions .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .empty-state { padding: 3rem 1rem; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title text-dark mb-0">Catálogo de Tallas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Tallas</li>
            </ol>
        </div>

        @can('gestionar_tallas')
            <a href="{{ route('tallas.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-medium">
                <i class="fas fa-plus me-2"></i>Nueva Talla
            </a>
        @endcan
    </div>

    <div class="card soft-card">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-ruler-combined"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Gestión de tallas</h5>
                    <div class="text-muted small">Organiza las tallas por tipo, orden y estado operativo.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('tallas.index') }}" class="row g-3 filters-row mb-4 bg-light rounded-3 p-3 border">
                <div class="col-lg-4 col-md-6">
                    <label for="q" class="form-label">Buscar</label>
                    <input type="search" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Código, nombre o tipo...">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="tipo_talla" class="form-label">Tipo</label>
                    <select name="tipo_talla" id="tipo_talla" class="form-select">
                        <option value="">Todas</option>
                        <option value="CALZADO" @selected(request('tipo_talla') === 'CALZADO')>Calzado</option>
                        <option value="ROPA" @selected(request('tipo_talla') === 'ROPA')>Ropa</option>
                        <option value="UNICA" @selected(request('tipo_talla') === 'UNICA')>Única</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activa" @selected(request('estado') === 'activa')>Activas</option>
                        <option value="inactiva" @selected(request('estado') === 'inactiva')>Inactivas</option>
                        <option value="eliminada" @selected(request('estado') === 'eliminada')>Eliminadas</option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-6">
                    <label for="per_page" class="form-label">Ver</label>
                    <select name="per_page" id="per_page" class="form-select">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex align-items-end gap-2">
                    <a href="{{ route('tallas.index') }}" class="btn btn-light border w-100 fw-medium">Limpiar</a>
                    <button type="submit" class="btn btn-primary w-100 fw-medium">
                        <i class="fas fa-filter me-1"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive rounded-3 border">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 120px;">Código</th>
                            <th style="min-width: 180px;">Nombre</th>
                            <th style="min-width: 140px;">Tipo</th>
                            <th class="text-center" style="width: 110px;">Orden</th>
                            <th class="text-center" style="width: 130px;">Estado</th>
                            @can('gestionar_tallas')
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tallas as $talla)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $talla->codigo }}</div>
                                    <div class="small text-muted">ID: {{ $talla->id }}</div>
                                </td>
                                <td class="fw-medium text-dark">{{ $talla->nombre }}</td>
                                <td>
                                    @if($talla->tipo_talla === 'CALZADO')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill">Calzado</span>
                                    @elseif($talla->tipo_talla === 'ROPA')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">Ropa</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">Única</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">{{ $talla->orden }}</td>
                                <td class="text-center">
                                    @if($talla->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Eliminada</span>
                                    @elseif((int) $talla->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activa</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill">Inactiva</span>
                                    @endif
                                </td>

                                @can('gestionar_tallas')
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm table-actions" role="group">
                                            <a href="{{ route('tallas.edit', $talla) }}" class="btn btn-outline-secondary text-primary border-light" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-secondary {{ $talla->trashed() ? 'text-success' : 'text-danger' }} border-light"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal-{{ $talla->id }}"
                                                    title="{{ $talla->trashed() ? 'Restaurar' : 'Desactivar' }}">
                                                <i class="fas {{ $talla->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_tallas') ? 6 : 5 }}" class="py-5">
                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-ruler-combined text-secondary fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No se encontraron tallas</h5>
                                        <p class="text-muted mb-0">Prueba con otros filtros o registra una nueva talla.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <div class="text-muted small fw-medium">
                    Mostrando <span class="fw-bold text-dark">{{ $tallas->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $tallas->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $tallas->total() }}</span> registros
                </div>
                <div class="pagination-custom">
                    {{ $tallas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@can('gestionar_tallas')
    @foreach($tallas as $talla)
        <div class="modal fade" id="confirmModal-{{ $talla->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center pb-4">
                        @if($talla->trashed())
                            <div class="text-success mb-3">
                                <i class="fas fa-check-circle fa-4x"></i>
                            </div>
                            <h4 class="fw-bold text-dark">¿Restaurar talla?</h4>
                            <p class="text-muted">La talla <strong>{{ $talla->codigo }} - {{ $talla->nombre }}</strong> volverá a estar activa.</p>
                        @else
                            <div class="text-danger mb-3">
                                <i class="fas fa-exclamation-circle fa-4x"></i>
                            </div>
                            <h4 class="fw-bold text-dark">¿Desactivar talla?</h4>
                            <p class="text-muted">La talla <strong>{{ $talla->codigo }} - {{ $talla->nombre }}</strong> dejará de estar disponible.</p>
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('tallas.destroy', $talla) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn {{ $talla->trashed() ? 'btn-success' : 'btn-danger' }} px-4">
                                Confirmar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endcan
@endsection