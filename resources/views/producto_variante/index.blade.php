@extends('layouts.app')
@section('title', 'Variantes de Producto')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .8rem; white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .chip { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .7rem; border-radius: 999px; font-size: .8rem; font-weight: 600; border: 1px solid rgba(148, 163, 184, .18); background: #fff; color: #334155; margin: .15rem; white-space: nowrap; }
    .table-actions .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .filters-row .form-label { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
    .empty-state { padding: 3rem 1rem; }
    
    /* Clases corregidas para ocultar el texto en inglés y estilizar los botones */
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title text-dark mb-0">Variantes de Producto</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Variantes</li>
            </ol>
        </div>

        @can('gestionar_productos')
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary shadow-sm rounded-3 px-4 fw-medium">
                    <i class="fas fa-boxes-stacked me-2"></i>Productos
                </a>
                <a href="{{ route('producto-variantes.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-medium">
                    <i class="fas fa-plus me-2"></i>Nueva Variante
                </a>
            </div>
        @endcan
    </div>

    <div class="card soft-card mb-4">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Control de SKU por tallas</h5>
                        <div class="text-muted small">Busca por producto, talla, código de variante o código de barra</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('producto-variantes.index') }}" class="row g-3 filters-row mb-4">
                <div class="col-lg-4 col-md-6">
                    <label for="q" class="form-label">Buscar</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Producto, talla o código">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="producto_id" class="form-label">Producto</label>
                    <select name="producto_id" id="producto_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" data-size="6">
                        <option value="">Todos</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" @selected((string) request('producto_id') === (string) $producto->id)>
                                {{ $producto->codigo }} - {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="talla_id" class="form-label">Talla</label>
                    <select name="talla_id" id="talla_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" data-size="6">
                        <option value="">Todas</option>
                        @foreach($tallas as $talla)
                            <option value="{{ $talla->id }}" @selected((string) request('talla_id') === (string) $talla->id)>
                                {{ $talla->codigo }} - {{ $talla->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
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

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('producto-variantes.index') }}" class="btn btn-light fw-medium border">Limpiar</a>
                    <button type="submit" class="btn btn-primary fw-medium">
                        <i class="fas fa-filter me-2"></i>Aplicar filtros
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th>Producto base</th>
                            <th>Talla</th>
                            <th>Código variante</th>
                            <th>Código barra</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Stock mín.</th>
                            <th class="text-center">Estado</th>
                            @can('gestionar_productos')
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($variantes as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ optional($item->producto)->nombre ?? 'Sin producto' }}</div>
                                    <div class="text-muted small">{{ optional($item->producto->marca)->nombre ?? 'Sin marca' }}</div>
                                </td>

                                <td>
                                    <span class="chip">
                                        {{ optional($item->talla)->codigo ?? '-' }} - {{ optional($item->talla)->nombre ?? 'Sin talla' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-light text-secondary border">{{ $item->codigo_variante }}</span>
                                </td>

                                <td>{{ $item->codigo_barra ?: '—' }}</td>

                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill px-3">
                                        {{ number_format((float) $item->stock_actual, 0) }} unid.
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-warning text-dark rounded-pill px-3">
                                        {{ number_format((float) $item->stock_minimo, 0) }} unid.
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if(!$item->trashed() && (int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                                    @endif
                                </td>

                                @can('gestionar_productos')
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm table-actions bg-white" role="group">
                                            <a href="{{ route('producto-variantes.show', $item) }}" class="btn btn-sm btn-outline-secondary text-info border-light" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('producto-variantes.edit', $item) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary {{ !$item->trashed() && (int) $item->estado === 1 ? 'text-danger' : 'text-success' }} border-light"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmModal-{{ $item->id }}"
                                                title="{{ !$item->trashed() && (int) $item->estado === 1 ? 'Desactivar' : 'Restaurar' }}">
                                                <i class="fas {{ !$item->trashed() && (int) $item->estado === 1 ? 'fa-trash-alt' : 'fa-trash-restore-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_productos') ? 8 : 7 }}" class="py-5">
                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-search text-secondary fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No se encontraron variantes</h5>
                                        <p class="text-muted mb-0">Ajusta los filtros o registra una nueva variante para empezar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <div class="text-muted small fw-medium">
                    Mostrando del <span class="fw-bold text-dark">{{ $variantes->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $variantes->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $variantes->total() }}</span> registros
                </div>
                
                <div class="pagination-custom">
                    {{ $variantes->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($variantes as $item)
    @can('gestionar_productos')
        <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-5">
                        @if(!$item->trashed() && (int) $item->estado === 1)
                            <div class="text-danger mb-4"><i class="fas fa-exclamation-triangle fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Suspender variante?</h4>
                            <p class="text-muted mb-4">La variante <strong>{{ $item->codigo_variante }}</strong> dejará de estar disponible.</p>
                        @else
                            <div class="text-success mb-4"><i class="fas fa-check-circle fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Activar variante?</h4>
                            <p class="text-muted mb-4">La variante <strong>{{ $item->codigo_variante }}</strong> volverá a estar activa.</p>
                        @endif
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('producto-variantes.destroy', $item) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn {{ !$item->trashed() && (int) $item->estado === 1 ? 'btn-danger' : 'btn-success' }} px-4 shadow-sm">
                                    Confirmar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endforeach
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush