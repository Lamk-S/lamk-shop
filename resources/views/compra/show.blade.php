@extends('layouts.app')
@section('title', 'Detalles de Compra')

@section('content')
@php
    $pagos = $compra->cuentaPorPagar?->pagos ?? collect();
    $estadoPago = $compra->estado_pago ?? ($compra->cuentaPorPagar?->estado ?? 'PENDIENTE');
    $saldoPendiente = (float) ($compra->saldo_pendiente ?? $compra->cuentaPorPagar?->saldo_pendiente ?? 0);
    $montoPagado = (float) ($compra->monto_pagado ?? $compra->cuentaPorPagar?->monto_pagado ?? 0);
    $puedeRegistrarPago = $compra->cuentaPorPagar && in_array($estadoPago, ['PENDIENTE', 'PARCIAL'], true);
@endphp

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3 d-print-none">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Detalles de la Compra</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('compras.index') }}" class="text-decoration-none text-muted">Compras</a></li>
                    <li class="breadcrumb-item active fw-medium text-dark">Ficha de ingreso</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('compras.index') }}" class="btn btn-light shadow-sm border px-4 fw-medium">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            @if($puedeRegistrarPago)
                <button type="button" class="btn btn-warning shadow-sm px-4 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#pagoCompraModal">
                    <i class="fas fa-wallet me-2"></i>Abonar pago
                </button>
            @endif
            <button onclick="window.print()" class="btn btn-secondary shadow-sm px-4 fw-medium">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    @include('layouts.partials.alert')

    <div class="row g-4">
        <div class="col-xl-8">
            <!-- Resumen -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0 fw-semibold text-dark fs-6 fs-md-5">
                        <i class="fa-solid fa-file-invoice text-primary me-2"></i>Resumen del Comprobante
                    </h5>
                    <span class="badge {{ $compra->estado_documento === 'ANULADA' ? 'bg-danger' : 'bg-success' }} bg-opacity-10 text-{{ $compra->estado_documento === 'ANULADA' ? 'danger' : 'success' }} border px-3 py-2 rounded-pill fs-7">
                        {{ $compra->estado_documento === 'ANULADA' ? 'COMPRA ANULADA' : 'COMPRA RECEPCIONADA' }}
                    </span>
                </div>
                <div class="card-body p-3 p-md-4 bg-light bg-opacity-50">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="summary-label">Tipo</div>
                            <div class="summary-value text-break">{{ $compra->tipo_comprobante ?? optional($compra->comprobante)->tipo_comprobante ?? 'Sin comprobante' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="summary-label">Número</div>
                            <div class="summary-value">{{ $compra->serie && $compra->correlativo ? $compra->serie . '-' . $compra->correlativo : '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="summary-label">Fecha y Hora</div>
                            <div class="summary-value">{{ optional($compra->fecha_emision)->format('d/m/Y H:i') ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="summary-label">Método de pago</div>
                            <div class="summary-value">{{ $compra->metodo_pago ?? 'N/A' }}</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="summary-label">Proveedor</div>
                            <div class="summary-value">{{ $compra->proveedor_nombre ?? optional($compra->proveedor?->persona)->nombre_completo ?? 'Sin proveedor' }}</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="summary-label">Documento</div>
                            <div class="summary-value">{{ $compra->proveedor_tipo_documento ?? 'N/A' }} {{ $compra->proveedor_numero_documento ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <h5 class="mb-0 fw-semibold text-dark fs-6 fs-md-5">
                        <i class="fa-solid fa-box-open text-primary me-2"></i>Productos Adquiridos
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-custom mb-0 align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th class="ps-3 ps-md-4">Producto</th>
                                    <th class="text-center">Talla</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Costo Unit.</th>
                                    <th class="text-end">Desc.</th>
                                    <th class="text-end">IGV</th>
                                    <th class="text-end pe-3 pe-md-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($compra->detalles as $item)
                                    <tr>
                                        <td class="ps-3 ps-md-4 fw-medium text-dark">
                                            <div>{{ $item->producto_nombre }}</div>
                                            <div class="small text-muted">{{ $item->producto_codigo }}</div>
                                        </td>
                                        <td class="text-center">{{ $item->talla_nombre ?? 'Sin talla' }}</td>
                                        <td class="text-center">{{ $item->cantidad }}</td>
                                        <td class="text-end">S/ {{ number_format((float) $item->costo_unitario, 2) }}</td>
                                        <td class="text-end text-danger">S/ {{ number_format((float) $item->descuento, 2) }}</td>
                                        <td class="text-end">S/ {{ number_format((float) $item->impuesto, 2) }}</td>
                                        <td class="text-end pe-3 pe-md-4 text-dark fw-bold">S/ {{ number_format((float) $item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay detalles registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light bg-opacity-50 border-0 p-3 p-md-4">
                    <div class="row justify-content-end">
                        <div class="col-12 col-sm-8 col-md-6 col-lg-5">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Subtotal:</span>
                                <strong>S/ {{ number_format((float) $compra->subtotal, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Descuento:</span>
                                <strong class="text-danger">- S/ {{ number_format((float) $compra->descuento_total, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">IGV:</span>
                                <strong>S/ {{ number_format((float) $compra->impuesto_total, 2) }}</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-5">
                                <span class="fw-bold text-dark">TOTAL:</span>
                                <span class="fw-bold text-primary">S/ {{ number_format((float) $compra->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial Pagos -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <h5 class="mb-0 fw-semibold text-dark fs-6 fs-md-5">
                        <i class="fa-solid fa-credit-card text-success me-2"></i>Historial de Pagos
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($pagos->count())
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0 text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="ps-3 ps-md-4">Método</th>
                                        <th class="text-end">Monto</th>
                                        <th>Referencia</th>
                                        <th>Observación</th>
                                        <th class="text-end pe-3 pe-md-4">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $pago)
                                        <tr>
                                            <td class="ps-3 ps-md-4">{{ $pago->metodo_pago }}</td>
                                            <td class="text-end fw-medium">S/ {{ number_format((float) $pago->monto, 2) }}</td>
                                            <td>{{ $pago->referencia_operacion ?: '—' }}</td>
                                            <td>{{ $pago->observacion ?: '—' }}</td>
                                            <td class="text-end pe-3 pe-md-4">{{ optional($pago->fecha_pago ?? $pago->created_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">No hay pagos registrados para esta compra.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Lateral (Estado de Pago y Proveedor) -->
        <div class="col-xl-4 no-print">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <h5 class="mb-0 fw-semibold text-dark fs-6 fs-md-5">Estado de Pago</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="mb-4">
                        <span class="badge bg-{{ $estadoPago === 'PAGADA' ? 'success' : ($estadoPago === 'PARCIAL' ? 'warning' : ($estadoPago === 'ANULADA' ? 'secondary' : 'danger')) }} text-{{ $estadoPago === 'PAGADA' ? 'success' : ($estadoPago === 'PARCIAL' ? 'warning' : ($estadoPago === 'ANULADA' ? 'secondary' : 'danger')) }} bg-opacity-10 border px-3 py-2 rounded-pill w-100 text-center fs-6">
                            {{ $estadoPago }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Compra:</span>
                        <strong class="text-dark">S/ {{ number_format((float) $compra->total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Abonado:</span>
                        <strong class="text-success">S/ {{ number_format($montoPagado, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted fw-bold">Saldo Pendiente:</span>
                        <strong class="text-danger fs-5">S/ {{ number_format($saldoPendiente, 2) }}</strong>
                    </div>
                    @if($compra->fecha_vencimiento)
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Vencimiento:</span>
                        <strong>{{ $compra->fecha_vencimiento->format('d/m/Y') }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <h5 class="mb-0 fw-semibold text-dark fs-6 fs-md-5">Datos del Proveedor</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="mb-3">
                        <div class="summary-label">Razón Social / Nombre</div>
                        <div class="fw-medium text-dark">{{ $compra->proveedor_nombre ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="summary-label">Documento</div>
                        <div>{{ $compra->proveedor_tipo_documento ?? '' }} {{ $compra->proveedor_numero_documento ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="summary-label">Dirección</div>
                        <div class="text-break">{{ $compra->proveedor_direccion ?? '—' }}</div>
                    </div>
                    <div class="mb-0">
                        <div class="summary-label">Contacto</div>
                        <div><i class="fas fa-phone small text-muted me-2"></i>{{ $compra->proveedor_telefono ?? '—' }}</div>
                        <div><i class="fas fa-envelope small text-muted me-2"></i>{{ $compra->proveedor_email ?? '—' }}</div>
                    </div>
                </div>
            </div>
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