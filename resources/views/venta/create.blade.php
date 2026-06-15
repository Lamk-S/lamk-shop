@extends('layouts.app')

@section('title', 'Realizar Venta')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .pos-totals th { font-size: 1rem; color: #495457; }
    .pos-totals .total-row th { font-size: 1.15rem; color: #0d6efd; }
    .help-text-soft { font-size: 0.8rem; color: #6c757d; }
    .customer-alert { border-left: 4px solid #0d6efd; }
    .summary-card { background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%); }
</style>
@endpush

@section('content')
@php
    $defaultComprobanteId = old('comprobante_id', optional($comprobantes->firstWhere('tipo_comprobante', 'TICKET'))->id);
    $variantData = $variantes->map(function ($v) {
        return [
            'id' => $v->id,
            'stock' => (int) $v->stock_actual,
            'precio' => (float) ($v->producto->precio_venta ?? 0),
            'producto' => $v->producto->nombre,
            'codigo' => $v->producto->codigo,
            'talla' => $v->talla?->nombre ?? 'Sin talla',
            'afecto_igv' => (bool) ($v->producto->afecto_igv ?? true),
        ];
    })->values();
@endphp

<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Realizar Venta</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" class="text-decoration-none">Ventas</a></li>
            <li class="breadcrumb-item active">Nueva venta</li>
        </ol>
    </div>

    <div class="alert alert-info customer-alert rounded-4 border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start gap-3">
            <div class="fs-4 text-primary"><i class="fa-solid fa-circle-info"></i></div>
            <div>
                <div class="fw-semibold mb-1">Modo operativo retail</div>
                <div class="small mb-0">
                    Deja el cliente en <strong>Consumidor final / Cliente varios</strong> para boleta rápida.
                    Para factura, selecciona un cliente con <strong>RUC</strong>.
                    Si necesitas registrar un cliente nuevo, puedes hacerlo desde el modal rápido.
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('ventas.store') }}" method="post" id="formVenta">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                @include('venta.partials.buscador_producto')

                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-body p-4">
                        @include('venta.partials.detalle')
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('venta.partials.pagos')
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
                    <div class="text-danger mb-3"><i class="fas fa-exclamation-triangle fa-4x opacity-75"></i></div>
                    <h4 class="fw-bold text-dark">¿Cancelar venta?</h4>
                    <p class="text-muted">Se eliminarán todos los productos agregados al detalle.</p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Mantener Venta</button>
                    <button id="btnCancelarVenta" type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Sí, Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('cliente.partials.quick-create-modal')
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
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
                    precio_unitario: Number(detail.precio_unitario ?? meta.precio ?? 0),
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
        $('#btnCancelarVenta').on('click', cancelarVenta);
        $('#monto_recibido').on('input', actualizarVuelto);
        $('#metodo_pago').on('change', actualizarVuelto);
        $('#comprobante_id').on('change', validarClienteSegunComprobante);
        $('#cliente_id').on('change', validarClienteSegunComprobante);

        mostrarValores();
        validarClienteSegunComprobante();
    });

    function mostrarValores() {
        const $option = $('#variante_id option:selected');

        if (!$option.val()) {
            $('#stock').val('');
            $('#precio_venta').val('');
            $('#cantidad').attr('max', null);
            $('#variante_resumen').text('Seleccione un producto para ver stock y precio');
            return;
        }

        const stock = Number($option.data('stock')) || 0;
        $('#stock').val(stock);
        $('#cantidad').attr('max', stock);
        $('#precio_venta').val(Number($option.data('precio') ?? 0).toFixed(2));
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
        const precioUnitario = Number($option.data('precio')) || 0;
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

        if (cantidad > stock) {
            showToast(`Stock insuficiente. Solo hay ${stock} unidades disponibles.`, 'error');
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

            if (nuevaCantidad > stock) {
                showToast(`Stock insuficiente. Ya agregaste ${current.cantidad} y solo hay ${stock} unidades disponibles.`, 'error');
                return;
            }

            current.cantidad = nuevaCantidad;
            current.descuento = round(Number(current.descuento) + descuento);
            current.precio_unitario = precioUnitario;
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
                precio_unitario: precioUnitario,
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
            const base = round((item.cantidad * item.precio_unitario) - item.descuento);
            const igv = item.afecto_igv ? round(base * 0.18) : 0;
            const totalLinea = round(base + igv);

            const row = `
                <tr>
                    <th class="align-middle text-center text-muted fw-normal">${index + 1}</th>
                    <td class="align-middle fw-medium">
                        <input type="hidden" name="detalles[${index}][producto_variante_id]" value="${item.producto_variante_id}">
                        <input type="hidden" name="detalles[${index}][cantidad]" value="${item.cantidad}">
                        <input type="hidden" name="detalles[${index}][precio_unitario]" value="${item.precio_unitario}">
                        <input type="hidden" name="detalles[${index}][descuento]" value="${item.descuento}">
                        <div>${item.producto}</div>
                        <div class="small text-muted">${item.codigo}</div>
                    </td>
                    <td class="align-middle text-center">${item.talla}</td>
                    <td class="align-middle text-center">${item.cantidad}</td>
                    <td class="align-middle text-end">${item.precio_unitario.toFixed(2)}</td>
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
        actualizarClienteComprobante();
    }

    function eliminarProducto(indice) {
        lineItems.splice(indice, 1);
        renderRows();
        showToast('Producto eliminado', 'success');
    }

    function cancelarVenta() {
        lineItems = [];
        renderRows();
        limpiarCampos();
        $('#monto_recibido').val('0.00');
        $('#vuelto_entregado').val('0.00');
        showToast('Venta cancelada', 'success');
    }

    function updateTotals() {
        const subtotalBruto = lineItems.reduce((acc, item) => acc + (Number(item.cantidad) * Number(item.precio_unitario)), 0);
        const descuentoTotal = lineItems.reduce((acc, item) => acc + Number(item.descuento), 0);
        const baseImponible = Math.max(0, subtotalBruto - descuentoTotal);
        const igv = lineItems.reduce((acc, item) => {
            const base = Math.max(0, (Number(item.cantidad) * Number(item.precio_unitario)) - Number(item.descuento));
            return acc + (item.afecto_igv ? (base * 0.18) : 0);
        }, 0);
        const total = round(baseImponible + igv);

        $('#subtotal_bruto').text(subtotalBruto.toFixed(2));
        $('#descuento_total').text(descuentoTotal.toFixed(2));
        $('#base_imponible').text(baseImponible.toFixed(2));
        $('#igv').text(igv.toFixed(2));
        $('#total').text(total.toFixed(2));
        $('#inputSubtotal').val(subtotalBruto.toFixed(2));
        $('#inputDescuentoTotal').val(descuentoTotal.toFixed(2));
        $('#inputIgvTotal').val(igv.toFixed(2));
        $('#inputTotal').val(total.toFixed(2));

        const actual = Number($('#monto_recibido').val()) || 0;
        if (!actual || actual === 0) {
            $('#monto_recibido').val(total.toFixed(2));
        }

        actualizarVuelto();
    }

    function actualizarVuelto() {
        const montoRecibido = Number($('#monto_recibido').val()) || 0;
        const total = Number($('#inputTotal').val()) || 0;
        const vuelto = round(Math.max(0, montoRecibido - total));
        $('#vuelto_entregado').val(vuelto.toFixed(2));
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
        $('#precio_venta').val('');
        $('#descuento').val('0');
        $('#stock').val('');
        $('#cantidad').attr('max', null);
        $('#variante_resumen').text('Seleccione un producto para ver stock y precio');
    }

    function round(num, decimales = 2) {
        return Number(parseFloat(num).toFixed(decimales));
    }

    function showToast(message, icon = 'error') {
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        }).fire({
            icon: icon,
            title: message
        });
    }

    function validarClienteSegunComprobante() {
        const selected = $('#comprobante_id option:selected');
        const tipo = (selected.data('tipo') || '').toString().toUpperCase();
        const clienteId = $('#cliente_id').val();

        if (tipo === 'FACTURA' && !clienteId) {
            $('#cliente_help').removeClass('text-muted').addClass('text-danger').text('Factura: selecciona un cliente identificado con RUC.');
        } else if (tipo === 'FACTURA' && clienteId) {
            $('#cliente_help').removeClass('text-danger').addClass('text-muted').text('Factura: cliente identificado seleccionado correctamente.');
        } else {
            $('#cliente_help').removeClass('text-danger').addClass('text-muted').text('Boleta rápida: puedes dejar el cliente en Consumidor final / Cliente varios.');
        }
    }

    function actualizarClienteComprobante() {
        validarClienteSegunComprobante();
    }
</script>
@endpush