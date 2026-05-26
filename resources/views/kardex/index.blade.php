@extends('layouts.app')

@section('title', 'Kardex de Productos')

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
            <h2 class="fw-bold text-dark mb-0">Kardex de Productos</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Kardex</li>
            </ol>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Movimientos de Inventario</h5>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('kardex.index') }}" class="row g-3 mb-4">
                <div class="col-md-5">
                    <label for="producto_id" class="form-label fw-medium text-secondary">Producto</label>
                    <select name="producto_id" id="producto_id" class="form-select">
                        <option value="">Todos los productos</option>
                        @foreach($productos as $item)
                            <option value="{{ $item->id }}" @selected(request('producto_id') == $item->id)>
                                {{ $item->codigo }} - {{ $item->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="tipo_transaccion" class="form-label fw-medium text-secondary">Tipo de transacción</label>
                    <select name="tipo_transaccion" id="tipo_transaccion" class="form-select">
                        <option value="">Todas</option>
                        <option value="COMPRA" @selected(request('tipo_transaccion') == 'COMPRA')>COMPRA</option>
                        <option value="VENTA" @selected(request('tipo_transaccion') == 'VENTA')>VENTA</option>
                        <option value="AJUSTE" @selected(request('tipo_transaccion') == 'AJUSTE')>AJUSTE</option>
                        <option value="APERTURA" @selected(request('tipo_transaccion') == 'APERTURA')>APERTURA</option>
                        <option value="ANULACION" @selected(request('tipo_transaccion') == 'ANULACION')>ANULACION</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('kardex.index') }}" class="btn btn-light flex-fill">
                        Limpiar
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Tipo</th>
                            <th>Descripción</th>
                            <th class="text-end">Entrada</th>
                            <th class="text-end">Salida</th>
                            <th class="text-end">Saldo</th>
                            <th class="text-end">Costo Unit.</th>
                            <th>Usuario</th>
                            <th class="text-end">Fecha</th>
                            <th class="text-center" style="width: 90px;">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kardex as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->producto?->nombre ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $item->producto?->codigo ?? 'Sin código' }}</div>
                                </td>

                                <td class="text-center align-content-center">
                                    @php
                                        $tipo = $item->tipo_transaccion;
                                        $badge = match ($tipo) {
                                            'COMPRA' => 'success',
                                            'VENTA' => 'danger',
                                            'AJUSTE' => 'warning',
                                            'APERTURA' => 'info',
                                            'ANULACION' => 'secondary',
                                            default => 'dark',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} border border-{{ $badge }} border-opacity-25 px-3 py-1 rounded-pill">
                                        {{ $tipo }}
                                    </span>
                                </td>

                                <td>
                                    <div class="text-dark">{{ $item->descripcion }}</div>
                                </td>

                                <td class="text-end align-content-center">
                                    <span class="fw-medium">{{ number_format($item->entrada, 0) }}</span>
                                </td>

                                <td class="text-end align-content-center">
                                    <span class="fw-medium">{{ number_format($item->salida, 0) }}</span>
                                </td>

                                <td class="text-end align-content-center fw-bold text-primary">
                                    {{ number_format($item->saldo, 0) }}
                                </td>

                                <td class="text-end align-content-center">
                                    {{ number_format($item->costo_unitario, 2) }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="small">{{ $item->user?->name ?? 'Sistema' }}</span>
                                    </div>
                                </td>

                                <td class="text-end align-content-center">
                                    <div class="small text-muted">{{ optional($item->created_at)->format('d-m-Y H:i') }}</div>
                                </td>

                                <td class="text-center align-content-center">
                                    <a href="{{ route('kardex.show', $item->id) }}" class="btn btn-sm btn-outline-secondary text-info border-light" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                                            style="width: 90px; height: 90px;">
                                            <i class="fas fa-box-open text-info fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">
                                            Sin movimientos de kardex
                                        </h5>
                                        <p class="text-muted mb-0">
                                            No existen movimientos registrados en el kardex actualmente.
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