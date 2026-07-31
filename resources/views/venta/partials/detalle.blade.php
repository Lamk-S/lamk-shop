<div class="card border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column h-100">
    <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-cart-shopping text-primary me-2"></i>Detalle de la venta
                </h5>
                <small class="text-muted">Productos agregados al carrito</small>
            </div>
            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 align-self-start align-self-sm-auto">
                <span id="cantidadItems">0</span> artículos
            </span>
        </div>
    </div>

    <div class="table-responsive flex-grow-1">
        <table id="tabla_detalle" class="table align-middle mb-0" style="min-width: 600px;">
            <thead class="bg-light text-secondary text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4" style="width:40%">Producto</th>
                    <th class="text-center" style="width:25%">Cantidad</th>
                    <th class="text-end" style="width:15%">Precio</th>
                    <th class="text-end" style="width:15%">Total</th>
                    <th class="pe-4 text-center" style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="cart-body">
                <tr id="empty-cart-row">
                    <td colspan="5" class="py-5">
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <i class="fa-solid fa-cart-shopping text-secondary opacity-25" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="fw-semibold text-secondary mb-1">El carrito está vacío</h5>
                            <p class="text-muted mb-0 small">Escanea un código o usa el buscador para empezar.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="border-top bg-light mt-auto">
        <div class="row g-0">
            <div class="col-12 col-lg-6 offset-lg-6">
                <div class="p-3 p-md-4">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-medium">Subtotal</span>
                        <span class="fw-bold text-dark">S/ <span id="subtotal_bruto">0.00</span></span>
                        <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-medium">Descuento</span>
                        <span class="text-danger fw-bold">- S/ <span id="descuento_total">0.00</span></span>
                        <input type="hidden" name="descuento_total" id="inputDescuentoTotal" value="0">
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted fw-medium">IGV (18%)</span>
                        <span class="fw-bold text-dark">S/ <span id="igv">0.00</span></span>
                        <input type="hidden" name="impuesto_total" id="inputIgvTotal" value="0">
                    </div>
                    
                    <div class="border-top border-secondary border-opacity-25 pt-3 mt-3 d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-dark">TOTAL</span>
                        <span class="fs-3 fw-bold text-primary" style="letter-spacing: -1px;">
                            S/ <span id="total">0.00</span>
                        </span>
                        <input type="hidden" name="total" id="inputTotal" value="0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>