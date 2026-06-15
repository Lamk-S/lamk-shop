@extends('layouts.app')

@section('title', 'Historial de Ventas')

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.82rem; letter-spacing: .02em; white-space: nowrap; }
    .table-custom td { vertical-align: middle; color: #495057; }
    .fs-7 { font-size: 0.875rem; }
    .fs-8 { font-size: 0.8rem; }
    .table-wrap { border-radius: 1rem; overflow: hidden; }
    .pagination { margin-bottom: 0; }
</style>
@endpush

@section('content')
@php
    $canView = auth()->user()->can('registrar_ventas') || auth()->user()->can('anular_ventas');
    $canAnnul = auth()->user()->can('anular_ventas');

    $estadoDocumentoActual = request('estado_documento');
    $clienteActual = request('cliente_id');
    $comprobanteActual = request('comprobante_id');
    $metodoPagoActual = request('metodo_pago');
    $fechaDesdeActual = request('fecha_desde');
    $fechaHastaActual = request('fecha_hasta');
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Historial de Ventas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Ventas</li>
            </ol>
        </div>

        @can('registrar_ventas')
            <div class="mt-3 mt-md-0">
                <a href="{{ route('ventas.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                    <i class="fas fa-plus me-2"></i>Nueva Venta
                </a>
            </div>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('ventas.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected((string) $clienteActual === (string) $cliente->id)>
                                {{ $cliente->persona?->nombre_completo }} - {{ $cliente->persona?->numero_documento }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Estado doc.</label>
                    <select name="estado_documento" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($optionsEstadoDocumento as $value => $label)
                            <option value="{{ $value }}" @selected($estadoDocumentoActual === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Comprobante</label>
                    <select name="comprobante_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($comprobantes as $comprobante)
                            <option value="{{ $comprobante->id }}" @selected((string) $comprobanteActual === (string) $comprobante->id)>
                                {{ $comprobante->tipo_comprobante }} - {{ $comprobante->serie }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Método</label>
                    <select name="metodo_pago" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($optionsMetodosPago as $value => $label)
                            <option value="{{ $value }}" @selected($metodoPagoActual === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-1 col-md-6">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesdeActual }}">
                </div>

                <div class="col-lg-1 col-md-6">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHastaActual }}">
                </div>

                <div class="col-lg-1 col-md-6">
                    <label class="form-label">Filas</label>
                    <select name="per_page" class="form-select">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-semibold text-dark">Registros de Transacciones</h5>
                <small class="text-muted">Ventas con boleta rápida, boleta registrada o factura</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive table-wrap">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th>Fecha y Hora</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th class="text-end">Total</th>
                            @if($canView)
                                <th class="text-center">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ $item->tipo_comprobante ? ($item->tipo_comprobante . ' ' . $item->serie . '-' . $item->correlativo) : 'Sin comprobante' }}
                                    </div>
                                    <div class="text-muted fs-8">
                                        <i class="fas fa-hashtag me-1"></i>{{ $item->correlativo ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1">
                                        {{ $item->cliente_nombre ?? 'Consumidor final' }}
                                    </div>
                                    <div class="text-muted fs-8 text-uppercase">
                                        @if($item->cliente_tipo_documento)
                                            <i class="fas {{ $item->cliente_tipo_documento === 'RUC' ? 'fa-building' : 'fa-user' }} me-1"></i>
                                            {{ $item->cliente_tipo_documento }} {{ $item->cliente_numero_documento ?? '—' }}
                                        @else
                                            <i class="fas fa-user me-1"></i>
                                            CONSUMIDOR FINAL
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark fs-7 mb-1">
                                        <i class="fas fa-calendar-alt text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('d/m/Y') ?? '—' }}
                                    </div>
                                    <div class="text-muted fs-8">
                                        <i class="fas fa-clock text-secondary me-2"></i>{{ optional($item->fecha_emision)->format('H:i') ?? '—' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fs-7">{{ $item->user?->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($item->estado_documento === 'ANULADA')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">
                                            Anulada
                                        </span>
                                    @elseif($item->estado_documento === 'EMITIDA')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">
                                            Emitida
                                        </span>
                                    @elseif($item->estado_documento === 'PENDIENTE')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill">
                                            Pendiente
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill">
                                            {{ $item->estado_documento }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">
                                    S/ {{ number_format((float) $item->total, 2) }}
                                </td>
                                @if($canView)
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm" role="group">
                                            @can('registrar_ventas')
                                                <a href="{{ route('ventas.show', $item) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @if($canAnnul)
                                                @if($item->estado_documento !== 'ANULADA')
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary text-danger border-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmModal-{{ $item->id }}"
                                                            title="Anular venta">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @else
                                                    <span class="btn btn-sm btn-outline-secondary text-secondary border-light disabled" title="Venta anulada">
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
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-cart-shopping text-success fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No hay ventas registradas</h5>
                                        <p class="text-muted mb-0">Aún no se han realizado ventas en el sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div class="text-muted small">
                Mostrando {{ $ventas->firstItem() ?? 0 }} a {{ $ventas->lastItem() ?? 0 }} de {{ $ventas->total() }} registros
            </div>
            <div>
                {{ $ventas->links() }}
            </div>
        </div>
    </div>
</div>

@if($canAnnul)
    @foreach ($ventas as $item)
        @if($item->estado_documento !== 'ANULADA')
            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center pb-4">
                            <div class="text-danger mb-3"><i class="fas fa-ban fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Anular esta venta?</h4>
                            <p class="text-muted mb-0">
                                La venta <strong>{{ $item->tipo_comprobante ? ($item->tipo_comprobante . ' ' . $item->serie . '-' . $item->correlativo) : $item->id }}</strong> pasará a estado anulada.
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0 justify-content-center">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('ventas.destroy', $item) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger px-4">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection