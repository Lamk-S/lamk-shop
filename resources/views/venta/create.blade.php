@extends('template')

@section('title', 'Realizar venta')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Realizar Venta</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
        <li class="breadcrumb-item active">Crear venta</li>
    </ol>
</div>

<form action="{{ route('ventas.store') }}" method="post">
    @csrf
    <div class="container-fluid mt-4">
        <div class="row gy-4">
            <!-- Venta producto -->
            <div class="col-md-8">
                <div class="text-white bg-primary p-1 text-center">
                    Detalles de la venta
                </div>
                <div class="p-3 border border-3 border-primary">
                    <div class="row">
                        <!-- Producto -->
                        <div class="col-md-12 mb-2">
                            <select name="producto_id" id="producto_id" class="form-control selectpicker" data-live-search="true" data-size="4" title="Busque un producto aquí">
                                @foreach ($productos as $item)
                                    <option value="{{ $item->id }}-{{ $item->stock }}-{{ $item->precio_venta }}">{{ $item->codigo.' '.$item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stock -->
                        <div class="d-flex justify-content-end mb-4">
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <label for="stock" class="form-label col-sm-4">En stock:</label>
                                    <div class="col-sm-8">
                                        <input disabled type="text" name="stock" id="stock" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-4 mb-2">
                            <label for="cantidad" class="form-label">Cantidad:</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control">
                        </div>

                        <!-- Precio de venta -->
                        <div class="col-md-4 mb-2">
                            <label for="precio_venta" class="form-label">Precio de venta:</label>
                            <input disabled type="number" name="precio_venta" id="precio_venta" class="form-control" step="0.1">
                        </div>

                        <!-- Descuento -->
                        <div class="col-md-4 mb-2">
                            <label for="descuento" class="form-label">Descuento:</label>
                            <input type="number" name="descuento" id="descuento" class="form-control">
                        </div>

                        <!-- Botón para agregar -->
                        <div class="col-md-12 mb-2 text-end">
                            <button id="btn_agregar" class="btn btn-primary" type="button">Agregar</button>
                        </div>

                        <!-- Tabla para el detalle de la venta -->
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tabla_detalle" class="table table-hover">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-white">#</th>
                                            <th class="text-white">Producto</th>
                                            <th class="text-white">Cantidad</th>
                                            <th class="text-white">Precio venta</th>
                                            <th class="text-white">Descuento</th>
                                            <th class="text-white">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Suma</th>
                                            <th colspan="2"><span id="sumas">0</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">IGV %</th>
                                            <th colspan="2"><span id="igv">0</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Total</th>
                                            <th colspan="2"><input type="hidden" name="total" value="0" id="inputTotal"><span id="total">0</span></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <button id="cancelar" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancelar venta</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venta -->
            <div class="col-md-4">
                <div class="text-white bg-success p-1 text-center">
                    Datos generales
                </div>
                <div class="p-3 border border-3 border-success">
                    <div class="row">
                        <!-- Cliente -->
                        <div class="col-md-12 mb-2">
                            <label for="cliente_id" class="form-label">Cliente:</label>
                            <select name="cliente_id" id="cliente_id" class="form-control selectpicker show-tick" data-live-search="true" title="Selecciona" data-size="4">
                                @foreach ( $clientes as $item )
                                    <option value="{{ $item->id }}">{{ $item->persona->razon_social }}</option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <small class="text-danger">{{ '*'.$messsage }}</small>
                            @enderror
                        </div>

                        <!-- Comprobante -->
                        <div class="col-md-12 mb-2">
                            <label for="comprobante_id" class="form-label">Comprobante:</label>
                            <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker show-tick" data-live-search="true" title="Selecciona">
                                @foreach ( $comprobantes as $item )
                                    <option value="{{ $item->id }}">{{ $item->tipo_comprobante }}</option>
                                @endforeach
                            </select>
                            @error('comprobante_id')
                                <small class="text-danger">{{ '*'.$messsage }}</small>
                            @enderror
                        </div>

                        <!-- Número de comprobante -->
                        <div class="col-md-12 mb-2">
                            <label for="numero_comprobante" class="form-label">Número de comprobante:</label>
                            <input required type="text" name="numero_comprobante" id="numero_comprobante" class="form-control">
                            @error('numero_comprobante')
                                <small class="text-danger">{{ '*'.$messsage }}</small>
                            @enderror
                        </div>

                        <!-- Impuesto -->
                        <div class="col-md-6 mb-2">
                            <label for="impuesto" class="form-label">Impuesto(IGV):</label>
                            <input readonly type="text" name="impuesto" id="impuesto" class="form-control border-success">
                            @error('impuesto')
                                <small class="text-danger">{{ '*'.$messsage }}</small>
                            @enderror
                        </div>

                        <!-- Fecha Hora-->
                        <div class="col-md-6 mb-2">
                            <label for="fecha" class="form-label">Fecha:</label>
                            <input readonly type="text" name="fecha" id="fecha" class="form-control border-success" value="<?php echo date("Y-m-d") ?>">
                            <?php 
                            use Carbon\Carbon;
                            $fecha_hora = Carbon::now()->toDateTimeString();
                            ?>
                            <input type="hidden" name="fecha_hora" value="{{ $fecha_hora }}">
                        </div>

                        <!-- User -->
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                        <!-- Botones -->
                        <div class="col-md-12 mb-2 text-center">
                            <button type="submit" class="btn btn-success" id="guardar">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar la venta -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="cancelModalLabel">Mensaje de confirmación</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres cancelar la venta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btnCancelarVenta" type="button" class="btn btn-danger" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        $('#producto_id').change(function () {
            mostrarValores();
        });

        $('#btn_agregar').click(function () {
            agregarProducto();
        });

        $('#btnCancelarVenta').click(function () {
            cancelarVenta();
        });

        disableButtons();
    });

    // Variables
    let cont = 0;
    let subTotal = [];
    let sumas = 0;
    let igv = 0;
    let total = 0;

    // Constantes
    const impuesto = 18;

    function mostrarValores() {
        let value = $('#producto_id').val();

        if (!value) {
            $('#stock').val('');
            $('#precio_venta').val('');
            return;
        }

        let dataProducto = value.split('-');

        $('#stock').val(dataProducto[1]);
        $('#precio_venta').val(dataProducto[2]);
    }

    function agregarProducto() {
        let value = $('#producto_id').val();

        if (!value) {
            showModal('Seleccione un producto');
            return;
        }

        let dataProducto = value.split('-');

        // Obtener valores
        let idProducto = dataProducto[0];
        let stock = parseFloat(dataProducto[1]);
        let precioVenta = parseFloat(dataProducto[2]);

        let nameProducto = $('#producto_id option:selected').text();

        let cantidad = parseInt($('#cantidad').val());
        let descuento = parseFloat($('#descuento').val()) || 0;

        // Validaciones
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

        // Validar cantidad acumulada del mismo producto
        let cantidadAgregada = 0;

        $('input[name="arrayidproducto[]"]').each(function(index) {
            if ($(this).val() == idProducto) {
                cantidadAgregada += parseInt(
                    $('input[name="arraycantidad[]"]').eq(index).val()
                );
            }
        });

        let stockDisponible = stock - cantidadAgregada;

        if (cantidad > stockDisponible) {
            showModal('Stock insuficiente');
            return;
        }

        // Calcular subtotal
        subTotal[cont] = round((cantidad * precioVenta) - descuento);

        sumas = round(sumas + subTotal[cont]);
        igv = round((sumas * impuesto) / 100);
        total = round(sumas + igv);

        let fila = `
            <tr id="fila${cont}">
                <th class="fila-numero"></th>
                <td>
                    <input type="hidden" name="arrayidproducto[]" value="${idProducto}">
                    ${nameProducto}
                </td>
                <td>
                    <input type="hidden" name="arraycantidad[]" value="${cantidad}">
                    ${cantidad}
                </td>
                <td>
                    <input type="hidden" name="arrayprecioventa[]" value="${precioVenta}">
                    ${precioVenta}
                </td>
                <td>
                    <input type="hidden" name="arraydescuento[]" value="${descuento}">
                    ${descuento}
                </td>
                <td>${subTotal[cont]}</td>
                <td>
                    <button 
                        class="btn btn-danger"
                        type="button"
                        onclick="eliminarProducto(${cont})">

                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        // Agregar al tbody
        $('#tabla_detalle tbody').append(fila);
        reordenarFilas();

        // Actualizar totales
        actualizarTotales();
        limpiarCampos();
        cont++;
        disableButtons();
    }

    function cancelarVenta() {
        // Limpiar tbody
        $('#tabla_detalle tbody').empty();

        // Reiniciar variables
        cont = 0;
        subTotal = [];
        sumas = 0;
        igv = 0;
        total = 0;

        actualizarTotales();
        limpiarCampos();
        disableButtons();
        showModal('Venta cancelada', 'success');
    }

    function eliminarProducto(indice) {
        sumas = round(sumas - subTotal[indice]);
        igv = round((sumas * impuesto) / 100);
        total = round(sumas + igv);
        // Eliminar fila
        $('#fila' + indice).remove();

        actualizarTotales();
        disableButtons();
        limpiarCampos();
        // Reordenar numeración visual
        reordenarFilas();
    }

    function actualizarTotales() {
        $('#sumas').html(sumas);
        $('#igv').html(igv);
        $('#total').html(total);
        $('#impuesto').val(igv);
        $('#inputTotal').val(total);
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
        $('#descuento').val('');
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
            timer: 1500,
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