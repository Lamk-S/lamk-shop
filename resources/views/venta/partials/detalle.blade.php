<div class="table-responsive border rounded-3">
    <table id="tabla_detalle" class="table table-hover mb-0 align-middle">
        <thead class="bg-light">
            <tr>
                <th class="text-secondary fw-semibold text-center" style="width: 50px;">#</th>
                <th class="text-secondary fw-semibold">Producto</th>
                <th class="text-secondary fw-semibold text-center">Talla</th>
                <th class="text-secondary fw-semibold text-center">Cant.</th>
                <th class="text-secondary fw-semibold text-end">Precio</th>
                <th class="text-secondary fw-semibold text-end">Desc.</th>
                <th class="text-secondary fw-semibold text-end">IGV</th>
                <th class="text-secondary fw-semibold text-end">Total</th>
                <th class="text-center" style="width: 60px;"></th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="bg-light pos-totals border-top">
            <tr>
                <th colspan="7" class="text-end py-3">Subtotal bruto:</th>
                <th class="text-end py-3">
                    <input type="hidden" name="subtotal" value="0" id="inputSubtotal">
                    <span id="subtotal_bruto">0.00</span>
                </th>
                <th></th>
            </tr>
            <tr>
                <th colspan="7" class="text-end py-3">Descuento total:</th>
                <th class="text-end py-3">
                    <input type="hidden" name="descuento_total" value="0" id="inputDescuentoTotal">
                    <span id="descuento_total">0.00</span>
                </th>
                <th></th>
            </tr>
            <tr>
                <th colspan="7" class="text-end py-3">IGV:</th>
                <th class="text-end py-3">
                    <input type="hidden" name="impuesto_total" value="0" id="inputIgvTotal">
                    <span id="igv">0.00</span>
                </th>
                <th></th>
            </tr>
            <tr class="total-row">
                <th colspan="7" class="text-end py-3">Total a Pagar:</th>
                <th class="text-end py-3">
                    <input type="hidden" name="total" value="0" id="inputTotal">
                    <span id="total" class="fw-bold">0.00</span>
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>