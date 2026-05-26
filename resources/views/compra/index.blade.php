@extends('layouts.app')

@section('title', 'Historial de Compras')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<style>
    .table-custom th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .table-custom td {
        vertical-align: middle;
        color: #495057;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Historial de Compras</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Compras</li>
            </ol>
        </div>

        @can('crear-compra')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('compras.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Registrar Compra
            </a>
        </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-store"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Registros de Transacciones</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Proveedor</th>
                            <th>Fecha y Hora</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Estado</th>
                            @canany(['mostrar-compra', 'eliminar-compra'])
                            <th class="text-center">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $item)
                            <tr>
                                <td>
                                    <div class="fw-medium text-dark">
                                        {{ optional($item->comprobante)->tipo_comprobante ?? 'Sin comprobante' }}
                                    </div>
                                    <div class="text-muted small">N° {{ $item->numero_comprobante ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">
                                        {{ optional(optional($item->proveedor)->persona)->razon_social ?? 'Sin proveedor' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ optional(optional($item->proveedor)->persona)->tipo_persona ? ucfirst(optional($item->proveedor)->persona->tipo_persona) : '' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark">
                                        <i class="far fa-calendar-alt me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($item->fecha_hora)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="far fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->fecha_hora)->format('H:i') }} hrs
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    S/ {{ number_format($item->total, 2) }}
                                </td>
                                <td class="text-center align-content-center">
                                    @if((int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Completada</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Anulada</span>
                                    @endif
                                </td>

                                @canany(['mostrar-compra', 'eliminar-compra'])
                                <td class="text-center align-content-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        @can('mostrar-compra')
                                        <a href="{{ route('compras.show', $item) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar-compra')
                                            @if((int) $item->estado === 1)
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-danger border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Anular compra">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-success border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Restaurar compra">
                                                    <i class="fas fa-trash-restore-alt"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>

                            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center pb-4">
                                            @if((int) $item->estado === 1)
                                                <div class="text-danger mb-3"><i class="fas fa-times-circle fa-4x"></i></div>
                                                <h4 class="fw-bold text-dark">¿Anular esta compra?</h4>
                                                <p class="text-muted">Se anulará la transacción con comprobante <strong>{{ $item->numero_comprobante }}</strong>.</p>
                                            @else
                                                <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x"></i></div>
                                                <h4 class="fw-bold text-dark">¿Restaurar esta compra?</h4>
                                                <p class="text-muted">La transacción <strong>{{ $item->numero_comprobante }}</strong> volverá a figurar como completada.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-0 pt-0 justify-content-center">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('compras.destroy', $item) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn {{ (int) $item->estado === 1 ? 'btn-danger' : 'btn-success' }} px-4">
                                                    Confirmar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush