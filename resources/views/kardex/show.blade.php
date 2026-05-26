@extends('layouts.app')

@section('title', 'Detalle de Kardex')

@push('css')
<style>
    .invoice-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; margin-bottom: 0.2rem; }
    .invoice-value { font-size: 1.05rem; color: #212529; font-weight: 500; }
    .table-invoice th { background-color: #f8f9fa; color: #495057; font-weight: 600; }
    .totals-row th { font-size: 1.05rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Detalle de Kardex</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kardex.index') }}" class="text-decoration-none">Kardex</a></li>
                <li class="breadcrumb-item active">Ver movimiento</li>
            </ol>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('kardex.index') }}" class="btn btn-light shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 900px;">
        <div class="card-body p-4 p-md-5 border-bottom">
            <div class="row align-items-center mb-4">
                <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                    <h3 class="fw-bold text-primary mb-0">Movimiento de Inventario</h3>
                    @php
                        $tipo = $kardex->tipo_transaccion;
                        $badge = match ($tipo) {
                            'COMPRA' => 'success',
                            'VENTA' => 'danger',
                            'AJUSTE' => 'warning',
                            'APERTURA' => 'info',
                            'ANULACION' => 'secondary',
                            default => 'dark',
                        };
                    @endphp
                    <span class="badge bg-{{ $badge }} mt-2">{{ $tipo }}</span>
                </div>

                <div class="col-sm-6 text-center text-sm-end">
                    <div class="invoice-label">Producto</div>
                    <h4 class="fw-bold text-dark mb-0">{{ $kardex->producto?->nombre ?? 'N/A' }}</h4>
                    <div class="text-muted small">{{ $kardex->producto?->codigo ?? 'Sin código' }}</div>
                </div>
            </div>

            <div class="row bg-light p-4 rounded-3 g-4">
                <div class="col-md-4">
                    <div class="invoice-label"><i class="fas fa-align-left me-1"></i> Descripción</div>
                    <div class="invoice-value">{{ $kardex->descripcion }}</div>
                </div>

                <div class="col-md-2">
                    <div class="invoice-label"><i class="fas fa-arrow-down me-1"></i> Entrada</div>
                    <div class="invoice-value">{{ number_format($kardex->entrada, 0) }}</div>
                </div>

                <div class="col-md-2">
                    <div class="invoice-label"><i class="fas fa-arrow-up me-1"></i> Salida</div>
                    <div class="invoice-value">{{ number_format($kardex->salida, 0) }}</div>
                </div>

                <div class="col-md-2">
                    <div class="invoice-label"><i class="fas fa-layer-group me-1"></i> Saldo</div>
                    <div class="invoice-value">{{ number_format($kardex->saldo, 0) }}</div>
                </div>

                <div class="col-md-2">
                    <div class="invoice-label"><i class="fas fa-coins me-1"></i> Costo</div>
                    <div class="invoice-value">{{ number_format($kardex->costo_unitario, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-invoice mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 ps-md-5">Campo</th>
                            <th class="pe-4 pe-md-5">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 ps-md-5 py-3 text-muted">Usuario responsable</td>
                            <td class="pe-4 pe-md-5 py-3 fw-medium">{{ $kardex->user?->name ?? 'Sistema' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 ps-md-5 py-3 text-muted">Fecha y hora</td>
                            <td class="pe-4 pe-md-5 py-3 fw-medium">{{ optional($kardex->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 ps-md-5 py-3 text-muted">Tipo de transacción</td>
                            <td class="pe-4 pe-md-5 py-3">
                                <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} border border-{{ $badge }} border-opacity-25 px-3 py-1 rounded-pill">
                                    {{ $kardex->tipo_transaccion }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 ps-md-5 py-3 text-muted">Producto</td>
                            <td class="pe-4 pe-md-5 py-3 fw-medium">{{ $kardex->producto?->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 ps-md-5 py-3 text-muted">Código</td>
                            <td class="pe-4 pe-md-5 py-3 fw-medium">{{ $kardex->producto?->codigo ?? 'Sin código' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection