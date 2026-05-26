@extends('layouts.app')

@section('title', 'Tesorería')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<style>
    .summary-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 .125rem .5rem rgba(0,0,0,.08);
    }
    .summary-label {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: .25rem;
    }
    .summary-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #212529;
    }
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
            <h2 class="fw-bold text-dark mb-0">Tesorería</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Tesorería</li>
            </ol>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('movimientos-tesoreria.index') }}" class="btn btn-outline-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-list me-2"></i>Ver movimientos
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body p-4">
                    <div class="summary-label">Nombre</div>
                    <div class="summary-value">{{ $tesoreria->nombre }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body p-4">
                    <div class="summary-label">Saldo en efectivo</div>
                    <div class="summary-value">S/ {{ number_format($tesoreria->saldo_efectivo ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card summary-card">
                <div class="card-body p-4">
                    <div class="summary-label">Saldo en banco</div>
                    <div class="summary-value">S/ {{ number_format($tesoreria->saldo_banco ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Movimientos recientes</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
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
                                <td colspan="7" class="py-5 text-center text-muted">No hay movimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection