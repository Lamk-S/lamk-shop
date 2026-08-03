@extends('layouts.app')
@section('title', 'Detalle de Venta')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Cabecera de acciones -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 d-print-none gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Detalle de Venta</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" class="text-decoration-none text-muted">Ventas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Ver recibo</li>
            </ol>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn btn-light border shadow-sm fw-medium">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button type="button" onclick="imprimirTicketSilencioso('{{ route('ventas.ticket', $venta->id) }}')" class="btn btn-secondary shadow-sm fw-medium">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            @can('anular_ventas')
                @if($venta->estado_documento !== 'ANULADA')
                    <button type="button" class="btn btn-danger shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#anularVentaModal">
                        <i class="fas fa-ban me-2"></i>Anular
                    </button>
                @endif
            @endcan
        </div>
    </div>

    @include('layouts.partials.alert')

    <!-- Tarjeta Principal del Recibo -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 1100px;">
        <div class="card-body p-4 p-md-5 border-bottom">
            <div class="row align-items-center mb-5">
                <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                    <h3 class="fw-bolder text-primary mb-0">Recibo de Venta</h3>
                    <span class="badge bg-{{ $venta->estado_documento === 'ANULADA' ? 'danger' : 'success' }} bg-opacity-10 text-{{ $venta->estado_documento === 'ANULADA' ? 'danger' : 'success' }} border border-{{ $venta->estado_documento === 'ANULADA' ? 'danger' : 'success' }} border-opacity-25 mt-2 px-3 py-2 rounded-pill">
                        {{ $venta->estado_documento === 'ANULADA' ? 'Venta Anulada' : 'Venta Activa' }}
                    </span>
                </div>
                <div class="col-sm-6 text-center text-sm-end">
                    <div class="text-uppercase small text-muted fw-bold mb-1 letter-spacing-1">N° de Comprobante</div>
                    <h4 class="fw-bold text-dark mb-0">
                        {{ $venta->tipo_comprobante ? ($venta->tipo_comprobante->value . ' ' . $venta->serie . '-' . $venta->correlativo) : 'Sin comprobante' }}
                    </h4>
                    <div class="text-muted small">{{ $venta->comprobante?->tipo_comprobante->value ?? 'Sin comprobante' }}</div>
                </div>
            </div>

            <!-- Metadatos de la venta -->
            <div class="row bg-light p-4 rounded-4 g-4 border">
                <div class="col-md-4">
                    <div class="text-uppercase small text-muted fw-bold mb-1"><i class="fas fa-user-tie me-1"></i> Cliente</div>
                    <div class="fs-5 fw-semibold text-dark">{{ $venta->cliente_nombre ?? 'Consumidor final' }}</div>
                    <div class="small text-muted">
                        {{ $venta->cliente_tipo_documento ?? 'SIN DOCUMENTO' }}
                        {{ $venta->cliente_numero_documento ?? '' }}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-uppercase small text-muted fw-bold mb-1"><i class="fas fa-calendar-alt me-1"></i> Fecha</div>
                    <div class="fs-5 fw-semibold text-dark font-monospace">{{ optional($venta->fecha_emision)->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-uppercase small text-muted fw-bold mb-1"><i class="fas fa-clock me-1"></i> Hora</div>
                    <div class="fs-5 fw-semibold text-dark font-monospace">{{ optional($venta->fecha_emision)->format('H:i') ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-uppercase small text-muted fw-bold mb-1"><i class="fas fa-user me-1"></i> Vendedor / Caja</div>
                    <div class="fs-5 fw-semibold text-dark">
                        {{ $venta->user?->name ?? 'N/A' }}
                    </div>
                    <div class="small text-muted">
                        {{ $venta->sesionCaja?->caja?->nombre ?? 'Sin caja' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-uppercase small text-muted fw-bold mb-1">Método(s) de pago</div>
                    <div class="fs-5 fw-semibold text-dark">
                        {{ $venta->pagos->pluck('metodo_pago')->map(fn($m) => $m->value ?? $m)->unique()->implode(', ') ?: 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-uppercase small text-muted fw-bold mb-1">Monto recibido</div>
                    <div class="fs-5 fw-semibold text-dark font-monospace">S/ {{ number_format((float) $venta->monto_recibido, 2) }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-uppercase small text-muted fw-bold mb-1">Vuelto entregado</div>
                    <div class="fs-5 fw-semibold text-dark font-monospace">S/ {{ number_format((float) $venta->vuelto_entregado, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Detalles de Productos -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 ps-md-5 py-3 border-bottom-0">Descripción del Producto</th>
                            <th class="text-center py-3 border-bottom-0">Talla</th>
                            <th class="text-center py-3 border-bottom-0">Cant.</th>
                            <th class="text-end py-3 border-bottom-0">Precio Unit.</th>
                            <th class="text-end py-3 border-bottom-0">Desc.</th>
                            <th class="text-end py-3 border-bottom-0">IGV</th>
                            <th class="text-end pe-4 pe-md-5 py-3 border-bottom-0">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($venta->detalles as $item)
                            <tr>
                                <td class="ps-4 ps-md-5 py-3 text-dark fw-medium">
                                    <div>{{ $item->producto_nombre }}</div>
                                    <div class="small text-muted font-monospace">{{ $item->producto_codigo }}</div>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-dark border">{{ $item->talla_nombre ?? 'Sin talla' }}</span>
                                </td>
                                <td class="text-center py-3 fw-bold">{{ $item->cantidad }}</td>
                                <td class="text-end py-3 text-muted font-monospace">S/ {{ number_format((float) $item->precio_unitario, 2) }}</td>
                                <td class="text-end py-3 text-danger font-monospace">
                                    {{ (float) $item->descuento > 0 ? '-S/ ' . number_format((float) $item->descuento, 2) : 'S/ 0.00' }}
                                </td>
                                <td class="text-end py-3 text-muted font-monospace">S/ {{ number_format((float) $item->impuesto, 2) }}</td>
                                <td class="text-end pe-4 pe-md-5 py-3 fw-bold text-dark font-monospace">
                                    S/ {{ number_format((float) $item->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    No hay detalles para esta venta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light border-top font-monospace">
                        <tr>
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Subtotal bruto:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->subtotal, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Descuento total:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-danger">- S/ {{ number_format((float) $venta->descuento_total, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">IGV:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->impuesto_total, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Monto recibido:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->monto_recibido, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end py-2 text-muted fw-normal">Vuelto entregado:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark">S/ {{ number_format((float) $venta->vuelto_entregado, 2) }}</th>
                        </tr>
                        <tr class="border-top">
                            <th colspan="6" class="text-end py-3 fs-5 fw-bold text-dark">Total Venta:</th>
                            <th class="text-end pe-4 pe-md-5 py-3 fs-5 fw-bold text-primary">S/ {{ number_format((float) $venta->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Listado de Pagos (Si aplica) -->
        @if($venta->pagos->count())
            <div class="card-body border-top p-4 p-md-5">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-credit-card text-primary me-2"></i>Historial de Pagos</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary text-uppercase small fw-bold">
                            <tr>
                                <th class="border-bottom-0">Método</th>
                                <th class="text-end border-bottom-0">Monto</th>
                                <th class="border-bottom-0">Referencia</th>
                                <th class="text-end border-bottom-0">Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->pagos as $pago)
                                <tr>
                                    <td class="fw-medium">{{ $pago->metodo_pago->value ?? $pago->metodo_pago }}</td>
                                    <td class="text-end fw-bold font-monospace">S/ {{ number_format((float) $pago->monto, 2) }}</td>
                                    <td class="text-muted">{{ $pago->referencia_operacion ?? '—' }}</td>
                                    <td class="text-end text-muted font-monospace">{{ optional($pago->created_at)->format('d/m/Y H:i') }}</td>
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
        <div class="modal fade d-print-none" id="anularVentaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form action="{{ route('ventas.destroy', $venta) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Anular venta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning bg-warning bg-opacity-10 border-start border-4 border-warning border-0 rounded-3 text-dark">
                                <i class="fas fa-triangle-exclamation me-2 text-warning"></i>
                                Esta acción revertirá inventario, caja y/o tesorería según corresponda.
                            </div>
                            <div class="mb-3 mt-4">
                                <label for="motivo_anulacion" class="form-label fw-bold text-dark">Motivo de anulación</label>
                                <textarea class="form-control bg-light" name="motivo_anulacion" id="motivo_anulacion" rows="4" maxlength="1000" placeholder="Especifique el motivo..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light fw-medium border" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger fw-bold">Confirmar anulación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endcan
@endsection

@push('js')
<script>
    function imprimirTicketSilencioso(url) {
        let iframeViejo = document.getElementById('iframeTicketSilencioso');
        if (iframeViejo) {
            iframeViejo.remove();
        }

        let iframe = document.createElement('iframe');
        iframe.id = 'iframeTicketSilencioso';
        iframe.style.display = 'none';
        iframe.src = url;
        
        document.body.appendChild(iframe);

        iframe.onload = function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    }
</script>
@endpush