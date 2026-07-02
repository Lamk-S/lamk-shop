@extends('layouts.app')

@section('title', 'Historial de Ventas')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .table-soft thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .fs-7 { font-size: 0.875rem; }
    .fs-8 { font-size: 0.75rem; }
    .table-wrap { border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0; }
    .tabular-nums { font-variant-numeric: tabular-nums; font-family: ui-monospace, SFMono-Regular, Consolas, monospace; }
    .bootstrap-select > .dropdown-toggle { background-color: #fff !important; border: 1px solid #dee2e6 !important; }
    .bootstrap-select > .dropdown-toggle:focus { outline: none !important; border-color: #0dcaf0 !important; box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25) !important; }
    .filter-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.3rem; }
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
@php
    $canView = auth()->user()->can('registrar_ventas') || auth()->user()->can('anular_ventas');
    $canAnnul = auth()->user()->can('anular_ventas');
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-0">Monitor de Ventas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Tickets y Facturas</li>
            </ol>
        </div>

        @can('registrar_ventas')
            <div>
                <a href="{{ route('ventas.create') }}" class="btn btn-info text-dark fw-bold shadow-sm rounded-pill px-4">
                    <i class="fas fa-cart-plus me-2"></i>Nueva Venta en POS
                </a>
            </div>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('ventas.index') }}" id="filtro-ventas-form" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="filter-label">Cliente / Comprador</label>
                    <select name="cliente_id" id="cliente_id" class="form-control selectpicker show-tick" data-live-search="true" data-size="6" title="Todos los clientes">
                        <option value="">-- Todos --</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected((string) request('cliente_id') === (string) $cliente->id)>
                                {{ $cliente->persona?->numero_documento }} - {{ $cliente->persona?->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-6">
                    <label class="filter-label">Estado Doc.</label>
                    <select name="estado_documento" id="estado_documento" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($optionsEstadoDocumento as $value => $label)
                            <option value="{{ $value }}" @selected(request('estado_documento') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-6">
                    <label class="filter-label">Comprobante</label>
                    <select name="comprobante_id" id="comprobante_id" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($comprobantes as $comprobante)
                            <option value="{{ $comprobante->id }}" @selected((string) request('comprobante_id') === (string) $comprobante->id)>
                                {{ $comprobante->tipo_comprobante }} ({{ $comprobante->serie }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-6">
                    <label class="filter-label">Método Pago</label>
                    <select name="metodo_pago" id="metodo_pago" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($optionsMetodosPago as $value => $label)
                            <option value="{{ $value }}" @selected(request('metodo_pago') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1 w-50">
                            <label class="filter-label">Desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" class="form-control shadow-sm" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="flex-grow-1 w-50">
                            <label class="filter-label">Hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control shadow-sm" value="{{ request('fecha_hasta') }}">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex justify-content-end">
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary w-100" data-bs-toggle="tooltip" title="Limpiar filtros">
                        <i class="fas fa-eraser"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-soft">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Registro de Ventas</h5>
            <div class="text-muted small">Historial de ventas realizadas por terminal.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Comprobante</th>
                            <th>Cliente</th>
                            <th>Fecha y Hora</th>
                            <th>Cajero</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Total Pagado</th>
                            @if($canView)
                                <th class="text-center pe-4">Docs</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ $item->tipo_comprobante ? ($item->tipo_comprobante . ' ' . $item->serie . '-' . $item->correlativo) : 'TICKET INTERNO' }}
                                    </div>
                                    <div class="text-muted fs-8">
                                        <i class="fas fa-hashtag me-1"></i>Op: {{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ Str::limit($item->cliente_nombre ?? 'Público General', 30) }}
                                    </div>
                                    <div class="text-muted fs-8 text-uppercase">
                                        @if($item->cliente_tipo_documento)
                                            <i class="fas {{ $item->cliente_tipo_documento === 'RUC' ? 'fa-building' : 'fa-id-card' }} me-1"></i>
                                            {{ $item->cliente_tipo_documento }} {{ $item->cliente_numero_documento ?? '—' }}
                                        @else
                                            <i class="fas fa-walking me-1"></i> CLIENTE DE PASO
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark fs-7 mb-1 tabular-nums">
                                        <i class="fas fa-calendar-day text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('d/m/Y') ?? '—' }}
                                    </div>
                                    <div class="text-muted fs-8 tabular-nums">
                                        <i class="fas fa-clock text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('H:i') ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fs-7 fw-medium">{{ explode(' ', $item->user?->name ?? 'Sistema')[0] }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeProps = match($item->estado_documento) {
                                            'ANULADA' => ['color' => 'danger', 'icon' => 'fa-ban'],
                                            'EMITIDA' => ['color' => 'success', 'icon' => 'fa-check'],
                                            'PENDIENTE' => ['color' => 'warning', 'icon' => 'fa-hourglass-half'],
                                            default => ['color' => 'secondary', 'icon' => 'fa-file-invoice'],
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeProps['color'] }} bg-opacity-10 text-{{ $badgeProps['color'] }} border border-{{ $badgeProps['color'] }} border-opacity-25 px-2 py-1 rounded">
                                        <i class="fas {{ $badgeProps['icon'] }} me-1"></i> {{ $item->estado_documento }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark fs-6 tabular-nums">
                                    S/ {{ number_format((float) $item->total, 2) }}
                                </td>
                                @if($canView)
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm" role="group">
                                            @can('registrar_ventas')
                                                <a href="{{ route('ventas.show', $item) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Ver comprobante detallado">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            @endcan

                                            @if($canAnnul)
                                                @if($item->estado_documento !== 'ANULADA')
                                                    <button type="button" class="btn btn-sm btn-light border text-danger" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Anular operación">
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
                                <td colspan="{{ $canView ? 7 : 6 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-cash-register text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Sin historial en esta fecha</h5>
                                        <p class="text-muted mb-0">No se encontraron ventas con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <form method="GET" action="{{ route('ventas.index') }}" class="d-flex align-items-center gap-2" id="pagination-form">
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm shadow-sm" style="width: auto;">
                    @foreach ([10, 15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <div class="text-muted small border-start ps-3 ms-2">
                    Viendo {{ $ventas->firstItem() ?? 0 }} a {{ $ventas->lastItem() ?? 0 }} de {{ $ventas->total() }} registros
                </div>
            </form>
            <div>
                {{ $ventas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@if($canAnnul)
    @foreach ($ventas as $item)
        @if($item->estado_documento !== 'ANULADA')
            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center pb-4 px-4">
                            <div class="text-danger mb-3"><i class="fas fa-circle-exclamation fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Anular transacción?</h4>
                            <p class="text-muted mb-0">
                                La operación <strong>{{ $item->tipo_comprobante ? ($item->tipo_comprobante . ' ' . $item->serie . '-' . $item->correlativo) : str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</strong> será revertida. Los productos volverán al stock del almacén.
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal">Mantener venta</button>
                            <form action="{{ route('ventas.destroy', $item) }}" method="post">
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
        const filterForm = document.getElementById('filtro-ventas-form');
        const paginationForm = document.getElementById('pagination-form');
        
        $('#cliente_id').on('changed.bs.select', function () {
            filterForm.submit();
        });

        const nativeSelects = document.querySelectorAll('#estado_documento, #comprobante_id, #metodo_pago');
        nativeSelects.forEach(select => {
            select.addEventListener('change', () => filterForm.submit());
        });

        const dateInputs = document.querySelectorAll('#fecha_desde, #fecha_hasta');
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