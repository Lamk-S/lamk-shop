@extends('layouts.app')
@section('title', 'Variantes y SKUs')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; background: #fff; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
    .chip { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .7rem; border-radius: 999px; font-size: .75rem; font-weight: 700; border: 1px solid rgba(148, 163, 184, .18); background: #f8fafc; color: #334155; white-space: nowrap; }
    .table-actions .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .filters-row .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 0.3rem;}
    .empty-state { padding: 3rem 1rem; }
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
    .bootstrap-select > .dropdown-toggle { border-color: #dee2e6 !important; background-color: #fff !important; padding: 0.375rem 0.75rem; border-radius: 0.375rem; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
    .bootstrap-select > .dropdown-toggle:focus { outline: none !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important; border-color: #86b7fe !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Gestión de SKUs y Tallas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none text-muted">Catálogo</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Variantes</li>
            </ol>
        </div>

        @can('gestionar_productos')
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('productos.index') }}" class="btn btn-light shadow-sm rounded-pill px-4 fw-bold border">
                    <i class="fas fa-boxes-stacked me-2"></i>Ver Productos Base
                </a>
                <a href="{{ route('producto-variantes.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-plus me-2"></i>Nueva Variante
                </a>
            </div>
        @endcan
    </div>

    <div class="card soft-card mb-4">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Control de Inventario Específico</h5>
                    <div class="text-muted small">Filtra rápidamente por modelo, talla de calzado/ropa o código de barras.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('producto-variantes.index') }}" id="filtro-variantes-form" class="row g-3 filters-row mb-4">
                <div class="col-lg-3 col-md-6">
                    <label for="q" class="form-label">Búsqueda rápida</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Ej. SKU, EAN-13, Modelo...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="producto_id" class="form-label">Producto Base (Modelo)</label>
                    <select name="producto_id" id="producto_id" class="form-control selectpicker show-tick" data-live-search="true" data-size="6" title="Todos los modelos...">
                        <option value="">Todos los modelos...</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" @selected((string) request('producto_id') === (string) $producto->id)>
                                {{ $producto->codigo }} - {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="talla_id" class="form-label">Talla / Medida</label>
                    <select name="talla_id" id="talla_id" class="form-control selectpicker show-tick" data-live-search="true" data-size="6" title="Cualquier talla...">
                        <option value="">Cualquier talla...</option>
                        @foreach($tallas as $talla)
                            <option value="{{ $talla->id }}" @selected((string) request('talla_id') === (string) $talla->id)>
                                {{ $talla->codigo }} - {{ $talla->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6 d-flex align-items-end">
                    <a href="{{ route('producto-variantes.index') }}" class="btn btn-outline-secondary w-100 fw-bold bg-white shadow-sm" title="Restablecer filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>

            <div class="table-responsive bg-white rounded-3 border">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 250px;">Producto / Marca</th>
                            <th style="min-width: 120px;">Talla</th>
                            <th>Códigos (SKU / Barras)</th>
                            <th class="text-center" style="width: 120px;">Stock Real</th>
                            <th class="text-center" style="width: 130px;">Estado</th>
                            @can('gestionar_productos')
                                <th class="text-center pe-4" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($variantes as $item)
                            @php
                                $stockReal = (float) $item->stock_actual;
                                $stockMin = (float) $item->stock_minimo;
                                $stockClass = $stockReal <= 0 ? 'text-danger bg-danger' : ($stockReal <= $stockMin ? 'text-warning bg-warning' : 'text-success bg-success');
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="{{ optional($item->producto)->nombre }}">
                                        {{ optional($item->producto)->nombre ?? 'Sin producto' }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ optional($item->producto)->codigo }}</span>
                                        <span class="small text-muted fw-medium"><i class="fas fa-tag me-1 text-primary text-opacity-50"></i>{{ optional($item->producto->marca)->nombre ?? 'Sin marca' }}</span>
                                    </div>
                                </td>

                                <td class="py-3">
                                    <span class="chip shadow-sm border-{{ optional($item->talla)->tipo_talla === 'CALZADO' ? 'info' : 'primary' }} border-opacity-25">
                                        <i class="fas {{ optional($item->talla)->tipo_talla === 'CALZADO' ? 'fa-shoe-prints text-info' : 'fa-tshirt text-primary' }}"></i>
                                        {{ optional($item->talla)->nombre ?? 'Única' }}
                                    </span>
                                </td>

                                <td class="py-3">
                                    <div class="font-monospace fw-bold text-dark fs-7" title="SKU Interno"><i class="fas fa-hashtag text-muted me-1"></i>{{ $item->codigo_variante }}</div>
                                    @if($item->codigo_barra)
                                        <div class="font-monospace text-muted small mt-1" title="EAN/UPC"><i class="fas fa-barcode me-1"></i>{{ $item->codigo_barra }}</div>
                                    @endif
                                </td>

                                <td class="text-center py-3">
                                    <div class="badge {{ $stockClass }} bg-opacity-10 {{ str_replace('bg-', 'border border-', $stockClass) }} border-opacity-25 rounded-pill px-3 py-2 fs-7 font-monospace shadow-sm" title="Mínimo sugerido: {{ $stockMin }}">
                                        {{ number_format($stockReal, 0) }} ud.
                                    </div>
                                    @if($stockReal <= $stockMin && $stockReal > 0)
                                        <div class="text-warning small mt-1" style="font-size: 0.65rem; font-weight: 700;"><i class="fas fa-exclamation-triangle me-1"></i>LOW STOCK</div>
                                    @endif
                                </td>

                                <td class="text-center py-3">
                                    @if(!$item->trashed() && (int) $item->estado === 1)
                                        <span class="badge bg-success text-white px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-circle ms-n1 me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Activo</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill">Inactivo</span>
                                    @endif
                                </td>

                                @can('gestionar_productos')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm table-actions" role="group">
                                            <a href="{{ route('producto-variantes.show', $item) }}" class="btn btn-light border text-info" data-bs-toggle="tooltip" title="Auditar movimientos">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('producto-variantes.edit', $item) }}" class="btn btn-light border text-primary" data-bs-toggle="tooltip" title="Editar variante">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-light border {{ !$item->trashed() && (int) $item->estado === 1 ? 'text-danger' : 'text-success' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmModal-{{ $item->id }}"
                                                title="{{ !$item->trashed() && (int) $item->estado === 1 ? 'Desactivar SKU' : 'Restaurar SKU' }}">
                                                <i class="fas {{ !$item->trashed() && (int) $item->estado === 1 ? 'fa-ban' : 'fa-trash-restore-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_productos') ? 6 : 5 }}" class="py-5">
                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-barcode text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Inventario no encontrado</h5>
                                        <p class="text-muted mb-0">No hay variantes que coincidan con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ route('producto-variantes.index') }}" id="pagination-form" class="d-flex align-items-center gap-2">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                    <select name="per_page" id="per_page_select" class="form-select form-select-sm shadow-sm" style="width: auto;">
                        @foreach([10, 15, 25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small fw-medium ms-2">
                        Viendo {{ $variantes->firstItem() ?? 0 }} a {{ $variantes->lastItem() ?? 0 }} de {{ $variantes->total() }}
                    </span>
                </form>
                
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
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-4 pb-5">
                        @if(!$item->trashed() && (int) $item->estado === 1)
                            <div class="text-danger mb-3"><i class="fas fa-ban fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Desactivar variante?</h4>
                            <p class="text-muted mb-4">El SKU <strong>{{ $item->codigo_variante }}</strong> (Talla {{ optional($item->talla)->nombre }}) no podrá ser seleccionado en nuevas ventas o compras.</p>
                        @else
                            <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Activar variante?</h4>
                            <p class="text-muted mb-4">El SKU <strong>{{ $item->codigo_variante }}</strong> volverá a estar disponible en el inventario.</p>
                        @endif
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('producto-variantes.destroy', $item) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn {{ !$item->trashed() && (int) $item->estado === 1 ? 'btn-danger' : 'btn-success' }} fw-bold px-4 rounded-pill shadow-sm">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filtro-variantes-form');
        const searchInput = document.getElementById('q');
        const paginationForm = document.getElementById('pagination-form');
        const perPageSelect = document.getElementById('per_page_select');

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        $('.selectpicker').on('changed.bs.select', function () {
            filterForm.submit();
        });
        
        filterForm.querySelectorAll('select:not(.selectpicker)').forEach(select => {
            select.addEventListener('change', () => filterForm.submit());
        });

        let typingTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });

        if(perPageSelect) {
            perPageSelect.addEventListener('change', () => {
                paginationForm.submit();
            });
        }
    });
</script>
@endpush