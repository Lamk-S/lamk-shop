<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex align-items-center">
        <i class="fa-solid fa-cart-plus text-primary fs-5 me-2"></i>
        <div>
            <h5 class="mb-0 fw-semibold text-dark fs-6 fs-md-5">Buscador de Productos</h5>
            <small class="text-muted">Seleccione una variante para agregarla al detalle</small>
        </div>
    </div>

    <div class="card-body p-3 p-md-4">
        <div class="row g-3">
            <div class="col-12">
                <label for="variante_id" class="form-label fw-medium text-secondary small">Buscar producto / talla</label>
                <select name="variante_id" id="variante_id" class="form-control selectpicker show-tick" data-live-search="true" data-size="7" title="Seleccione o busque una variante...">
                    @foreach ($variantes as $item)
                        <option
                            value="{{ $item->id }}"
                            data-subtext="Cod: {{ $item->codigo_variante }}"
                            data-stock="{{ $item->stock_actual }}"
                            data-precio-compra="{{ $item->producto->precio_compra }}"
                            data-precio-venta="{{ $item->producto->precio_venta }}"
                            data-producto="{{ $item->producto->nombre }}"
                            data-codigo="{{ $item->producto->codigo }}"
                            data-talla="{{ $item->talla?->nombre ?? 'Sin talla' }}"
                            data-afecto-igv="{{ (int) ($item->producto->afecto_igv ?? 1) }}"
                        >
                            {{ $item->producto->codigo }} - {{ $item->producto->nombre }} - {{ $item->talla?->nombre ?? 'Sin talla' }}
                        </option>
                    @endforeach
                </select>
                <div class="small text-muted mt-2" id="variante_resumen">
                    Seleccione un producto para previsualizar sus datos.
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label for="stock" class="form-label fw-medium text-secondary small">Stock actual</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="fas fa-box"></i></span>
                    <input disabled type="text" id="stock" class="form-control bg-light text-center fw-bold text-dark">
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label for="costo_unitario" class="form-label fw-medium text-secondary small">Costo Unit. Real</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted">S/</span>
                    <input type="number" id="costo_unitario" class="form-control bg-white text-end fw-bold" step="0.01" min="0">
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label for="precio_venta_ref" class="form-label fw-medium text-secondary small">P. Venta Ref.</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted">S/</span>
                    <input disabled type="number" id="precio_venta_ref" class="form-control bg-light text-end fw-bold" step="0.01">
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label for="cantidad" class="form-label fw-medium text-secondary small">Cantidad</label>
                <input type="number" id="cantidad" class="form-control text-center fw-bold" min="1" value="1">
            </div>

            <div class="col-12 col-md-4">
                <label for="descuento" class="form-label fw-medium text-secondary small">Descuento (Monetario)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted">S/</span>
                    <input type="number" id="descuento" class="form-control text-end" value="0" min="0" step="0.01">
                </div>
            </div>

            <div class="col-12 col-md-8 d-flex align-items-end justify-content-end mt-3 mt-md-0">
                <button id="btn_agregar" class="btn btn-primary px-4 shadow-sm w-100 w-md-auto" type="button">
                    <i class="fas fa-plus me-2"></i>Agregar al detalle
                </button>
            </div>
        </div>
    </div>
</div>