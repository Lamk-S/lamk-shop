<div class="table-responsive border rounded-3">
    <table id="tabla_detalle" class="table table-hover mb-0 align-middle">
        <thead class="bg-light">
            <tr>
                <th class="border-bottom-0 text-center" style="width: 50px;">#</th>
                <th class="border-bottom-0">Producto</th>
                <th class="border-bottom-0 text-center">Talla</th>
                <th class="border-bottom-0 text-center">Cant.</th>
                <th class="border-bottom-0 text-end">Costo Unit.</th>
                <th class="border-bottom-0 text-end">Ref. Venta</th>
                <th class="border-bottom-0 text-end">Desc.</th>
                <th class="border-bottom-0 text-end">IGV</th>
                <th class="border-bottom-0 text-end">Total</th>
                <th class="border-bottom-0 text-center"><i class="fas fa-cog"></i></th>
            </tr>
        </thead>

        <tbody></tbody>

        <tfoot class="bg-light">
            <tr>
                <th colspan="8" class="text-end fw-semibold">Subtotal bruto:</th>
                <th class="text-end text-dark">S/ <span id="subtotal_bruto">0.00</span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="8" class="text-end fw-semibold">Descuento total:</th>
                <th class="text-end text-danger">S/ <span id="descuento_total">0.00</span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="8" class="text-end fw-semibold">IGV (18%):</th>
                <th class="text-end text-dark">S/ <span id="igv">0.00</span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="8" class="text-end fw-bold text-dark fs-6">TOTAL:</th>
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