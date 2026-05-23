@extends('layouts.app')

@section('title', 'Realizar Compra')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Registrar Compra</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('compras.index') }}" class="text-decoration-none">Compras</a></li>
            <li class="breadcrumb-item active">Nueva compra</li>
        </ol>
    </div>

    <form action="{{ route('compras.store') }}" method="post">
        @csrf
        <div class="row g-4">
            
            <!-- Panel Izquierdo: Selección de Productos -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
                        <i class="fa-solid fa-cart-plus text-primary fs-5 me-2"></i>
                        <h5 class="mb-0 fw-semibold text-dark">Detalle de Productos</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Producto -->
                            <div class="col-md-12">
                                <label for="producto_id" class="form-label fw-medium text-secondary small">Buscar Producto</label>
                                <select name="producto_id" id="producto_id" class="form-control selectpicker show-tick" data-live-search="true" data-size="5" title="Seleccione o busque un producto...">
                                    @foreach ($productos as $item)
                                        <option value="{{ $item->id }}" data-subtext="Cod: {{ $item->codigo }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Cantidad y Precios -->
                            <div class="col-md-4">
                                <label for="cantidad" class="form-label fw-medium text-secondary small">Cantidad</label>
                                <input type="number" name="cantidad" id="cantidad" class="form-control" min="1">
                            </div>
                            <div class="col-md-4">
                                <label for="precio_compra" class="form-label fw-medium text-secondary small">Precio de Compra (S/)</label>
                                <input type="number" name="precio_compra" id="precio_compra" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="precio_venta" class="form-label fw-medium text-secondary small">Precio de Venta (S/)</label>
                                <input type="number" name="precio_venta" id="precio_venta" class="form-control" step="0.01" min="0">
                            </div>

                            <!-- Botón Agregar -->
                            <div class="col-md-12 text-end mt-3">
                                <button id="btn_agregar" class="btn btn-primary shadow-sm px-4" type="button">
                                    <i class="fas fa-plus me-2"></i>Agregar a la lista
                                </button>
                            </div>

                            <!-- Tabla Detalle -->
                            <div class="col-md-12 mt-4">
                                <div class="table-responsive border rounded-3">
                                    <table id="tabla_detalle" class="table table-hover table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th class="border-bottom-0">#</th>
                                                <th class="border-bottom-0">Producto</th>
                                                <th class="border-bottom-0">Cant.</th>
                                                <th class="border-bottom-0">P. Compra</th>
                                                <th class="border-bottom-0">P. Venta</th>
                                                <th class="border-bottom-0 text-end">Subtotal</th>
                                                <th class="border-bottom-0 text-center"><i class="fas fa-cog"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Las filas se inyectan mediante JS -->
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <th colspan="5" class="text-end fw-semibold">Subtotal:</th>
                                                <th class="text-end text-dark">S/ <span id="sumas">0.00</span></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="text-end fw-semibold">IGV (18%):</th>
                                                <th class="text-end text-dark">S/ <span id="igv">0.00</span></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="text-end fw-bold text-dark fs-6">TOTAL:</th>
                                                <th class="text-end fw-bold text-primary fs-6">
                                                    S/ <span id="total">0.00</span>
                                                    <input type="hidden" name="total" value="0" id="inputTotal">
                                                </th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Derecho: Datos de Facturación -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
                        <i class="fa-solid fa-file-invoice text-success fs-5 me-2"></i>
                        <h5 class="mb-0 fw-semibold text-dark">Datos del Comprobante</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="row g-4">
                            
                            <!-- Proveedor -->
                            <div class="col-12">
                                <label for="proveedore_id" class="form-label fw-medium text-secondary small">Proveedor <span class="text-danger">*</span></label>
                                <select name="proveedore_id" id="proveedore_id" class="form-control selectpicker show-tick" data-live-search="true" title="Seleccione proveedor" data-size="5">
                                    @foreach ( $proveedores as $item )
                                        <option value="{{ $item->id }}">{{ $item->persona->razon_social }}</option>
                                    @endforeach
                                </select>
                                @error('proveedore_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Comprobante -->
                            <div class="col-12">
                                <label for="comprobante_id" class="form-label fw-medium text-secondary small">Tipo de Comprobante <span class="text-danger">*</span></label>
                                <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker show-tick" title="Seleccione tipo">
                                    @foreach ( $comprobantes as $item )
                                        <option value="{{ $item->id }}">{{ $item->tipo_comprobante }}</option>
                                    @endforeach
                                </select>
                                @error('comprobante_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Número comprobante -->
                            <div class="col-12">
                                <label for="numero_comprobante" class="form-label fw-medium text-secondary small">Número de comprobante <span class="text-danger">*</span></label>
                                <input required type="text" name="numero_comprobante" id="numero_comprobante" class="form-control" placeholder="Ej: F001-000456">
                                @error('numero_comprobante') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Fecha y Hora -->
                            <div class="col-12">
                                <label class="form-label fw-medium text-secondary small">Fecha de Emisión</label>
                                <input readonly type="text" class="form-control bg-light" value="{{ date('d/m/Y') }}">
                                <input type="hidden" name="fecha_hora" value="{{ \Carbon\Carbon::now()->toDateTimeString() }}">
                            </div>

                            <!-- Impuesto -->
                            <div class="col-12">
                                <label for="impuesto" class="form-label fw-medium text-secondary small">Monto Impuesto (Automático)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">S/</span>
                                    <input readonly type="text" name="impuesto" id="impuesto" class="form-control bg-light border-start-0" value="0.00">
                                </div>
                                @error('impuesto') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                        </div>
                    </div>
                    
                    <!-- Botones Generales -->
                    <div class="card-footer bg-white border-top border-light p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success py-2 shadow-sm fw-bold" id="guardar"><i class="fas fa-check-circle me-2"></i>Procesar Compra</button>
                            <button id="cancelar" type="button" class="btn btn-light py-2 text-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancelar Compra</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!-- Modal para cancelar la compra -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
        $('#btn_agregar').click(function () { agregarProducto(); });
        $('#btnCancelarCompra').click(function () { cancelarCompra(); });
        disableButtons();
    });

    let cont = 0;
    let subTotal = [];
    let sumas = 0;
    let igv = 0;
    let total = 0;
    const impuesto = 18;

    function agregarProducto() {
        let idProducto = $('#producto_id').val();
        let nameProducto = $('#producto_id option:selected').text();
        let cantidad = parseInt($('#cantidad').val());
        let precioCompra = parseFloat($('#precio_compra').val());
        let precioVenta = parseFloat($('#precio_venta').val());

        if (!idProducto) { showModal('Seleccione un producto'); return; }
        if (!cantidad || cantidad <= 0 || !Number.isInteger(cantidad)) { showModal('Ingrese una cantidad entera válida'); return; }
        if (!precioCompra || precioCompra <= 0) { showModal('Precio de compra inválido'); return; }
        if (!precioVenta || precioVenta <= 0) { showModal('Precio de venta inválido'); return; }
        if (precioVenta <= precioCompra) { showModal('El precio de venta debe ser mayor al de compra'); return; }

        subTotal[cont] = round(cantidad * precioCompra);
        sumas = round(sumas + subTotal[cont]);
        igv = round((sumas * impuesto) / 100);
        total = round(sumas + igv);

        let fila = `
            <tr id="fila${cont}">
                <th class="fila-numero align-middle"></th>
                <td class="align-middle">
                    <input type="hidden" name="arrayidproducto[]" value="${idProducto}">
                    <span class="fw-medium">${nameProducto}</span>
                </td>
                <td class="align-middle">
                    <input type="hidden" name="arraycantidad[]" value="${cantidad}">
                    ${cantidad}
                </td>
                <td class="align-middle">
                    <input type="hidden" name="arraypreciocompra[]" value="${precioCompra}">
                    S/ ${precioCompra.toFixed(2)}
                </td>
                <td class="align-middle">
                    <input type="hidden" name="arrayprecioventa[]" value="${precioVenta}">
                    S/ ${precioVenta.toFixed(2)}
                </td>
                <td class="align-middle text-end fw-medium text-dark">
                    S/ ${subTotal[cont].toFixed(2)}
                </td>
                <td class="text-center align-middle">
                    <button class="btn btn-sm btn-outline-danger border-0" type="button" onclick="eliminarProducto(${cont})">
                        <i class="fa-solid fa-trash"></i>
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

    function cancelarCompra() {
        $('#tabla_detalle tbody').empty();
        cont = 0; subTotal = []; sumas = 0; igv = 0; total = 0;
        actualizarTotales();
        limpiarCampos();
        disableButtons();
        showModal('Compra cancelada', 'success');
    }

    function actualizarTotales() {
        $('#sumas').html(sumas.toFixed(2));
        $('#igv').html(igv.toFixed(2));
        $('#total').html(total.toFixed(2));
        $('#impuesto').val(igv.toFixed(2));
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
            toast: true, position: "top-end", showConfirmButton: false, timer: 2000,
            timerProgressBar: true, customClass: { popup: 'shadow-sm border-0 rounded-3' }
        }).fire({ icon: icon, title: message });
    }
</script>
@endpush