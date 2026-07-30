@extends('layouts.app')
@section('title', 'Historial de Compras')

@section('content')
@php
    $canView = auth()->user()->can('registrar_compras') || auth()->user()->can('anular_compras');
    $canAnnul = auth()->user()->can('anular_compras');
@endphp

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Monitor de Abastecimiento</h2>
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

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('compras.index') }}" id="filtro-compras-form">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Proveedor</label>
                        <select name="proveedor_id" id="proveedor_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" data-size="7" title="Todos los proveedores">
                            <option value="">-- Todos --</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" @selected((string) request('proveedor_id') === (string) $proveedor->id)>
                                    {{ $proveedor->persona?->numero_documento ?? '—' }} - {{ $proveedor->persona?->nombre_completo ?? $proveedor->persona?->razon_social ?? 'Proveedor' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Estado doc.</label>
                        <select name="estado_documento" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($optionsEstadoDocumento as $value => $label)
                                <option value="{{ $value }}" @selected(request('estado_documento') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Estado pago</label>
                        <select name="estado_pago" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($optionsEstadoPago as $value => $label)
                                <option value="{{ $value }}" @selected(request('estado_pago') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Fechas</label>
                        <div class="input-group shadow-sm">
                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" title="Desde">
                            <input type="date" name="fecha_hasta" class="form-control border-start-0" value="{{ request('fecha_hasta') }}" title="Hasta">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm fw-medium">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                        <a href="{{ route('compras.index') }}" class="btn btn-light border shadow-sm" data-bs-toggle="tooltip" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-store"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Registros de Transacciones</h5>
                <small class="text-muted">Compras con ingreso de mercadería y trazabilidad.</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">Comprobante</th>
                            <th class="border-bottom-0">Proveedor</th>
                            <th class="border-bottom-0">Fecha y Hora</th>
                            <th class="border-bottom-0">Operador</th>
                            <th class="text-center border-bottom-0">Doc. / Pago</th>
                            <th class="text-end border-bottom-0">Total</th>
                            @if($canView)
                                <th class="text-center pe-4 border-bottom-0">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($compras as $item)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ $item->tipo_comprobante ? ($item->tipo_comprobante->value . ' ' . $item->serie . '-' . $item->correlativo) : 'INTERNO' }}
                                    </div>
                                    <div class="text-muted small font-monospace">
                                        <i class="fas fa-hashtag me-1"></i>{{ $item->correlativo ?? str_pad($item->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1 text-wrap" style="max-width: 200px;">
                                        {{ Str::limit($item->proveedor_nombre ?? optional($item->proveedor?->persona)->nombre_completo ?? 'Proveedor General', 25) }}
                                    </div>
                                    <div class="text-muted small text-uppercase">
                                        @php $doc = $item->proveedor_tipo_documento; @endphp
                                        <i class="fas {{ $doc === 'RUC' ? 'fa-building' : 'fa-id-card' }} me-1"></i>
                                        {{ $doc ?? 'DOC' }} {{ $item->proveedor_numero_documento ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark fs-7 mb-1 font-monospace">
                                        <i class="fas fa-calendar-alt text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('d/m/Y') ?? '—' }}
                                    </div>
                                    <div class="text-muted small font-monospace">
                                        <i class="fas fa-clock text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('H:i') ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 28px; height: 28px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <span class="fs-7 fw-medium text-dark">{{ explode(' ', $item->user?->name ?? 'N/A')[0] }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeDocColor = match($item->estado_documento) {
                                            'ANULADA' => 'danger',
                                            'RECEPCIONADA' => 'success',
                                            'PENDIENTE' => 'warning',
                                            default => 'secondary'
                                        };
                                        $pagoColor = match($item->estado_pago) {
                                            'PAGADA' => 'success',
                                            'PARCIAL' => 'warning',
                                            'PENDIENTE', 'ANULADA' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <span class="badge bg-{{ $badgeDocColor }} bg-opacity-10 text-{{ $badgeDocColor }} border border-{{ $badgeDocColor }} border-opacity-25 px-2 py-1 rounded">
                                            <i class="fas fa-file-invoice me-1"></i>{{ $item->estado_documento }}
                                        </span>
                                        <span class="badge bg-{{ $pagoColor }} bg-opacity-10 text-{{ $pagoColor }} border border-{{ $pagoColor }} border-opacity-25 px-2 py-1 rounded">
                                            <i class="fas fa-wallet me-1"></i>{{ $item->estado_pago ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-dark fs-6 font-monospace py-3">
                                    S/ {{ number_format((float) $item->total, 2) }}
                                </td>
                                @if($canView)
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('compras.show', $item) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($canAnnul)
                                                @if($item->estado_documento !== 'ANULADA')
                                                    <button type="button" class="btn btn-sm btn-light border text-danger btn-anular" 
                                                        data-id="{{ $item->id }}" 
                                                        data-comprobante="{{ $item->tipo_comprobante ? ($item->tipo_comprobante->value . ' ' . $item->serie . '-' . $item->correlativo) : str_pad($item->id, 6, '0', STR_PAD_LEFT) }}"
                                                        title="Anular compra">
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

        <!-- Paginación -->
        <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <form method="GET" action="{{ route('compras.index') }}" class="d-flex align-items-center gap-2" id="pagination-form">
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                    @foreach ([10, 15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <div class="text-muted small border-start ps-3 ms-2">
                    Viendo {{ $compras->firstItem() ?? 0 }} a {{ $compras->lastItem() ?? 0 }} de {{ $compras->total() }} registros
                </div>
            </form>
            <div class="pagination-custom">
                {{ $compras->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@if($canAnnul)
    <!-- Modal Global de Confirmación -->
    <div class="modal fade" id="modalAnularGlobal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center pb-4 px-4">
                    <div class="text-danger mb-3"><i class="fas fa-triangle-exclamation fa-4x opacity-75"></i></div>
                    <h4 class="fw-bold text-dark">¿Anular abastecimiento?</h4>
                    <p class="text-muted mb-0">
                        La compra <strong id="modalAnularDoc"></strong> será revertida y el stock ingresado se descontará del inventario general.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border" data-bs-dismiss="modal">Mantener compra</button>
                    <form id="formAnularGlobal" action="" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm">Sí, Anular</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const modalObj = document.getElementById('modalAnularGlobal');
        if (modalObj) {
            const modalGlobal = new bootstrap.Modal(modalObj);
            document.querySelectorAll('.btn-anular').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const doc = this.dataset.comprobante;
                    document.getElementById('modalAnularDoc').textContent = doc;
                    document.getElementById('formAnularGlobal').action = `/compras/${id}`;
                    modalGlobal.show();
                });
            });
        }
    });
</script>
@endpush