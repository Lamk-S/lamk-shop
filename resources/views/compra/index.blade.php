@extends('layouts.app')

@section('title', 'Historial de Compras')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.82rem; letter-spacing: .02em; white-space: nowrap; border-bottom: 2px solid #dee2e6; }
    .table-custom td { vertical-align: middle; color: #495057; }
    .fs-7 { font-size: 0.875rem; }
    .fs-8 { font-size: 0.8rem; }
    .table-wrap { border-radius: 1rem; overflow: hidden; border: 1px solid #dee2e6; }
    .pagination { margin-bottom: 0; }
    .filter-card .form-label { font-size: .75rem; font-weight: 700; color: #6c757d; margin-bottom: .35rem; text-transform: uppercase; }
    .badge-soft { border: 1px solid rgba(0,0,0,.06); padding: .45rem .8rem; border-radius: 999px; font-weight: 600; font-size: .78rem; }
    .summary-chip { background: #f8f9fa; border: 1px solid #eef1f4; border-radius: 999px; padding: .35rem .75rem; font-size: .8rem; color: #6c757d; }
    .bootstrap-select > .dropdown-toggle { background-color: #fff !important; border: 1px solid #dee2e6 !important; }
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
@php
    $canView = auth()->user()->can('registrar_compras') || auth()->user()->can('anular_compras');
    $canAnnul = auth()->user()->can('anular_compras');
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-0">Monitor de Abastecimiento</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Historial de Compras</li>
            </ol>
        </div>

        @can('registrar_compras')
            <div>
                <a href="{{ route('compras.create') }}" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
                    <i class="fas fa-truck-loading me-2"></i>Registrar Compra
                </a>
            </div>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4 filter-card">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('compras.index') }}" id="filtro-compras-form">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Proveedor</label>
                        <select name="proveedor_id" id="proveedor_id" class="form-control selectpicker show-tick shadow-sm" data-live-search="true" data-size="7" title="Todos los proveedores">
                            <option value="">-- Todos --</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" @selected((string) request('proveedor_id') === (string) $proveedor->id)>
                                    {{ $proveedor->persona?->numero_documento ?? '—' }} - {{ $proveedor->persona?->nombre_completo ?? $proveedor->persona?->razon_social ?? 'Proveedor' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Estado doc.</label>
                        <select name="estado_documento" id="estado_documento" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($optionsEstadoDocumento as $value => $label)
                                <option value="{{ $value }}" @selected(request('estado_documento') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Estado pago</label>
                        <select name="estado_pago" id="estado_pago" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($optionsEstadoPago as $value => $label)
                                <option value="{{ $value }}" @selected(request('estado_pago') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Método Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($optionsMetodoPago as $value => $label)
                                <option value="{{ $value }}" @selected(request('metodo_pago') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-8">
                        <div class="d-flex gap-2">
                            <div class="w-50">
                                <label class="form-label">Desde</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control shadow-sm fs-7 p-1" value="{{ request('fecha_desde') }}">
                            </div>
                            <div class="w-50">
                                <label class="form-label">Hasta</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control shadow-sm fs-7 p-1" value="{{ request('fecha_hasta') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-4 d-flex justify-content-end">
                        <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary w-100" data-bs-toggle="tooltip" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-semibold text-dark">Registros de Transacciones</h5>
                    <small class="text-muted">Compras con ingreso de mercadería y trazabilidad.</small>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive table-wrap border-0">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Comprobante</th>
                            <th>Proveedor</th>
                            <th>Fecha y Hora</th>
                            <th>Operador</th>
                            <th class="text-center">Método</th>
                            <th class="text-center">Doc.</th>
                            <th class="text-center">Pago</th>
                            <th class="text-end">Total</th>
                            @if($canView)
                                <th class="text-center pe-4">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($compras as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ $item->tipo_comprobante ? ($item->tipo_comprobante . ' ' . $item->serie . '-' . $item->correlativo) : 'INTERNO' }}
                                    </div>
                                    <div class="text-muted fs-8 font-monospace">
                                        <i class="fas fa-hashtag me-1"></i>{{ $item->correlativo ?? str_pad($item->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ Str::limit($item->proveedor_nombre ?? optional($item->proveedor?->persona)->nombre_completo ?? 'Proveedor General', 25) }}
                                    </div>
                                    <div class="text-muted fs-8 text-uppercase">
                                        @php
                                            $doc = $item->proveedor_tipo_documento;
                                        @endphp
                                        <i class="fas {{ $doc === 'RUC' ? 'fa-building' : 'fa-id-card' }} me-1"></i>
                                        {{ $doc ?? 'DOC' }} {{ $item->proveedor_numero_documento ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark fs-7 mb-1 font-monospace">
                                        <i class="fas fa-calendar-alt text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('d/m/Y') ?? '—' }}
                                    </div>
                                    <div class="text-muted fs-8 font-monospace">
                                        <i class="fas fa-clock text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('H:i') ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fs-7">{{ explode(' ', $item->user?->name ?? 'N/A')[0] }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded">
                                        {{ $item->metodo_pago ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeColor = match($item->estado_documento) {
                                            'ANULADA' => 'danger',
                                            'RECEPCIONADA' => 'success',
                                            'PENDIENTE' => 'warning',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} border-opacity-25 px-2 py-1 rounded">
                                        {{ $item->estado_documento }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $pagoColor = match($item->estado_pago) {
                                            'PAGADA' => 'success',
                                            'PARCIAL' => 'warning',
                                            'PENDIENTE', 'ANULADA' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $pagoColor }} bg-opacity-10 text-{{ $pagoColor }} border border-{{ $pagoColor }} border-opacity-25 px-2 py-1 rounded">
                                        {{ $item->estado_pago ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-danger fs-6 font-monospace">
                                    S/ {{ number_format((float) $item->total, 2) }}
                                </td>
                                @if($canView)
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm" role="group">
                                            <a href="{{ route('compras.show', $item) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($canAnnul)
                                                @if($item->estado_documento !== 'ANULADA')
                                                    <button type="button" class="btn btn-sm btn-light border text-danger" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Anular compra">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @else
                                                    <span class="btn btn-sm btn-light border text-muted disabled">
                                                        <i class="fas fa-ban"></i>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canView ? 9 : 8 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-boxes-stacked text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">Inventario estático</h5>
                                        <p class="text-muted mb-0">Aún no se han registrado compras con los filtros seleccionados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <form method="GET" action="{{ route('compras.index') }}" class="d-flex align-items-center gap-2" id="pagination-form">
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm shadow-sm" style="width: auto;">
                    @foreach ([10, 15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <div class="text-muted small border-start ps-3 ms-2">
                    Viendo {{ $compras->firstItem() ?? 0 }} a {{ $compras->lastItem() ?? 0 }} de {{ $compras->total() }} registros
                </div>
            </form>
            <div>
                {{ $compras->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@if($canAnnul)
    @foreach ($compras as $item)
        @if($item->estado_documento !== 'ANULADA')
            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center pb-4 px-4">
                            <div class="text-danger mb-3"><i class="fas fa-triangle-exclamation fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Anular abastecimiento?</h4>
                            <p class="text-muted mb-0">
                                La compra <strong>{{ $item->tipo_comprobante ? ($item->tipo_comprobante . ' ' . $item->serie . '-' . $item->correlativo) : str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</strong> será revertida y el stock ingresado se descontará.
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border" data-bs-dismiss="modal">Mantener compra</button>
                            <form action="{{ route('compras.destroy', $item) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill">Sí, Anular</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filtro-compras-form');
        const paginationForm = document.getElementById('pagination-form');
        
        $('#proveedor_id').on('changed.bs.select', function () {
            filterForm.submit();
        });

        const nativeSelects = filterForm.querySelectorAll('select:not(.selectpicker)');
        nativeSelects.forEach(select => {
            select.addEventListener('change', () => filterForm.submit());
        });

        const dateInputs = filterForm.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            input.addEventListener('change', () => filterForm.submit());
        });

        document.getElementById('per_page').addEventListener('change', () => {
            paginationForm.submit();
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush