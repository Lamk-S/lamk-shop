@extends('layouts.app')

@section('title', 'Realizar Compra')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .help-text-soft { font-size: 0.8rem; color: #6c757d; }
    .purchase-alert { border-left: 4px solid #198754; }
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; white-space: nowrap; }
    .table-custom td { vertical-align: middle; }
</style>
@endpush

@section('content')
@php
    $defaultComprobanteId = old('comprobante_id', optional($comprobantes->first())->id);
    $variantData = $variantes->map(function ($v) {
        return [
            'id' => $v->id,
            'stock' => (int) $v->stock_actual,
            'precio_compra' => (float) ($v->producto->precio_compra ?? 0),
            'precio_venta' => (float) ($v->producto->precio_venta ?? 0),
            'producto' => $v->producto->nombre,
            'codigo' => $v->producto->codigo,
            'talla' => $v->talla?->nombre ?? 'Sin talla',
            'afecto_igv' => (bool) ($v->producto->afecto_igv ?? true),
        ];
    })->values();
@endphp

<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Registrar Compra</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('compras.index') }}" class="text-decoration-none">Compras</a></li>
            <li class="breadcrumb-item active">Nueva compra</li>
        </ol>
    </div>

    <div class="alert alert-success purchase-alert rounded-4 border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start gap-3">
            <div class="fs-4 text-success"><i class="fa-solid fa-circle-info"></i></div>
            <div>
                <div class="fw-semibold mb-1">Ingreso de mercadería</div>
                <div class="small mb-0">
                    Toda compra debe quedar vinculada a un proveedor identificado. La compra puede ser al contado, a crédito o mixta.
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('compras.store') }}" method="post" id="formCompra">
        @csrf
        <div class="row g-4">
            <div class="col-xl-8">
                @include('compra.partials.buscador_producto')

                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-body p-4">
                        @include('compra.partials.detalle')
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
                        <i class="fa-solid fa-file-invoice text-success fs-5 me-2"></i>
                        <h5 class="mb-0 fw-semibold text-dark">Datos de la Compra</h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="proveedor_id" class="form-label fw-medium text-secondary small">
                                    Proveedor <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                                    <small class="help-text-soft">Proveedor natural o jurídico identificado.</small>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickProveedorModal">
                                            <i class="fa-solid fa-user-plus me-1"></i>Nuevo
                                        </button>
                                    </div>
                                </div>

                                <select name="proveedor_id" id="proveedor_id" class="form-control selectpicker show-tick" data-live-search="true" title="Seleccione proveedor" data-size="6">
                                    @foreach ($proveedores as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('proveedor_id') === (string) $item->id)>
                                            {{ $item->persona?->razon_social ?? $item->persona?->nombre_completo }}
                                            — {{ $item->persona?->documento?->codigo ?? 'DOC' }} {{ $item->persona?->numero_documento ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('proveedor_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12">
                                <label for="comprobante_id" class="form-label fw-medium text-secondary small">
                                    Comprobante de Compra
                                </label>
                                <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker show-tick" data-live-search="true" title="Seleccione comprobante" data-size="5">
                                    <option value="">Sin comprobante</option>
                                    @foreach ($comprobantes as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('comprobante_id', $defaultComprobanteId) === (string) $item->id)>
                                            {{ $item->tipo_comprobante }} - {{ $item->serie }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('comprobante_id') <small class="text-danger">{{ $message }}</small> @enderror
                                <small class="help-text-soft d-block mt-2">La serie y correlativo se asignan automáticamente al guardar.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium text-secondary small">Fecha de Emisión</label>
                                <input readonly type="text" class="form-control bg-light" value="{{ date('d/m/Y') }}">
                                <input type="hidden" name="fecha_emision" value="{{ old('fecha_emision', now()->toDateTimeString()) }}">
                            </div>

                            <div class="col-12">
                                <label for="fecha_vencimiento" class="form-label fw-medium text-secondary small">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
                                @error('fecha_vencimiento') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12">
                                <label for="metodo_pago" class="form-label fw-medium text-secondary small">
                                    Método de pago <span class="text-danger">*</span>
                                </label>
                                <select name="metodo_pago" id="metodo_pago" class="form-control selectpicker show-tick" title="Seleccione método" data-size="5">
                                    <option value="EFECTIVO" @selected(old('metodo_pago') === 'EFECTIVO')>EFECTIVO</option>
                                    <option value="TARJETA" @selected(old('metodo_pago') === 'TARJETA')>TARJETA</option>
                                    <option value="TRANSFERENCIA" @selected(old('metodo_pago') === 'TRANSFERENCIA')>TRANSFERENCIA</option>
                                    <option value="CREDITO" @selected(old('metodo_pago') === 'CREDITO')>CRÉDITO</option>
                                    <option value="MIXTO" @selected(old('metodo_pago') === 'MIXTO')>MIXTO</option>
                                </select>
                                @error('metodo_pago') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12">
                                <label for="observacion" class="form-label fw-medium text-secondary small">Observación</label>
                                <textarea name="observacion" id="observacion" rows="3" class="form-control" placeholder="Opcional">{{ old('observacion') }}</textarea>
                                @error('observacion') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="actualizar_precio_venta" name="actualizar_precio_venta" value="1" @checked(old('actualizar_precio_venta'))>
                                    <label for="actualizar_precio_venta" class="form-check-label">
                                        Actualizar precio de venta desde esta compra
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="precio_venta" class="form-label fw-medium text-secondary small">Nuevo precio de venta</label>
                                <input type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" class="form-control" value="{{ old('precio_venta') }}">
                                @error('precio_venta') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top border-light p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success py-2 shadow-sm fw-bold" id="guardar">
                                <i class="fas fa-check-circle me-2"></i>Procesar Compra
                            </button>
                            <button id="cancelar" type="button" class="btn btn-light py-2 text-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                Cancelar Compra
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <div class="text-danger mb-3"><i class="fas fa-trash-alt fa-4x"></i></div>
                    <h4 class="fw-bold text-dark">¿Cancelar la compra?</h4>
                    <p class="text-muted">Se vaciará la lista de productos y perderá los datos ingresados.</p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Volver</button>
                    <button id="btnCancelarCompra" type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Sí, cancelar todo</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('proveedor.partials.quick-create-modal')
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const variantData = @json($variantData, JSON_UNESCAPED_UNICODE);
    const oldDetails = @json(old('detalles', []), JSON_UNESCAPED_UNICODE);
    let lineItems = [];

    $(document).ready(function () {
        $('.selectpicker').selectpicker();

        if (Array.isArray(oldDetails) && oldDetails.length > 0) {
            lineItems = oldDetails.map((detail) => {
                const variantId = Number(detail.producto_variante_id);
                const meta = variantData.find(v => Number(v.id) === variantId) || {};

                return {
                    producto_variante_id: variantId,
                    cantidad: Number(detail.cantidad ?? 1),
                    costo_unitario: Number(detail.costo_unitario ?? meta.precio_compra ?? 0),
                    precio_venta: Number(detail.precio_venta ?? meta.precio_venta ?? 0),
                    descuento: Number(detail.descuento ?? 0),
                    producto: meta.producto ?? 'Producto',
                    codigo: meta.codigo ?? '',
                    talla: meta.talla ?? 'Sin talla',
                    stock: meta.stock ?? 0,
                    afecto_igv: !!meta.afecto_igv
                };
            });

            renderRows();
        } else {
            updateTotals();
        }

        $('#variante_id').on('change', mostrarValores);
        $('#btn_agregar').on('click', agregarProducto);
        $('#btnCancelarCompra').on('click', cancelarCompra);
        $('#producto_id').on('change', mostrarValores);

        mostrarValores();
    });

    function mostrarValores() {
        const $option = $('#variante_id option:selected');

        if (!$option.val()) {
            $('#stock').val('');
            $('#precio_compra').val('');
            $('#precio_venta').val('');
            $('#variante_resumen').text('Seleccione un producto para ver stock y costo');
            return;
        }

        $('#stock').val($option.data('stock') ?? '');
        $('#precio_compra').val(Number($option.data('precio-compra') ?? 0).toFixed(2));
        $('#precio_venta').val(Number($option.data('precio-venta') ?? 0).toFixed(2));
        $('#variante_resumen').html(
            `<strong>${$option.data('producto') ?? 'Producto'}</strong> · ${$option.data('talla') ?? 'Sin talla'}`
        );
    }

    function agregarProducto() {
        const idVariante = Number($('#variante_id').val());

        if (!idVariante) {
            showToast('Seleccione un producto', 'error');
            return;
        }

        const $option = $('#variante_id option:selected');
        const stock = Number($option.data('stock')) || 0;
        const costoUnitario = Number($option.data('precio-compra')) || 0;
        const precioVenta = Number($option.data('precio-venta')) || 0;
        const producto = $option.data('producto') || 'Producto';
        const codigo = $option.data('codigo') || '';
        const talla = $option.data('talla') || 'Sin talla';
        const afectoIgv = Number($option.data('afecto-igv')) === 1 || $option.data('afecto-igv') === true || $option.data('afectoigv') === 1;
        const cantidad = Number($('#cantidad').val());
        const descuento = Number($('#descuento').val()) || 0;

        if (!Number.isInteger(cantidad) || cantidad <= 0) {
            showToast('Ingrese una cantidad válida', 'error');
            return;
        }

        if (costoUnitario <= 0) {
            showToast('Costo unitario inválido', 'error');
            return;
        }

        if (precioVenta <= 0) {
            showToast('Precio de venta de referencia inválido', 'error');
            return;
        }

        if (descuento < 0) {
            showToast('Descuento inválido', 'error');
            return;
        }

        const existingIndex = lineItems.findIndex(item => Number(item.producto_variante_id) === idVariante);

        if (existingIndex !== -1) {
            const current = lineItems[existingIndex];
            const nuevaCantidad = Number(current.cantidad) + cantidad;

            current.cantidad = nuevaCantidad;
            current.costo_unitario = round(
                ((Number(current.cantidad) - cantidad) * Number(current.costo_unitario) + (cantidad * costoUnitario)) / nuevaCantidad
            );
            current.precio_venta = precioVenta;
            current.descuento = round(Number(current.descuento) + descuento);
            current.producto = producto;
            current.codigo = codigo;
            current.talla = talla;
            current.stock = stock;
            current.afecto_igv = afectoIgv;

            showToast('La variante ya estaba en el detalle; se sumó la cantidad.', 'success');
        } else {
            lineItems.push({
                producto_variante_id: idVariante,
                cantidad,
                costo_unitario: costoUnitario,
                precio_venta: precioVenta,
                descuento,
                producto,
                codigo,
                talla,
                stock,
                afecto_igv: afectoIgv
            });

            showToast('Producto agregado correctamente', 'success');
        }

        renderRows();
        limpiarCampos();
    }

    function renderRows() {
        const $tbody = $('#tabla_detalle tbody');
        $tbody.empty();

        if (lineItems.length === 0) {
            updateTotals();
            toggleActions();
            return;
        }

        lineItems.forEach((item, index) => {
            const base = round((item.cantidad * item.costo_unitario) - item.descuento);
            const igv = item.afecto_igv ? round(base * 0.18) : 0;
            const totalLinea = round(base + igv);

            const row = `
                <tr>
                    <th class="align-middle text-center text-muted fw-normal">${index + 1}</th>
                    <td class="align-middle fw-medium">
                        <input type="hidden" name="detalles[${index}][producto_variante_id]" value="${item.producto_variante_id}">
                        <input type="hidden" name="detalles[${index}][cantidad]" value="${item.cantidad}">
                        <input type="hidden" name="detalles[${index}][costo_unitario]" value="${item.costo_unitario}">
                        <input type="hidden" name="detalles[${index}][precio_venta]" value="${item.precio_venta}">
                        <input type="hidden" name="detalles[${index}][descuento]" value="${item.descuento}">
                        <div>${item.producto}</div>
                        <div class="small text-muted">${item.codigo}</div>
                    </td>
                    <td class="align-middle text-center">${item.talla}</td>
                    <td class="align-middle text-center">${item.cantidad}</td>
                    <td class="align-middle text-end">${item.costo_unitario.toFixed(2)}</td>
                    <td class="align-middle text-end text-muted">${item.precio_venta.toFixed(2)}</td>
                    <td class="align-middle text-end text-danger">${item.descuento > 0 ? '-S/ ' + item.descuento.toFixed(2) : 'S/ 0.00'}</td>
                    <td class="align-middle text-end">${igv.toFixed(2)}</td>
                    <td class="align-middle text-end fw-bold text-dark">${totalLinea.toFixed(2)}</td>
                    <td class="align-middle text-center">
                        <button class="btn btn-sm btn-outline-danger border-0" type="button" onclick="eliminarProducto(${index})" title="Quitar">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;

            $tbody.append(row);
        });

        updateTotals();
        toggleActions();
    }

    function eliminarProducto(indice) {
        lineItems.splice(indice, 1);
        renderRows();
        showToast('Producto eliminado', 'success');
    }

    function cancelarCompra() {
        lineItems = [];
        renderRows();
        limpiarCampos();
        showToast('Compra cancelada', 'success');
    }

    function updateTotals() {
        const subtotalBruto = lineItems.reduce((acc, item) => acc + (Number(item.cantidad) * Number(item.costo_unitario)), 0);
        const descuentoTotal = lineItems.reduce((acc, item) => acc + Number(item.descuento), 0);
        const baseImponible = Math.max(0, subtotalBruto - descuentoTotal);
        const igv = lineItems.reduce((acc, item) => {
            const base = Math.max(0, (Number(item.cantidad) * Number(item.costo_unitario)) - Number(item.descuento));
            return acc + (item.afecto_igv ? (base * 0.18) : 0);
        }, 0);
        const total = round(baseImponible + igv);

        $('#subtotal_bruto').text(subtotalBruto.toFixed(2));
        $('#descuento_total').text(descuentoTotal.toFixed(2));
        $('#igv').text(igv.toFixed(2));
        $('#total').text(total.toFixed(2));
        $('#inputSubtotal').val(subtotalBruto.toFixed(2));
        $('#inputDescuentoTotal').val(descuentoTotal.toFixed(2));
        $('#inputIgvTotal').val(igv.toFixed(2));
        $('#inputTotal').val(total.toFixed(2));
        $('#impuesto').val(igv.toFixed(2));
    }

    function toggleActions() {
        if (lineItems.length === 0) {
            $('#guardar').prop('disabled', true).addClass('disabled');
            $('#cancelar').prop('disabled', true).addClass('disabled');
        } else {
            $('#guardar').prop('disabled', false).removeClass('disabled');
            $('#cancelar').prop('disabled', false).removeClass('disabled');
        }
    }

    function limpiarCampos() {
        $('#variante_id').selectpicker('val', '');
        $('#cantidad').val('1');
        $('#precio_compra').val('');
        $('#precio_venta').val('');
        $('#descuento').val('0');
        $('#stock').val('');
        $('#variante_resumen').text('Seleccione un producto para ver stock y costo');
    }

    function round(num, decimales = 2) {
        return Number(parseFloat(num).toFixed(decimales));
    }

    function showToast(message, icon = 'error') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            icon: icon,
            title: message
        });
    }
</script>
@endpush