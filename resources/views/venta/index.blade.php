@extends('layouts.app')
@section('title', 'Historial de Ventas')

@section('content')
@php
    $canView = auth()->user()->can('registrar_ventas') || auth()->user()->can('anular_ventas');
    $canAnnul = auth()->user()->can('anular_ventas');
@endphp

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Monitor de Ventas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Tickets y Facturas</li>
            </ol>
        </div>

        @can('registrar_ventas')
            <div>
                <a href="{{ route('ventas.create') }}" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
                    <i class="fas fa-cart-plus me-2"></i>Nueva Venta en POS
                </a>
            </div>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <!-- Filtro -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('ventas.index') }}" id="filtro-ventas-form">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Cliente / Comprador</label>
                        <select name="cliente_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" data-size="6" title="Todos los clientes">
                            <option value="">-- Todos --</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected((string) request('cliente_id') === (string) $cliente->id)>
                                    {{ $cliente->persona?->numero_documento }} - {{ $cliente->persona?->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Estado Doc.</label>
                        <select name="estado_documento" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($optionsEstadoDocumento as $value => $label)
                                <option value="{{ $value }}" @selected(request('estado_documento') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Comprobante</label>
                        <select name="comprobante_id" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($comprobantes as $comprobante)
                                <option value="{{ $comprobante->id }}" @selected((string) request('comprobante_id') === (string) $comprobante->id)>
                                    {{ $comprobante->tipo_comprobante }} ({{ $comprobante->serie }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Método Pago</label>
                        <select name="metodo_pago" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            @foreach ($optionsMetodosPago as $value => $label)
                                <option value="{{ $value }}" @selected(request('metodo_pago') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-12">
                        <label class="form-label text-secondary fw-bold small text-uppercase mb-1">Fechas</label>
                        <div class="input-group shadow-sm">
                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" title="Desde">
                            <input type="date" name="fecha_hasta" class="form-control border-start-0" value="{{ request('fecha_hasta') }}" title="Hasta">
                        </div>
                    </div>

                    <!-- Botones de Acción de Filtro -->
                    <div class="col-12 d-flex gap-2 justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm fw-medium">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-light border shadow-sm" data-bs-toggle="tooltip" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Registro de Ventas</h5>
                <small class="text-muted">Historial de transacciones realizadas por terminal.</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">Comprobante</th>
                            <th class="border-bottom-0">Cliente</th>
                            <th class="border-bottom-0">Fecha y Hora</th>
                            <th class="border-bottom-0">Cajero</th>
                            <th class="text-center border-bottom-0">Estado</th>
                            <th class="text-end border-bottom-0">Total Pagado</th>
                            @if($canView)
                                <th class="text-center pe-4 border-bottom-0">Docs</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $item)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ $item->tipo_comprobante ? ($item->tipo_comprobante->value . ' ' . $item->serie . '-' . $item->correlativo) : 'TICKET INTERNO' }}
                                    </div>
                                    <div class="text-muted small font-monospace">
                                        <i class="fas fa-hashtag me-1"></i>Op: {{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1 text-wrap" style="max-width: 250px;">
                                        {{ Str::limit($item->cliente_nombre ?? 'Público General', 30) }}
                                    </div>
                                    <div class="text-muted small text-uppercase">
                                        @if($item->cliente_tipo_documento)
                                            <i class="fas {{ $item->cliente_tipo_documento === 'RUC' ? 'fa-building' : 'fa-id-card' }} me-1"></i>
                                            {{ $item->cliente_tipo_documento }} {{ $item->cliente_numero_documento ?? '—' }}
                                        @else
                                            <i class="fas fa-walking me-1"></i> CLIENTE DE PASO
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark fs-7 mb-1 font-monospace">
                                        <i class="fas fa-calendar-day text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('d/m/Y') ?? '—' }}
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
                                        <span class="fs-7 fw-medium text-dark">{{ explode(' ', $item->user?->name ?? 'Sistema')[0] }}</span>
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
                                <td class="text-end fw-bold text-dark fs-6 font-monospace py-3">
                                    S/ {{ number_format((float) $item->total, 2) }}
                                </td>
                                @if($canView)
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm">
                                            @can('registrar_ventas')
                                                <a href="{{ route('ventas.show', $item) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Ver comprobante detallado">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            @endcan
                                            @if($canAnnul)
                                                @if($item->estado_documento !== 'ANULADA')
                                                    @php
                                                        $nombreOperacion = $item->tipo_comprobante ? ($item->tipo_comprobante->value . ' ' . $item->serie . '-' . $item->correlativo) : str_pad($item->id, 6, '0', STR_PAD_LEFT);
                                                    @endphp
                                                    <button type="button" class="btn btn-sm btn-light border text-danger btn-anular" 
                                                        data-id="{{ $item->id }}"
                                                        data-operacion="{{ $nombreOperacion }}"
                                                        title="Anular operación">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-light border text-muted disabled">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
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
                                        <h5 class="fw-bold text-dark mb-1">Inventario estático</h5>
                                        <p class="text-muted mb-0">No se encontraron ventas con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <form method="GET" action="{{ route('ventas.index') }}" class="d-flex align-items-center gap-2" id="pagination-form">
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                <select name="per_page" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                    @foreach ([10, 15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <div class="text-muted small border-start ps-3 ms-2">
                    Viendo {{ $ventas->firstItem() ?? 0 }} a {{ $ventas->lastItem() ?? 0 }} de {{ $ventas->total() }} registros
                </div>
            </form>
            <div class="pagination-custom">
                {{ $ventas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@if($canAnnul)
<div class="modal fade" id="modalAnularGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center pb-4 px-4">
                <div class="text-danger mb-3"><i class="fas fa-circle-exclamation fa-4x opacity-75"></i></div>
                <h4 class="fw-bold text-dark">¿Anular transacción?</h4>
                <p class="text-muted mb-0">
                    La operación <strong id="textoOperacionGlobal"></strong> será revertida. Los productos volverán al stock del almacén.
                </p>
                <form id="formAnularGlobal" action="" method="post" class="mt-3">
                    @method('DELETE')
                    @csrf
                    <input type="hidden" name="motivo_anulacion" value="Anulada desde el historial por el usuario">
                    
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border" data-bs-dismiss="modal">Mantener venta</button>
                        <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm">Sí, Anular</button>
                    </div>
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

        const modalAnularObj = document.getElementById('modalAnularGlobal');
        if (modalAnularObj) {
            const modalAnular = new bootstrap.Modal(modalAnularObj);
            
            document.querySelectorAll('.btn-anular').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const operacion = this.dataset.operacion;
                    document.getElementById('formAnularGlobal').action = `/ventas/${id}`;
                    document.getElementById('textoOperacionGlobal').textContent = operacion;
                    modalAnular.show();
                });
            });
        }
    });
</script>
@endpush