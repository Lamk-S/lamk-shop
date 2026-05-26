@extends('layouts.app')

@section('title', 'Detalle de Sesión de Caja')

@push('css')
<style>
    .invoice-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; margin-bottom: 0.2rem; }
    .invoice-value { font-size: 1.05rem; color: #212529; font-weight: 500; }
    .table-invoice th { background-color: #f8f9fa; color: #495495; font-weight: 600; }
    .totals-row th { font-size: 1.05rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Detalle de Sesión de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none">Sesiones de Caja</a></li>
                <li class="breadcrumb-item active">Ver detalle</li>
            </ol>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 1000px;">
        <div class="card-body p-4 p-md-5 border-bottom">
            <div class="row align-items-center mb-4">
                <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                    <h3 class="fw-bold text-primary mb-0">Sesión de Caja #{{ $sesionCaja->id }}</h3>
                    <span class="badge bg-{{ (int) $sesionCaja->estado === 1 ? 'success' : 'secondary' }} mt-2">
                        {{ (int) $sesionCaja->estado === 1 ? 'Sesión Abierta' : 'Sesión Cerrada' }}
                    </span>
                </div>

                <div class="col-sm-6 text-center text-sm-end">
                    <div class="invoice-label">Caja</div>
                    <h4 class="fw-bold text-dark mb-0">{{ $sesionCaja->caja?->nombre ?? 'N/A' }}</h4>
                    <div class="text-muted small">{{ $sesionCaja->user?->name ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="row bg-light p-4 rounded-3 g-4">
                <div class="col-md-2 col-6">
                    <div class="invoice-label">
                        <i class="fas fa-calendar-alt me-1"></i> Apertura
                    </div>
                    <div class="invoice-value">
                        {{ $sesionCaja->fecha_hora_apertura
                            ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_apertura)->format('d/m/Y H:i')
                            : '-' }}
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="invoice-label"><i class="fas fa-calendar-check me-1"></i> Cierre</div>
                    <div class="invoice-value">
                        {{ $sesionCaja->fecha_hora_cierre ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_cierre)->format('d/m/Y H:i') : 'Abierta' }}
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="invoice-label"><i class="fas fa-vault me-1"></i> Fondo fijo</div>
                    <div class="invoice-value">{{ number_format($sesionCaja->caja?->fondo_fijo ?? 100, 2) }}</div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="invoice-label"><i class="fas fa-hand-holding-usd me-1"></i> Inicial</div>
                    <div class="invoice-value">{{ number_format($sesionCaja->saldo_inicial, 2) }}</div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="invoice-label"><i class="fas fa-calculator me-1"></i> Esperado</div>
                    <div class="invoice-value">{{ $sesionCaja->saldo_final_esperado !== null ? number_format($sesionCaja->saldo_final_esperado, 2) : '0.00' }}</div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="invoice-label"><i class="fas fa-file-invoice-dollar me-1"></i> Declarado</div>
                    <div class="invoice-value">{{ $sesionCaja->saldo_final_declarado !== null ? number_format($sesionCaja->saldo_final_declarado, 2) : '0.00' }}</div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="invoice-label"><i class="fas fa-balance-scale me-1"></i> Diferencia</div>
                    <div class="invoice-value {{ (float) ($sesionCaja->diferencia ?? 0) === 0.0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($sesionCaja->diferencia ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-invoice mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 ps-md-5">Movimiento</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end pe-4 pe-md-5">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesionCaja->movimientosCaja as $item)
                            <tr>
                                <td class="ps-4 ps-md-5 py-3 text-dark fw-medium">{{ $item->descripcion }}</td>
                                <td class="text-center py-3">
                                    @if($item->tipo === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Egreso</span>
                                    @endif
                                </td>
                                <td class="text-end py-3 fw-bold text-dark">{{ number_format($item->monto, 2) }}</td>
                                <td class="text-end pe-4 pe-md-5 py-3 text-muted">{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                                            style="width: 90px; height: 90px;">
                                            <i class="fas fa-money-bill-wave text-warning fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">
                                            Sin movimientos de caja en esta sesión
                                        </h5>
                                        <p class="text-muted mb-0">
                                            No hay registros de movimientos de caja asociados a la sesión actual.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-body border-top">
            <h5 class="fw-semibold text-dark mb-3"><i class="fas fa-receipt text-primary me-2"></i>Ventas asociadas</h5>
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesionCaja->ventas as $venta)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $venta->numero_comprobante }}</div>
                                    <div class="small text-muted">{{ $venta->comprobante?->tipo_comprobante ?? 'Sin comprobante' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $venta->cliente?->persona?->razon_social ?? 'Consumidor final' }}</div>
                                </td>
                                <td class="text-end fw-bold text-primary">{{ number_format($venta->total, 2) }}</td>
                                <td class="text-center">
                                    @if((int) $venta->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Activa</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Anulada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                                            style="width: 90px; height: 90px;">
                                            <i class="fas fa-shopping-cart text-secondary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">
                                            Sin ventas en esta sesión
                                        </h5>
                                        <p class="text-muted mb-0">
                                            No hay ventas asociadas a la sesión actual.
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