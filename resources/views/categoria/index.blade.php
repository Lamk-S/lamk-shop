@extends('layouts.app')
@section('title', 'Categorías')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; background: #fff; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
    .filters-row .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 0.3rem;}
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Líneas de Producto (Categorías)</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Categorías</li>
            </ol>
        </div>

        @can('gestionar_categorias')
            <a href="{{ route('categorias.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="fas fa-plus me-2"></i>Crear Categoría
            </a>
        @endcan
    </div>

    <div class="card soft-card">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('categorias.index') }}" id="filtro-form" class="row g-3 filters-row">
                <div class="col-lg-5 col-md-6">
                    <label for="q" class="form-label">Buscar Categoría</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Ej. Zapatillas Running, Poleras...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="estado" class="form-label">Disponibilidad</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Cualquier estado</option>
                        <option value="activa" @selected(request('estado') === 'activa')>Solo Activas</option>
                        <option value="inactiva" @selected(request('estado') === 'inactiva')>Solo Inactivas</option>
                        <option value="eliminada" @selected(request('estado') === 'eliminada')>En Papelera</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="per_page" class="form-label">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select shadow-sm">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex align-items-end gap-2">
                    <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary w-100 fw-bold bg-white shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header soft-header p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Estructura del Catálogo</h5>
                <div class="text-muted small">Familias de productos para organizar el inventario y tienda virtual.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 220px;">Nombre Comercial</th>
                            <th>Descripción</th>
                            <th class="text-center" style="width: 130px;">Estado</th>
                            @can('gestionar_categorias')
                                <th class="text-center pe-4" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categorias as $categoria)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $categoria->nombre }}</div>
                                    <div class="small text-muted font-monospace mt-1">ID Interno: {{ str_pad($categoria->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="text-muted py-3">
                                    {{ \Illuminate\Support\Str::limit($categoria->descripcion, 80, '...') ?: 'Sin descripción registrada' }}
                                </td>
                                <td class="text-center py-3">
                                    @if($categoria->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Eliminada</span>
                                    @elseif((int) $categoria->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Activa</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Inactiva</span>
                                    @endif
                                </td>
                                @can('gestionar_categorias')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2" role="group">
                                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-light border text-primary" title="Editar Categoría">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light border {{ $categoria->trashed() ? 'text-success' : 'text-danger' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmModal-{{ $categoria->id }}"
                                                title="{{ $categoria->trashed() ? 'Restaurar' : 'Eliminar' }}">
                                                <i class="fas {{ $categoria->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_categorias') ? 4 : 3 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-box-open text-muted fs-2 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Catálogo Vacío</h5>
                                        <p class="text-muted mb-0">No se han encontrado categorías activas con tu búsqueda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small fw-medium">
                    Mostrando del <span class="fw-bold text-dark">{{ $categorias->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $categorias->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $categorias->total() }}</span> registros
                </div>
                <div class="pagination-custom">
                    {{ $categorias->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@can('gestionar_categorias')
    @foreach($categorias as $categoria)
        <div class="modal fade" id="confirmModal-{{ $categoria->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center p-4 pb-5">
                        @if(!$categoria->trashed())
                            <div class="text-danger mb-3"><i class="fas fa-ban fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Archivar categoría?</h4>
                            <p class="text-muted mb-4">La línea <strong>{{ $categoria->nombre }}</strong> dejará de mostrarse al clasificar nuevos productos.</p>
                        @else
                            <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Restaurar categoría?</h4>
                            <p class="text-muted mb-4">La línea <strong>{{ $categoria->nombre }}</strong> volverá a estar activa en todo el sistema.</p>
                        @endif
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('categorias.destroy', $categoria) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn {{ !$categoria->trashed() ? 'btn-danger' : 'btn-success' }} fw-bold px-4 rounded-pill shadow-sm">
                                    Confirmar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endcan
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filtro-form');
        const searchInput = document.getElementById('q');

        form.querySelectorAll('select').forEach(element => {
            element.addEventListener('change', () => form.submit());
        });

        let typingTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endpush