<div class="table-responsive border rounded-3 bg-white mb-4 shadow-sm">
    <table id="tabla_detalle" class="table table-hover mb-0 align-middle text-nowrap">
        <thead class="bg-light">
            <tr>
                <th class="border-bottom-0 text-center text-secondary small fw-semibold" style="width: 50px;">#</th>
                <th class="border-bottom-0 text-secondary small fw-semibold">Producto</th>
                <th class="border-bottom-0 text-center text-secondary small fw-semibold">Talla</th>
                <th class="border-bottom-0 text-center text-secondary small fw-semibold">Cant.</th>
                <th class="border-bottom-0 text-end text-secondary small fw-semibold">Costo Unit.</th>
                <th class="border-bottom-0 text-end text-secondary small fw-semibold">Ref. Venta</th>
                <th class="border-bottom-0 text-end text-secondary small fw-semibold">Desc.</th>
                <th class="border-bottom-0 text-end text-secondary small fw-semibold">IGV</th>
                <th class="border-bottom-0 text-end text-secondary small fw-semibold">Total</th>
                <th class="border-bottom-0 text-center text-secondary small fw-semibold"><i class="fas fa-cog"></i></th>
            </tr>
        </thead>

        <tbody>
            <!-- Llenado por JS -->
        </tbody>

        <tfoot class="bg-light">
            <tr>
                <th colspan="8" class="text-end fw-medium text-muted">Subtotal bruto:</th>
                <th class="text-end text-dark">S/ <span id="subtotal_bruto">0.00</span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="8" class="text-end fw-medium text-muted">Descuento total:</th>
                <th class="text-end text-danger">- S/ <span id="descuento_total">0.00</span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="8" class="text-end fw-medium text-muted">IGV (18%):</th>
                <th class="text-end text-dark">S/ <span id="igv">0.00</span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="8" class="text-end fw-bold text-dark fs-6">TOTAL FINAL:</th>
                <th class="text-end fw-bold text-primary fs-6">
                    S/ <span id="total">0.00</span>
                    <input type="hidden" name="subtotal" value="0" id="inputSubtotal">
                    <input type="hidden" name="descuento_total" value="0" id="inputDescuentoTotal">
                    <input type="hidden" name="impuesto_total" value="0" id="inputIgvTotal">
                    <input type="hidden" name="total" value="0" id="inputTotal">
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>