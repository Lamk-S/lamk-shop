@extends('layouts.app')
@section('title', 'Variantes y SKUs')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Gestión de SKUs y Tallas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none text-muted">Catálogo</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Variantes</li>
            </ol>
        </div>

        @can('gestionar_productos')
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('productos.index') }}" class="btn btn-light shadow-sm rounded-3 px-4 fw-medium border">
                    <i class="fas fa-boxes-stacked me-2"></i>Ver Productos Base
                </a>
                <a href="{{ route('producto-variantes.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-medium">
                    <i class="fas fa-plus me-2"></i>Nueva Variante
                </a>
            </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4">
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
            <form method="GET" action="{{ route('producto-variantes.index') }}" id="filtro-variantes-form" class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <label for="q" class="form-label fw-bold text-secondary small text-uppercase">Búsqueda rápida</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                        <!-- Eliminado el event typingTimer. Un lector POS da "Enter" automático -->
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="SKU, EAN-13, Modelo...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="producto_id" class="form-label fw-bold text-secondary small text-uppercase">Producto Base</label>
                    <select name="producto_id" id="producto_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" data-size="6" title="Todos los modelos...">
                        <option value="">Todos los modelos...</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" @selected((string) request('producto_id') === (string) $producto->id)>
                                {{ $producto->codigo }} - {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="talla_id" class="form-label fw-bold text-secondary small text-uppercase">Talla</label>
                    <select name="talla_id" id="talla_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" data-size="6" title="Cualquier talla...">
                        <option value="">Cualquier talla...</option>
                        @foreach($tallas as $talla)
                            <option value="{{ $talla->id }}" @selected((string) request('talla_id') === (string) $talla->id)>
                                {{ $talla->codigo }} - {{ $talla->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <label for="estado" class="form-label fw-bold text-secondary small text-uppercase">Estado</label>
                    <select name="estado" id="estado" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6 d-flex justify-content-end align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm fw-medium"><i class="fas fa-search"></i></button>
                    <a href="{{ route('producto-variantes.index') }}" class="btn btn-light border shadow-sm w-100 fw-medium" title="Restablecer filtros">
                        <i class="fas fa-eraser"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive bg-white rounded-3 shadow-sm border">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-secondary small text-uppercase fw-bold" style="min-width: 250px;">Producto / Marca</th>
                            <th class="text-secondary small text-uppercase fw-bold" style="min-width: 120px;">Talla</th>
                            <th class="text-secondary small text-uppercase fw-bold">Códigos SKU</th>
                            <th class="text-center text-secondary small text-uppercase fw-bold" style="width: 120px;">Stock Real</th>
                            <th class="text-center text-secondary small text-uppercase fw-bold" style="width: 130px;">Estado</th>
                            @canany(['gestionar_productos', 'ver_productos'])
                                <th class="text-center pe-4 text-secondary small text-uppercase fw-bold" style="width: 120px;">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($variantes as $item)
                            @php
                                $stockReal = (float) $item->stock_actual;
                                $stockMin = (float) $item->stock_minimo;
                                $stockStatusClass = $stockReal <= 0 ? 'danger' : ($stockReal <= $stockMin ? 'warning' : 'primary');
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark text-wrap" style="max-width: 280px;">
                                        {{ optional($item->producto)->nombre ?? 'Sin producto' }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-light text-secondary border px-2 py-1">{{ optional($item->producto)->codigo }}</span>
                                        <span class="small text-muted fw-medium"><i class="fas fa-tag me-1"></i>{{ optional($item->producto->marca)->nombre ?? 'Sin marca' }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        <i class="fas {{ optional($item->talla)->tipo_talla === 'CALZADO' ? 'fa-shoe-prints text-info' : 'fa-tshirt text-primary' }} me-1"></i>
                                        {{ optional($item->talla)->nombre ?? 'Única' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="font-monospace fw-bold text-dark"><i class="fas fa-hashtag text-muted me-1"></i>{{ $item->codigo_variante }}</div>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-{{ $stockStatusClass }} bg-opacity-10 text-{{ $stockStatusClass }} border border-{{ $stockStatusClass }} border-opacity-25 rounded-pill px-3 py-2 fs-6 shadow-sm" title="Mínimo sugerido: {{ $stockMin }}">
                                        {{ number_format($stockReal, 0) }} ud.
                                    </span>
                                    @if($stockReal <= $stockMin && $stockReal > 0)
                                        <div class="text-warning mt-1" style="font-size: 0.70rem; font-weight: 700;"><i class="fas fa-exclamation-triangle me-1"></i>ALERTA MÍNIMO</div>
                                    @endif
                                </td>
                                <td class="text-center py-3">
                                    @if(!$item->trashed() && (int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill">Inactivo</span>
                                    @endif
                                </td>
                                @canany(['gestionar_productos', 'ver_productos'])
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('producto-variantes.show', $item) }}" class="btn btn-sm btn-light border text-info" title="Auditar movimientos">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('gestionar_productos')
                                                <a href="{{ route('producto-variantes.edit', $item) }}" class="btn btn-sm btn-light border text-primary" title="Editar variante">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-light border btn-confirmar {{ !$item->trashed() && (int) $item->estado === 1 ? 'text-danger' : 'text-success' }}"
                                                    data-id="{{ $item->id }}"
                                                    data-sku="{{ $item->codigo_variante }}"
                                                    data-talla="{{ optional($item->talla)->nombre }}"
                                                    data-accion="{{ !$item->trashed() && (int) $item->estado === 1 ? 'desactivar' : 'restaurar' }}"
                                                    title="{{ !$item->trashed() && (int) $item->estado === 1 ? 'Desactivar SKU' : 'Restaurar SKU' }}">
                                                    <i class="fas {{ !$item->trashed() && (int) $item->estado === 1 ? 'fa-ban' : 'fa-trash-restore-alt' }}"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_productos') ? 6 : 5 }}" class="py-5 text-center">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-barcode text-muted fs-2 opacity-50"></i>
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

            <!-- Paginación -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ route('producto-variantes.index') }}" class="d-flex align-items-center gap-2">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                    <select name="per_page" class="form-select form-select-sm shadow-sm" style="width: auto;" onchange="this.form.submit()">
                        @foreach([10, 15, 25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small fw-medium ms-2">
                        Viendo {{ $variantes->firstItem() ?? 0 }} a {{ $variantes->lastItem() ?? 0 }} de {{ $variantes->total() }}
                    </span>
                </form>
                <div class="pagination-custom">
                    {{ $variantes->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@can('gestionar_productos')
<!-- Modal Confirmación Global -->
<div class="modal fade" id="modalConfirmacionGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 pb-5">
                <div class="mb-3" id="modalIcon"></div>
                <h4 class="fw-bold text-dark" id="modalTitle">¿Desactivar variante?</h4>
                <p class="text-muted mb-4" id="modalDesc"></p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light fw-medium px-4 border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formConfirmacionGlobal" action="" method="post">
                        <!-- El método siempre es DELETE hacia la ruta resource. El backend evalúa si restaura o elimina de verdad -->
                        @method('DELETE') 
                        @csrf
                        <button type="submit" class="btn fw-medium px-4 shadow-sm" id="modalBtnConfirm">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filtro-variantes-form');
        
        if(filterForm && window.jQuery) {
            $('.selectpicker').on('changed.bs.select', () => filterForm.submit());
        }

        const modalConfirmObj = document.getElementById('modalConfirmacionGlobal');
        const modalConfirm = modalConfirmObj ? new bootstrap.Modal(modalConfirmObj) : null;
        
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-confirmar');
            if (btn && modalConfirm) {
                const { id, sku, talla, accion } = btn.dataset;
                const form = document.getElementById('formConfirmacionGlobal');
                form.action = `/producto-variantes/${id}`;

                const icon = document.getElementById('modalIcon');
                const title = document.getElementById('modalTitle');
                const desc = document.getElementById('modalDesc');
                const btnSubmit = document.getElementById('modalBtnConfirm');

                if (accion === 'desactivar') {
                    icon.innerHTML = '<i class="fas fa-ban fa-4x opacity-75 text-danger"></i>';
                    title.textContent = '¿Desactivar variante?';
                    desc.innerHTML = `El SKU <strong>${sku}</strong> (Talla ${talla}) no podrá ser seleccionado.`;
                    btnSubmit.className = 'btn btn-danger fw-bold px-4 shadow-sm';
                } else {
                    icon.innerHTML = '<i class="fas fa-check-circle fa-4x opacity-75 text-success"></i>';
                    title.textContent = '¿Activar variante?';
                    desc.innerHTML = `El SKU <strong>${sku}</strong> volverá a estar disponible en el inventario.`;
                    btnSubmit.className = 'btn btn-success fw-bold px-4 shadow-sm';
                }

                modalConfirm.show();
            }
        });
    });
</script>
@endpush