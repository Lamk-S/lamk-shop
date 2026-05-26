@extends('layouts.app')

@section('title', 'Movimientos de Caja')

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
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Movimientos de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Movimientos de Caja</li>
            </ol>
        </div>

        @can('crear-movimiento-caja')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('movimientos-caja.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Nuevo Movimiento
            </a>
        </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Historial de Movimientos</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th>Sesión</th>
                            <th>Usuario</th>
                            <th class="text-center">Tipo</th>
                            <th>Descripción</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->sesionCaja?->caja?->nombre ?? 'N/A' }}</div>
                                    <div class="small text-muted">Sesión #{{ $item->sesion_caja_id }}</div>
                                </td>

                                <td>
                                    <div class="small">
                                        <div class="text-muted">
                                            <span class="fw-semibold text-dark">Apertura:</span>
                                            {{ $item->sesionCaja?->fecha_hora_apertura
                                                ? \Carbon\Carbon::parse($item->sesionCaja->fecha_hora_apertura)->format('d-m-Y H:i')
                                                : '-' }}
                                        </div>
                                        <div class="text-muted">
                                            <span class="fw-semibold text-dark">Cierre:</span>

                                            @if ($item->sesionCaja?->fecha_hora_cierre)
                                                {{ \Carbon\Carbon::parse($item->sesionCaja->fecha_hora_cierre)->format('d-m-Y H:i') }}
                                            @else
                                                <span class="badge bg-success-subtle text-success">
                                                    Abierta
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="small">{{ $item->sesionCaja?->user?->name ?? 'N/A' }}</span>
                                    </div>
                                </td>

                                <td class="text-center align-content-center">
                                    @if($item->tipo === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Egreso</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="text-dark">{{ $item->descripcion }}</div>
                                </td>

                                <td class="text-end align-content-center fw-bold text-primary">
                                    {{ number_format($item->monto, 2) }}
                                </td>

                                <td class="text-end align-content-center">
                                    <div class="small text-muted">{{ optional($item->created_at)->format('d-m-Y H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                                            style="width: 90px; height: 90px;">
                                            <i class="fas fa-money-bill-wave text-success fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">
                                            No hay movimientos de caja registrados.
                                        </h5>
                                        <p class="text-muted mb-0">
                                            Aún no existen registros de movimiento de caja para mostrar.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
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