@extends('layouts.app')

@section('title', 'Realizar Venta')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .pos-totals th {
        font-size: 1.1rem;
        color: #495457;
    }

    .pos-totals .total-row th {
        font-size: 1.3rem;
        color: #0d6efd;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Realizar Venta</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" class="text-decoration-none">Ventas</a></li>
            <li class="breadcrumb-item active">Nueva venta</li>
        </ol>
    </div>

    <form action="{{ route('ventas.store') }}" method="post">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Detalle de Productos</h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                            <div class="col-md-12">
                                <label for="producto_id" class="form-label fw-medium text-secondary small">Buscar Producto</label>
                                <select name="producto_id" id="producto_id" class="form-control selectpicker shadow-sm border-0" data-live-search="true" data-size="5" title="Escriba o seleccione un producto...">
                                    @foreach ($productos as $item)
                                    <option value="{{ $item->id }}" data-stock="{{ $item->stock }}" data-precio="{{ $item->precio_venta }}">
                                        {{ $item->codigo }} - {{ $item->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="stock" class="form-label fw-medium text-secondary small">Stock Disp.</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fas fa-box"></i></span>
                                    <input disabled type="text" name="stock" id="stock" class="form-control bg-white text-center fw-bold text-success">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="precio_venta" class="form-label fw-medium text-secondary small">Precio Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">S/</span>
                                    <input disabled type="number" name="precio_venta" id="precio_venta" class="form-control bg-white text-end fw-bold" step="0.1">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="cantidad" class="form-label fw-medium text-secondary small">Cantidad</label>
                                <input type="number" name="cantidad" id="cantidad" class="form-control text-center" min="1">
                            </div>

                            <div class="col-md-3">
                                <label for="descuento" class="form-label fw-medium text-secondary small">Descuento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">S/</span>
                                    <input type="number" name="descuento" id="descuento" class="form-control text-end" value="0" min="0" step="0.01">
                                </div>
                            </div>

                            <div class="col-12 mt-3 text-end">
                                <button id="btn_agregar" class="btn btn-primary px-4 shadow-sm" type="button">
                                    <i class="fas fa-plus me-2"></i>Agregar al carrito
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-3">
                            <table id="tabla_detalle" class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-secondary fw-semibold text-center" style="width: 50px;">#</th>
                                        <th class="text-secondary fw-semibold">Producto</th>
                                        <th class="text-secondary fw-semibold text-center">Cant.</th>
                                        <th class="text-secondary fw-semibold text-end">Precio</th>
                                        <th class="text-secondary fw-semibold text-end">Desc.</th>
                                        <th class="text-secondary fw-semibold text-end">Subtotal</th>
                                        <th class="text-center" style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="bg-light pos-totals border-top">
                                    <tr>
                                        <th colspan="5" class="text-end py-3">Subtotal sin IGV:</th>
                                        <th class="text-end py-3">
                                            <input type="hidden" name="subtotal" value="0" id="inputSubtotal">
                                            <span id="sumas">0.00</span>
                                        </th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end py-3">IGV (18%):</th>
                                        <th class="text-end py-3"><span id="igv">0.00</span></th>
                                        <th></th>
                                    </tr>
                                    <tr class="total-row">
                                        <th colspan="5" class="text-end py-3">Total a Pagar:</th>
                                        <th class="text-end py-3">
                                            <input type="hidden" name="total" value="0" id="inputTotal">
                                            <span id="total" class="fw-bold">0.00</span>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-file-invoice text-info me-2"></i>Datos Generales</h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="cliente_id" class="form-label fw-medium text-secondary small">Cliente <span class="text-danger">*</span></label>
                                <select name="cliente_id" id="cliente_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" title="Seleccione un cliente..." data-size="4">
                                    @foreach ($clientes as $item)
                                    <option value="{{ $item->id }}" @selected(old('cliente_id')==$item->id)>{{ $item->persona->razon_social }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="comprobante_id" class="form-label fw-medium text-secondary small">Tipo de Comprobante <span class="text-danger">*</span></label>
                                <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker show-tick border shadow-sm" data-live-search="true" title="Seleccione...">
                                    @foreach ($comprobantes as $item)
                                    <option value="{{ $item->id }}" @selected(old('comprobante_id')==$item->id)>{{ $item->tipo_comprobante }}</option>
                                    @endforeach
                                </select>
                                @error('comprobante_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-medium text-secondary small">N° Comprobante</label>
                                <input type="text" id="numero_comprobante_preview" class="form-control bg-light text-muted"
                                    value="Se generará automáticamente al guardar" readonly>
                                <small class="text-muted">El número final se asignará según la serie y el correlativo automático.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha" class="form-label fw-medium text-secondary small">Fecha Emisión</label>
                                <input readonly type="text" name="fecha" id="fecha" class="form-control bg-light text-muted" value="{{ date('d-m-Y') }}">
                                <input type="hidden" name="fecha_hora" value="{{ old('fecha_hora', \Carbon\Carbon::now()->toDateTimeString()) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="impuesto" class="form-label fw-medium text-secondary small">Impuesto Generado</label>
                                <input readonly type="text" name="impuesto" id="impuesto" class="form-control bg-light text-muted text-end">
                                @error('impuesto') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="metodo_pago" class="form-label fw-medium text-secondary small">Método de pago <span class="text-danger">*</span></label>
                                <select name="metodo_pago" id="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror">
                                    <option value="EFECTIVO" @selected(old('metodo_pago', 'EFECTIVO' )=='EFECTIVO' )>EFECTIVO</option>
                                    <option value="TARJETA" @selected(old('metodo_pago')=='TARJETA' )>TARJETA</option>
                                    <option value="TRANSFERENCIA" @selected(old('metodo_pago')=='TRANSFERENCIA' )>TRANSFERENCIA</option>
                                </select>
                                @error('metodo_pago') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="monto_recibido" class="form-label fw-medium text-secondary small">Monto recibido <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">$</span>
                                    <input type="number" name="monto_recibido" id="monto_recibido" class="form-control border-start-0 text-end" value="{{ old('monto_recibido', 0) }}" min="0" step="0.01">
                                </div>
                                @error('monto_recibido') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="vuelto_entregado" class="form-label fw-medium text-secondary small">Vuelto entregado</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">$</span>
                                    <input disabled type="number" name="vuelto_entregado" id="vuelto_entregado" class="form-control border-start-0 bg-light text-end fw-bold" value="{{ old('vuelto_entregado', 0) }}" step="0.01">
                                </div>
                                @error('vuelto_entregado') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top border-light p-4 text-center">
                        <button id="cancelar" type="button" class="btn btn-light w-100 mb-2 py-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times me-2"></i>Cancelar Venta
                        </button>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-6 shadow-sm" id="guardar">
                            <i class="fas fa-check-circle me-2"></i>Procesar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        $('#producto_id').change(function() {
            mostrarValores();
        });
        $('#btn_agregar').click(function() {
            agregarProducto();
        });
        $('#btnCancelarVenta').click(function() {
            cancelarVenta();
        });
        $('#monto_recibido').on('input', actualizarVuelto);
        $('#metodo_pago').on('change', function() {
            actualizarVuelto();
        });

        disableButtons();
    });

    let cont = 0;
    let subTotal = [];
    let sumas = 0;
    let igv = 0;
    let total = 0;
    const impuesto = 18;

    function mostrarValores() {
        let $option = $('#producto_id option:selected');

        if (!$option.val()) {
            $('#stock').val('');
            $('#precio_venta').val('');
            return;
        }

        $('#stock').val($option.data('stock') ?? '');
        $('#precio_venta').val($option.data('precio') ?? '');
    }

    function agregarProducto() {
        let idProducto = $('#producto_id').val();

        if (!idProducto) {
            showModal('Seleccione un producto');
            return;
        }

        let $option = $('#producto_id option:selected');
        let stock = parseFloat($option.data('stock'));
        let precioVenta = parseFloat($option.data('precio'));
        let nameProducto = $option.text().split(' - ').slice(1).join(' - ');

        let cantidad = parseInt($('#cantidad').val());
        let descuento = parseFloat($('#descuento').val()) || 0;

        if (!cantidad || cantidad <= 0) {
            showModal('Ingrese una cantidad válida');
            return;
        }
        if (!Number.isInteger(cantidad)) {
            showModal('La cantidad debe ser entera');
            return;
        }
        if (descuento < 0) {
            showModal('Descuento inválido');
            return;
        }

        let cantidadAgregada = 0;
        $('input[name="arrayidproducto[]"]').each(function(index) {
            if ($(this).val() == idProducto) {
                cantidadAgregada += parseInt($('input[name="arraycantidad[]"]').eq(index).val());
            }
        });

        let stockDisponible = stock - cantidadAgregada;
        if (cantidad > stockDisponible) {
            showModal('Stock insuficiente');
            return;
        }

        subTotal[cont] = round((cantidad * precioVenta) - descuento);
        sumas = round(sumas + subTotal[cont]);
        igv = round((sumas * impuesto) / 100);
        total = round(sumas + igv);

        let fila = `
            <tr id="fila${cont}">
                <th class="align-middle text-center text-muted fw-normal">${cont + 1}</th>
                <td class="align-middle fw-medium">
                    <input type="hidden" name="arrayidproducto[]" value="${idProducto}">
                    ${nameProducto}
                </td>
                <td class="align-middle text-center">
                    <input type="hidden" name="arraycantidad[]" value="${cantidad}">
                    ${cantidad}
                </td>
                <td class="align-middle text-end">
                    <input type="hidden" name="arrayprecioventa[]" value="${precioVenta}">
                    ${precioVenta.toFixed(2)}
                </td>
                <td class="align-middle text-end text-danger">
                    <input type="hidden" name="arraydescuento[]" value="${descuento}">
                    ${descuento > 0 ? '-' + descuento.toFixed(2) : '0.00'}
                </td>
                <td class="align-middle text-end fw-bold text-dark">${subTotal[cont].toFixed(2)}</td>
                <td class="align-middle text-center">
                    <button class="btn btn-sm btn-outline-danger border-0" type="button" onclick="eliminarProducto(${cont})" title="Quitar">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#tabla_detalle tbody').append(fila);
        reordenarFilas();
        actualizarTotales();

        limpiarCampos();
        cont++;
        disableButtons();
    }

    function cancelarVenta() {
        $('#tabla_detalle tbody').empty();
        cont = 0;
        subTotal = [];
        sumas = 0;
        igv = 0;
        total = 0;
        actualizarTotales();
        limpiarCampos();
        $('#monto_recibido').val('0.00');
        $('#vuelto_entregado').val('0.00');
        disableButtons();
        showModal('Venta cancelada', 'success');
    }

    function eliminarProducto(indice) {
        sumas = round(sumas - subTotal[indice]);
        igv = round((sumas * impuesto) / 100);
        total = round(sumas + igv);
        $('#fila' + indice).remove();
        actualizarTotales();
        disableButtons();
        limpiarCampos();
        reordenarFilas();
    }

    function actualizarTotales() {
        $('#sumas').html(sumas.toFixed(2));
        $('#igv').html(igv.toFixed(2));
        $('#total').html(total.toFixed(2));
        $('#impuesto').val(igv.toFixed(2));
        $('#inputTotal').val(total.toFixed(2));
        $('#inputSubtotal').val(sumas.toFixed(2));

        const montoActual = parseFloat($('#monto_recibido').val()) || 0;

        if (!montoActual || montoActual === 0) {
            $('#monto_recibido').val(total.toFixed(2));
        }

        actualizarVuelto();
    }

    function actualizarVuelto() {
        const montoRecibido = parseFloat($('#monto_recibido').val()) || 0;
        const vuelto = round(Math.max(0, montoRecibido - total));
        $('#vuelto_entregado').val(vuelto.toFixed(2));
    }

    function disableButtons() {
        if (!total || total <= 0) {
            $('#guardar').hide();
            $('#cancelar').hide();
        } else {
            $('#guardar').show();
            $('#cancelar').show();
        }
    }

    function limpiarCampos() {
        $('#producto_id').selectpicker('val', '');
        $('#cantidad').val('');
        $('#precio_venta').val('');
        $('#descuento').val('0');
        $('#stock').val('');
    }

    function reordenarFilas() {
        $('#tabla_detalle tbody tr').each(function(index) {
            $(this).children('th').text(index + 1);
        });
    }

    function round(num, decimales = 2) {
        return Number(parseFloat(num).toFixed(decimales));
    }

    function showModal(message, icon = 'error') {
        Swal.mixin({
            toast: true,
            position: "top-end",
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
</script>
@endpush