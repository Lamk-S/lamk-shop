@extends('layouts.app')

@section('title', 'Movimientos de Tesorería')

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
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Movimientos de Tesorería</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Movimientos</li>
            </ol>
        </div>
    </div>

    <form class="card border-0 shadow-sm rounded-4 mb-4" method="GET" action="{{ route('movimientos-tesoreria.index') }}">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tesorería</label>
                    <select name="tesoreria_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($tesorerias as $tesoreria)
                            <option value="{{ $tesoreria->id }}" {{ request('tesoreria_id') == $tesoreria->id ? 'selected' : '' }}>
                                {{ $tesoreria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="INGRESO" {{ request('tipo') === 'INGRESO' ? 'selected' : '' }}>Ingreso</option>
                        <option value="EGRESO" {{ request('tipo') === 'EGRESO' ? 'selected' : '' }}>Egreso</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('movimientos-tesoreria.index') }}" class="btn btn-light px-4">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-right-left"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Historial de movimientos</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tesorería</th>
                            <th>Origen</th>
                            <th>Descripción</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Saldo anterior</th>
                            <th class="text-end">Saldo posterior</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td>{{ optional($movimiento->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $movimiento->tesoreria?->nombre ?? 'N/A' }}</td>
                                <td>{{ $movimiento->origen }}</td>
                                <td>{{ $movimiento->descripcion }}</td>
                                <td class="text-center">
                                    @if($movimiento->tipo === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Egreso</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">S/ {{ number_format($movimiento->monto, 2) }}</td>
                                <td class="text-end">S/ {{ number_format($movimiento->saldo_anterior, 2) }}</td>
                                <td class="text-end">S/ {{ number_format($movimiento->saldo_posterior, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-center text-muted">No hay movimientos registrados.</td>
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