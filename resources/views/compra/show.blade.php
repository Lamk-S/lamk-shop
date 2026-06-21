@extends('layouts.app')

@section('title', 'Detalles de Compra')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; border-bottom: 2px solid #e9ecef; white-space: nowrap; }
    .table-custom td { vertical-align: middle; color: #495057; } 
    .summary-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; margin-bottom: 0.2rem; }
    .summary-value { font-size: 1.05rem; color: #212529; font-weight: 500; }
    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .card { box-shadow: none !important; border: 0 !important; }
    }
</style>
@endpush

@section('content')
@php
    $pagos = $compra->cuentaPorPagar?->pagos ?? collect();
    $estadoPago = $compra->estado_pago ?? ($compra->cuentaPorPagar?->estado ?? 'PENDIENTE');
    $saldoPendiente = (float) ($compra->saldo_pendiente ?? $compra->cuentaPorPagar?->saldo_pendiente ?? 0);
    $montoPagado = (float) ($compra->monto_pagado ?? $compra->cuentaPorPagar?->monto_pagado ?? 0);
    $puedeRegistrarPago = $compra->cuentaPorPagar && in_array($estadoPago, ['PENDIENTE', 'PARCIAL'], true);
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
        <div>
            <h2 class="page-title mb-0">Detalles de la Compra</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('compras.index') }}" class="text-decoration-none text-muted">Compras</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Ver compra</li>
            </ol>
        </div>

        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('compras.index') }}" class="btn btn-light shadow-sm border px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver al historial
            </a>

            @if($puedeRegistrarPago)
                <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#pagoCompraModal">
                    <i class="fas fa-wallet me-2"></i>Registrar pago
                </button>
            @endif

            <button onclick="window.print()" class="btn btn-secondary shadow-sm">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    @include('layouts.partials.alert')

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fa-solid fa-file-invoice text-primary me-2"></i>Resumen del Comprobante
                    </h5>

                    <span class="badge {{ $compra->estado_documento === 'ANULADA' ? 'bg-danger' : 'bg-success' }} bg-opacity-10 text-{{ $compra->estado_documento === 'ANULADA' ? 'danger' : 'success' }} border px-3 py-2 rounded-pill">
                        {{ $compra->estado_documento === 'ANULADA' ? 'COMPRA ANULADA' : 'COMPRA RECEPCIONADA' }}
                    </span>
                </div>

                <div class="card-body p-4 p-md-5 bg-light bg-opacity-50">
                    <div class="row g-4">
                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Tipo Comprobante</p>
                            <p class="summary-value">{{ $compra->tipo_comprobante ?? optional($compra->comprobante)->tipo_comprobante ?? 'Sin comprobante' }}</p>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Número</p>
                            <p class="summary-value">
                                {{ $compra->serie && $compra->correlativo ? $compra->serie . '-' . $compra->correlativo : '—' }}
                            </p>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Fecha y Hora</p>
                            <p class="summary-value">{{ optional($compra->fecha_emision)->format('d/m/Y - H:i') ?? '—' }}</p>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Proveedor</p>
                            <p class="summary-value">
                                {{ $compra->proveedor_nombre ?? optional($compra->proveedor?->persona)->nombre_completo ?? 'Sin proveedor' }}
                            </p>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Documento proveedor</p>
                            <p class="summary-value">
                                {{ $compra->proveedor_tipo_documento ?? 'N/A' }} {{ $compra->proveedor_numero_documento ?? '' }}
                            </p>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Método de pago</p>
                            <p class="summary-value">{{ $compra->metodo_pago ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Registrado por</p>
                            <p class="summary-value">{{ $compra->user?->name ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label">Observación</p>
                            <p class="summary-value">{{ $compra->observacion ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fa-solid fa-box-open text-primary me-2"></i>Productos Adquiridos
                    </h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-custom mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-center">Talla</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Costo Unit. (S/)</th>
                                    <th class="text-end">Desc. (S/)</th>
                                    <th class="text-end">IGV (S/)</th>
                                    <th class="text-end pe-4">Total Línea (S/)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($compra->detalles as $item)
                                    <tr>
                                        <td class="ps-4 fw-medium text-dark">
                                            <div>{{ $item->producto_nombre }}</div>
                                            <div class="small text-muted">{{ $item->producto_codigo }}</div>
                                        </td>
                                        <td class="text-center">{{ $item->talla_nombre ?? 'Sin talla' }}</td>
                                        <td class="text-center">{{ $item->cantidad }}</td>
                                        <td class="text-end">{{ number_format((float) $item->costo_unitario, 2) }}</td>
                                        <td class="text-end text-danger">{{ number_format((float) $item->descuento, 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $item->impuesto, 2) }}</td>
                                        <td class="text-end pe-4 text-dark fw-medium">
                                            {{ number_format((float) $item->total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No hay detalles para esta compra.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-light bg-opacity-50 border-0 p-4">
                    <div class="row g-3 justify-content-end">
                        <div class="col-md-5 col-lg-4">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Subtotal:</span>
                                <strong>S/ {{ number_format((float) $compra->subtotal, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Descuento total:</span>
                                <strong class="text-danger">S/ {{ number_format((float) $compra->descuento_total, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">IGV:</span>
                                <strong>S/ {{ number_format((float) $compra->impuesto_total, 2) }}</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-5">
                                <span class="fw-bold text-dark">TOTAL FINAL:</span>
                                <span class="fw-bold text-primary">S/ {{ number_format((float) $compra->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fa-solid fa-credit-card text-success me-2"></i>Pagos de la compra
                    </h5>
                </div>

                <div class="card-body p-4">
                    @if($pagos->count())
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Método</th>
                                        <th class="text-end">Monto</th>
                                        <th>Referencia</th>
                                        <th>Observación</th>
                                        <th class="text-end">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $pago)
                                        <tr>
                                            <td>{{ $pago->metodo_pago }}</td>
                                            <td class="text-end">S/ {{ number_format((float) $pago->monto, 2) }}</td>
                                            <td>{{ $pago->referencia_operacion ?? '—' }}</td>
                                            <td>{{ $pago->observacion ?? '—' }}</td>
                                            <td class="text-end">{{ optional($pago->fecha_pago ?? $pago->created_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            No hay pagos registrados para esta compra.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4 no-print">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fa-solid fa-receipt text-success me-2"></i>Estado de pago
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="summary-label">Estado</div>
                        <span class="badge bg-{{ $estadoPago === 'PAGADA' ? 'success' : ($estadoPago === 'PARCIAL' ? 'warning' : ($estadoPago === 'ANULADA' ? 'secondary' : 'danger')) }} bg-opacity-10 text-{{ $estadoPago === 'PAGADA' ? 'success' : ($estadoPago === 'PARCIAL' ? 'warning' : ($estadoPago === 'ANULADA' ? 'secondary' : 'danger')) }} border px-3 py-2 rounded-pill">
                            {{ $estadoPago }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total:</span>
                        <strong>S/ {{ number_format((float) $compra->total, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Pagado:</span>
                        <strong>S/ {{ number_format($montoPagado, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Pendiente:</span>
                        <strong class="text-danger">S/ {{ number_format($saldoPendiente, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Vencimiento:</span>
                        <strong>{{ $compra->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pago total:</span>
                        <strong>{{ $compra->fecha_pago_total?->format('d/m/Y H:i') ?? '—' }}</strong>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fa-solid fa-user-tag text-primary me-2"></i>Datos históricos del proveedor
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="mb-2">
                        <div class="summary-label">Nombre</div>
                        <div class="summary-value">{{ $compra->proveedor_nombre ?? '—' }}</div>
                    </div>

                    <div class="mb-2">
                        <div class="summary-label">Documento</div>
                        <div class="summary-value">
                            {{ $compra->proveedor_tipo_documento ?? '—' }} {{ $compra->proveedor_numero_documento ?? '' }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="summary-label">Dirección</div>
                        <div class="summary-value">{{ $compra->proveedor_direccion ?? '—' }}</div>
                    </div>

                    <div class="mb-2">
                        <div class="summary-label">Teléfono</div>
                        <div class="summary-value">{{ $compra->proveedor_telefono ?? '—' }}</div>
                    </div>

                    <div class="mb-0">
                        <div class="summary-label">Correo</div>
                        <div class="summary-value">{{ $compra->proveedor_email ?? '—' }}</div>
                    </div>
                </div>
            </div>

            @if($compra->cuentaPorPagar)
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-wallet text-warning me-2"></i>Cuenta por pagar
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Estado:</span>
                            <strong>{{ $compra->cuentaPorPagar->estado }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pagado:</span>
                            <strong>S/ {{ number_format((float) $compra->cuentaPorPagar->monto_pagado, 2) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pendiente:</span>
                            <strong class="text-danger">S/ {{ number_format((float) $compra->cuentaPorPagar->saldo_pendiente, 2) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Vencimiento:</span>
                            <strong>{{ $compra->cuentaPorPagar->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</strong>
                        </div>

                        @if($pagos->count())
                            <hr>
                            <div class="small fw-semibold text-muted mb-2">Pagos registrados</div>
                            <div class="vstack gap-2">
                                @foreach ($pagos as $pago)
                                    <div class="border rounded-3 p-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="small fw-semibold">{{ $pago->metodo_pago }}</span>
                                            <span class="small">S/ {{ number_format((float) $pago->monto, 2) }}</span>
                                        </div>
                                        <div class="small text-muted">
                                            {{ optional($pago->fecha_pago ?? $pago->created_at)->format('d/m/Y H:i') ?? '—' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($puedeRegistrarPago)
    <div class="modal fade" id="pagoCompraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('cuentas-por-pagar.pagos.store', $compra->cuentaPorPagar) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar pago de compra</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-light border mb-3">
                            <div class="small text-muted">Saldo pendiente</div>
                            <div class="fw-bold fs-5">S/ {{ number_format($saldoPendiente, 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Método de pago</label>
                            <select name="metodo_pago" class="form-select" required>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TARJETA">Tarjeta</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="YAPE">Yape</option>
                                <option value="PLIN">Plin</option>
                                <option value="OTRO">Otro</option>
                            </select>
                            @error('metodo_pago')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Monto</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $saldoPendiente }}" name="monto" class="form-control" required>
                            @error('monto')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Referencia operación</label>
                            <input type="text" name="referencia_operacion" class="form-control" maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observación</label>
                            <textarea name="observacion" class="form-control" rows="3" maxlength="255"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Guardar pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection