@extends('layouts.app')
@section('title', 'Catálogo de Productos')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .8rem; white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .chip { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .7rem; border-radius: 999px; font-size: .8rem; font-weight: 600; border: 1px solid rgba(148, 163, 184, .18); background: #fff; color: #334155; margin: .15rem; white-space: nowrap; }
    .chip-muted { background: #f8fafc; color: #64748b; }
    .product-thumb { width: 52px; height: 52px; border-radius: 1rem; object-fit: cover; background: #f8fafc; border: 1px solid rgba(148, 163, 184, .18); }
    .product-thumb-placeholder { width: 52px; height: 52px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid rgba(148, 163, 184, .18); color: #94a3b8; }
    .table-actions .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .empty-state { padding: 3rem 1rem; }
    .filters-row .form-label { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
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
            <h2 class="page-title text-dark mb-0">Catálogo de Productos</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Productos</li>
            </ol>
        </div>

        @can('gestionar_productos')
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('producto-variantes.index') }}" class="btn btn-outline-secondary shadow-sm rounded-3 px-4 fw-medium">
                    <i class="fas fa-layer-group me-2"></i>Gestión de Variantes
                </a>
                <a href="{{ route('productos.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-medium">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </a>
            </div>
        @endcan
    </div>

    <div class="card soft-card mb-4">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Inventario general</h5>
                        <div class="text-muted small">Búsqueda rápida, filtros y administración de stock base</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('productos.index') }}" id="filtro-productos-form" class="row g-3 filters-row mb-4">
                <div class="col-lg-4 col-md-6">
                    <label for="q" class="form-label">Buscar producto</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Código, barra o nombre...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="tipo_producto" class="form-label">Clasificación</label>
                    <select name="tipo_producto" id="tipo_producto" class="form-select shadow-sm">
                        <option value="">Todas</option>
                        @foreach(\App\Enums\TipoProducto::opciones() as $value => $label)
                            <option value="{{ $value }}" @selected(request('tipo_producto') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="marca_id" class="form-label">Marca</label>
                    <select name="marca_id" id="marca_id" class="form-control selectpicker show-tick shadow-sm border" data-live-search="true" data-size="6">
                        <option value="">Todas</option>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}" @selected((string) request('marca_id') === (string) $marca->id)>{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label">Disponibilidad</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-12 d-flex justify-content-end align-items-end">
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary w-100 fw-medium" title="Limpiar todos los filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0 border">
                    <thead>
                        <tr>
                            <th style="min-width: 280px;">Identificación de Producto</th>
                            <th style="min-width: 200px;">Clasificación</th>
                            <th class="text-center">Stock Actual</th>
                            <th class="text-end">P. Compra</th>
                            <th class="text-end">P. Venta</th>
                            <th class="text-center">Estado</th>
                            @can('gestionar_productos')
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->img_path)
                                            <img src="{{ asset('storage/' . $item->img_path) }}" alt="{{ $item->nombre }}" class="product-thumb">
                                        @else
                                            <div class="product-thumb-placeholder"><i class="fas fa-box-open"></i></div>
                                        @endif

                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->nombre }}</div>
                                            <div class="small mt-1">
                                                <span class="badge bg-light text-secondary border me-1"><i class="fas fa-hashtag me-1"></i>{{ $item->codigo }}</span>
                                                @if($item->codigo_barra)
                                                    <span class="badge bg-light text-secondary border"><i class="fas fa-barcode me-1"></i>{{ $item->codigo_barra }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small mb-1">
                                        <span class="chip chip-muted"><i class="fas fa-tag"></i> {{ optional($item->marca)->nombre ?? 'Genérico' }}</span>
                                        <span class="chip chip-muted"><i class="fas fa-layer-group"></i> {{ ucfirst(strtolower($item->tipo_producto?->value ?? $item->tipo_producto)) }}</span>
                                    </div>
                                    <div>
                                        @if($item->maneja_tallas)
                                            <span class="chip" style="background:#ecfeff;color:#0f766e;border-color:#a5f3fc;font-size:0.7rem;">Segmentado por tallas</span>
                                        @else
                                            <span class="chip" style="background:#f8fafc;color:#475569;border-color:#e2e8f0;font-size:0.7rem;">Talla estándar/única</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2 {{ ($item->stock_total ?? 0) <= ($item->stock_minimo ?? 5) ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' }}">
                                        {{ number_format((float) ($item->stock_total ?? 0), 0) }} unid.
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-secondary">S/ {{ number_format((float) $item->precio_compra, 2) }}</td>
                                <td class="text-end fw-bold text-success">S/ {{ number_format((float) $item->precio_venta, 2) }}</td>
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
                                            <button type="button" class="btn btn-sm btn-outline-secondary text-info border-light" data-bs-toggle="modal" data-bs-target="#verModal-{{ $item->id }}" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('productos.edit', $item) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary {{ !$item->trashed() && (int) $item->estado === 1 ? 'text-danger' : 'text-success' }} border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}">
                                                <i class="fas {{ !$item->trashed() && (int) $item->estado === 1 ? 'fa-trash-alt' : 'fa-trash-restore-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_productos') ? 7 : 6 }}" class="py-5">
                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-search text-secondary fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No se encontraron productos</h5>
                                        <p class="text-muted mb-0">Ajusta los filtros de búsqueda o registra un nuevo producto en el catálogo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ route('productos.index') }}" id="pagination-form" class="d-flex align-items-center gap-2">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page" class="form-label mb-0 small fw-bold text-muted text-uppercase">Mostrar:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm shadow-sm" style="width: 80px;">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small fw-medium ms-2">
                        del <span class="fw-bold text-dark">{{ $productos->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $productos->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $productos->total() }}</span> registros
                    </span>
                </form>
                <div class="pagination-custom">
                    {{ $productos->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>

@foreach($productos as $item)
    @can('gestionar_productos')
        <div class="modal fade" id="verModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                </div>
        </div>
        <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered">
                </div>
        </div>
    @endcan
@endforeach
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filtro-productos-form');
        const paginationForm = document.getElementById('pagination-form');
        const searchInput = document.getElementById('q');
        
        const nativeSelects = filterForm.querySelectorAll('select:not(.selectpicker)');
        nativeSelects.forEach(select => {
            select.addEventListener('change', () => filterForm.submit());
        });

        $('#marca_id').on('changed.bs.select', function () {
            filterForm.submit();
        });

        let typingTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                filterForm.submit();
            }, 600);
        });

        document.getElementById('per_page').addEventListener('change', () => {
            paginationForm.submit();
        });
    });
</script>
@endpush