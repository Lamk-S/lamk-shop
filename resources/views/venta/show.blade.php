@extends('layouts.app')
@section('title', 'Detalle de Venta')

@push('css')
<style>
    .invoice-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; margin-bottom: 0.2rem; }
    .invoice-value { font-size: 1.05rem; color: #212529; font-weight: 500; }
    .table-invoice th { background-color: #f8f9fa; color: #49595f; font-weight: 600; white-space: nowrap; }
    .totals-row th { font-size: 1rem; } 
    .totals-row.grand-total th { font-size: 1.15rem; color: #0d6efd; }
    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .card { box-shadow: none !important; border: 0 !important; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
        <div>
            <h2 class="fw-bold text-dark mb-0">Detalle de Venta</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" class="text-decoration-none">Ventas</a></li>
                <li class="breadcrumb-item active">Ver recibo</li>
            </ol>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn btn-light shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            @can('anular_ventas')
                @if($venta->estado_documento !== 'ANULADA')
                    <button type="button" class="btn btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#anularVentaModal">
                        <i class="fas fa-ban me-2"></i>Anular
                    </button>
                @endif
            @endcan
        </div>
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 1100px;">
        <div class="card-body p-4 p-md-5 border-bottom">
            <div class="row align-items-center mb-4">
                <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                    <h3 class="fw-bold text-primary mb-0">Recibo de Venta</h3>
                    <span class="badge bg-{{ $venta->estado_documento === 'ANULADA' ? 'danger' : 'success' }} mt-2">
                        {{ $venta->estado_documento === 'ANULADA' ? 'Venta Anulada' : 'Venta Activa' }}
                    </span>
                </div>
                <div class="col-sm-6 text-center text-sm-end">
                    <div class="invoice-label">N° de Comprobante</div>
                    <h4 class="fw-bold text-dark mb-0">
                        {{ $venta->tipo_comprobante ? ($venta->tipo_comprobante . ' ' . $venta->serie . '-' . $venta->correlativo) : 'Sin comprobante' }}
                    </h4>
                    <div class="text-muted small">{{ $venta->comprobante?->tipo_comprobante ?? 'Sin comprobante' }}</div>
                </div>
            </div>

            <div class="row bg-light p-4 rounded-3 g-4">
                <div class="col-md-4">
                    <div class="invoice-label"><i class="fas fa-user-tie me-1"></i> Cliente</div>
                    <div class="invoice-value">{{ $venta->cliente_nombre ?? 'Consumidor final' }}</div>
                    <div class="small text-muted">
                        {{ $venta->cliente_tipo_documento ?? 'SIN DOCUMENTO' }}
                        {{ $venta->cliente_numero_documento ?? '' }}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="invoice-label"><i class="fas fa-calendar-alt me-1"></i> Fecha</div>
                    <div class="invoice-value">{{ optional($venta->fecha_emision)->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="invoice-label"><i class="fas fa-clock me-1"></i> Hora</div>
                    <div class="invoice-value">{{ optional($venta->fecha_emision)->format('H:i') ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="invoice-label"><i class="fas fa-user me-1"></i> Vendedor / Caja</div>
                    <div class="invoice-value">
                        {{ $venta->user?->name ?? 'N/A' }}
                        <div class="small text-muted">
                            {{ $venta->sesionCaja?->caja?->nombre ?? 'Sin caja' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="invoice-label">Método(s) de pago</div>
                    <div class="invoice-value">
                        {{ $venta->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="invoice-label">Monto recibido</div>
                    <div class="invoice-value">S/ {{ number_format((float) $venta->monto_recibido, 2) }}</div>
                </div>
                <div class="col-md-4">
                    <div class="invoice-label">Vuelto entregado</div>
                    <div class="invoice-value">S/ {{ number_format((float) $venta->vuelto_entregado, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-invoice mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 ps-md-5">Descripción del Producto</th>
                            <th class="text-center">Talla</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Desc.</th>
                            <th class="text-end">IGV</th>
                            <th class="text-end pe-4 pe-md-5">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($venta->detalles as $item)
                            <tr>
                                <td class="ps-4 ps-md-5 py-3 text-dark fw-medium">
                                    <div>{{ $item->producto_nombre }}</div>
                                    <div class="small text-muted">{{ $item->producto_codigo }}</div>
                                </td>
                                <td class="text-center py-3">{{ $item->talla_nombre ?? 'Sin talla' }}</td>
                                <td class="text-center py-3">{{ $item->cantidad }}</td>
                                <td class="text-end py-3 text-muted">S/ {{ number_format((float) $item->precio_unitario, 2) }}</td>
                                <td class="text-end py-3 text-danger">
                                    {{ (float) $item->descuento > 0 ? '-S/ ' . number_format((float) $item->descuento, 2) : 'S/ 0.00' }}
                                </td>
                                <td class="text-end py-3 text-muted">S/ {{ number_format((float) $item->impuesto, 2) }}</td>
                                <td class="text-end pe-4 pe-md-5 py-3 fw-bold text-dark">
                                    S/ {{ number_format((float) $item->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No hay detalles para esta venta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light border-top">
                        <tr class="totals-row">
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Subtotal bruto:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->subtotal, 2) }}</th>
                        </tr>
                        <tr class="totals-row">
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Descuento total:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->descuento_total, 2) }}</th>
                        </tr>
                        <tr class="totals-row">
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">IGV:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->impuesto_total, 2) }}</th>
                        </tr>
                        <tr class="totals-row">
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Monto recibido:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->monto_recibido, 2) }}</th>
                        </tr>
                        <tr class="totals-row">
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Vuelto entregado:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->vuelto_entregado, 2) }}</th>
                        </tr>
                        <tr class="totals-row grand-total border-top">
                            <th colspan="6" class="text-end py-3">Total Venta:</th>
                            <th class="text-end pe-4 pe-md-5 py-3">S/ {{ number_format((float) $venta->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($venta->pagos->count())
            <div class="card-body border-top">
                <h5 class="fw-semibold text-dark mb-3"><i class="fas fa-credit-card text-primary me-2"></i>Pagos registrados</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th class="text-end">Monto</th>
                                <th>Referencia</th>
                                <th class="text-end">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->pagos as $pago)
                                <tr>
                                    <td>{{ $pago->metodo_pago }}</td>
                                    <td class="text-end">S/ {{ number_format((float) $pago->monto, 2) }}</td>
                                    <td>{{ $pago->referencia_operacion ?? '—' }}</td>
                                    <td class="text-end">{{ optional($pago->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

@can('anular_ventas')
    @if($venta->estado_documento !== 'ANULADA')
        <div class="modal fade" id="anularVentaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('ventas.destroy', $venta) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-semibold">Anular venta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                Esta acción revertirá inventario, caja y/o tesorería según corresponda.
                            </div>
                            <div class="mb-3">
                                <label for="motivo_anulacion" class="form-label fw-medium">Motivo de anulación</label>
                                <textarea class="form-control" name="motivo_anulacion" id="motivo_anulacion" rows="4" maxlength="1000" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Confirmar anulación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endcan
@endsection