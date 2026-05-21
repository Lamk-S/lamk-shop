@extends('template')

@section('title', 'Realizar compra')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Realizar Compra</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
        <li class="breadcrumb-item active">Crear compra</li>
    </ol>
</div>
<form action="{{ route('compras.store') }}" method="post">
    @csrf
    <div class="container-fluid mt-4">
        <div class="row gy-4">
            <!-- Compra producto -->
            <div class="col-md-8">
                <div class="text-white bg-primary p-1 text-center">
                    Detalles de la compra
                </div>
                <div class="p-3 border border-3 border-primary">
                    <div class="row">
                        <!-- Producto -->
                        <div class="col-md-12 mb-2">
                            <select name="producto_id" id="producto_id" class="form-control selectpicker" data-live-search="true" data-size="4" title="Busque un producto aquí">
                                @foreach ($productos as $item)
                                    <option value="{{ $item->id }}">{{ $item->codigo.' '.$item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-4 mb-2">
                            <label for="cantidad" class="form-label">Cantidad:</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control">
                        </div>

                        <!-- Precio de compra -->
                        <div class="col-md-4 mb-2">
                            <label for="precio_compra" class="form-label">Precio de compra:</label>
                            <input type="number" name="precio_compra" id="precio_compra" class="form-control" step="0.1">
                        </div>

                        <!-- Precio de venta -->
                        <div class="col-md-4 mb-2">
                            <label for="precio_venta" class="form-label">Precio de venta:</label>
                            <input type="number" name="precio_venta" id="precio_venta" class="form-control" step="0.1">
                        </div>

                        <!-- Botón para agregar -->
                        <div class="col-md-12 mb-2 text-end">
                            <button id="btn_agregar" class="btn btn-primary" type="button">Agregar</button>
                        </div>

                        <!-- Tabla para el detalle de la compra -->
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tabla_detalle" class="table table-hover">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-white">#</th>
                                            <th class="text-white">Producto</th>
                                            <th class="text-white">Cantidad</th>
                                            <th class="text-white">Precio compra</th>
                                            <th class="text-white">Precio venta</th>
                                            <th class="text-white">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Sumas</th>
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
                            <button id="cancelar" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancelar compra</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Producto -->
            <div class="col-md-4">
                <div class="text-white bg-success p-1 text-center">
                    Datos generales
                </div>
                <div class="p-3 border border-3 border-success">
                    <div class="row">
                        <!-- Proveedor -->
                        <div class="col-md-12 mb-2">
                            <label for="proveedore_id" class="form-label">Proveedor:</label>
                            <select name="proveedore_id" id="proveedore_id" class="form-control selectpicker show-tick" data-live-search="true" title="Selecciona" data-size="4">
                                @foreach ( $proveedores as $item )
                                    <option value="{{ $item->id }}">{{ $item->persona->razon_social }}</option>
                                @endforeach
                            </select>
                            @error('proveedore_id')
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

                        <!-- Botones -->
                        <div class="col-md-12 mb-2 text-center">
                            <button type="submit" class="btn btn-success" id="guardar">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar la compra -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="cancelModalLabel">Mensaje de confirmación</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres cancelar la compra?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btnCancelarCompra" type="button" class="btn btn-danger" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
        $('#btn_agregar').click(function () {
            agregarProducto();
        });

        $('#btnCancelarCompra').click(function () {
            cancelarCompra();
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

    function agregarProducto() {
        let idProducto = $('#producto_id').val();
        let nameProducto = $('#producto_id option:selected').text();
        let cantidad = parseInt($('#cantidad').val());
        let precioCompra = parseFloat($('#precio_compra').val());
        let precioVenta = parseFloat($('#precio_venta').val());

        // Validar producto
        if (!idProducto) {
            showModal('Seleccione un producto');
            return;
        }

        // Validaciones
        if (!cantidad || cantidad <= 0) {
            showModal('Ingrese una cantidad válida');
            return;
        }

        if (!Number.isInteger(cantidad)) {
            showModal('La cantidad debe ser entera');
            return;
        }

        if (!precioCompra || precioCompra <= 0) {
            showModal('Precio de compra inválido');
            return;
        }

        if (!precioVenta || precioVenta <= 0) {
            showModal('Precio de venta inválido');
            return;
        }

        // Validar lógica de precios
        if (precioVenta <= precioCompra) {
            showModal('El precio de venta debe ser mayor al de compra');
            return;
        }

        // Calcular subtotal
        subTotal[cont] = round(cantidad * precioCompra);

        sumas = round(sumas + subTotal[cont]);
        igv = round((sumas * impuesto) / 100);
        total = round(sumas + igv);

        // Crear fila
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
                    <input type="hidden" name="arraypreciocompra[]" value="${precioCompra}">
                    ${precioCompra}
                </td>
                <td>
                    <input type="hidden" name="arrayprecioventa[]" value="${precioVenta}">
                    ${precioVenta}
                </td>
                <td>
                    ${subTotal[cont]}
                </td>
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

        // Insertar fila en tbody
        $('#tabla_detalle tbody').append(fila);
        reordenarFilas();

        actualizarTotales();
        limpiarCampos();
        cont++;
        disableButtons();
    }

    function eliminarProducto(indice) {
        // Restar subtotal
        sumas = round(sumas - subTotal[indice]);

        igv = round((sumas * impuesto) / 100);
        total = round(sumas + igv);

        // Eliminar fila
        $('#fila' + indice).remove();

        actualizarTotales();
        disableButtons();
        limpiarCampos();
        reordenarFilas();
    }

    function cancelarCompra() {
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
        showModal('Compra cancelada', 'success');
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
        $('#precio_compra').val('');
        $('#precio_venta').val('');
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