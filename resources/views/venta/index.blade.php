@extends('layouts.app')

@section('title', 'Registro de Ventas')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
    .table-custom td { vertical-align: middle; color: #495057; }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <!-- Encabezado y Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Registro de Ventas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Ventas</li>
            </ol>
        </div>
        
        @can('crear-venta')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('ventas.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-cart-plus me-2"></i>Nueva Venta
            </a>
        </div>
        @endcan
    </div>

    <!-- Contenedor de la Tabla -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Historial de Ventas</h5>
        </div>
        
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th>Fecha y Hora</th>
                            <th>Vendedor</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Total</th>
                            @canany(['mostrar-venta', 'eliminar-venta'])
                            <th class="text-center" style="width: 120px;">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventas as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1">{{ $item->comprobante->tipo_comprobante }}</div>
                                    <div class="text-muted fs-8"><i class="fas fa-hashtag me-1"></i>{{ $item->numero_comprobante }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-7 mb-1">{{ $item->cliente->persona->razon_social }}</div>
                                    <div class="text-muted fs-8 text-uppercase"><i class="fas fa-user-tie me-1"></i>{{ $item->cliente->persona->tipo_persona }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark fs-7 mb-1"><i class="fas fa-calendar-alt text-secondary me-2"></i>{{ \Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y') }}</div>
                                    <div class="text-muted fs-8"><i class="fas fa-clock text-secondary me-2"></i>{{ \Carbon\Carbon::parse($item->fecha_hora)->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fs-7">{{ $item->user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center align-content-center">
                                    @if($item->estado == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Activa</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Anulada</span>
                                    @endif
                                </td>
                                <td class="text-end align-content-center fw-bold text-primary">
                                    {{ number_format($item->total, 2) }}
                                </td>
                                
                                @canany(['mostrar-venta', 'eliminar-venta'])
                                <td class="text-center align-content-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        @can('mostrar-venta')
                                        <a href="{{ route('ventas.show', ['venta' => $item]) }}" class="btn btn-sm btn-outline-secondary text-info border-light" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('eliminar-venta')
                                            @if($item->estado == 1)
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-danger border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Anular Venta">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-success border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Restaurar Venta">
                                                    <i class="fas fa-trash-restore-alt"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>

                            <!-- Modal de Confirmación -->
                            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center pb-4">
                                            @if($item->estado == 1)
                                                <div class="text-danger mb-3"><i class="fas fa-ban fa-4x opacity-75"></i></div>
                                                <h4 class="fw-bold text-dark">¿Anular esta venta?</h4>
                                                <p class="text-muted">La venta {{ $item->numero_comprobante }} pasará a estado anulado.</p>
                                            @else
                                                <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x opacity-75"></i></div>
                                                <h4 class="fw-bold text-dark">¿Restaurar esta venta?</h4>
                                                <p class="text-muted">La venta {{ $item->numero_comprobante }} volverá a estar activa.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-0 pt-0 justify-content-center">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('ventas.destroy',['venta'=>$item->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn {{ $item->estado == 1 ? 'btn-danger' : 'btn-success' }} px-4">
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