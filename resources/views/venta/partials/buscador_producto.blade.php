<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom border-light p-4">
        <h5 class="mb-0 fw-semibold text-dark">
            <i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Detalle de Productos
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3 mb-3 bg-light p-3 rounded-3 border">
            <div class="col-md-12">
                <label for="variante_id" class="form-label fw-medium text-secondary small">Buscar producto / talla</label>
                <select name="variante_id" id="variante_id" class="form-control selectpicker shadow-sm border-0" data-live-search="true" data-size="6" title="Escriba o seleccione un producto...">
                    <option value="">Seleccione un producto</option>
                    @foreach ($variantes as $variante)
                        <option
                            value="{{ $variante->id }}"
                            data-stock="{{ $variante->stock_actual ?? 0 }}"
                            data-precio="{{ $variante->producto->precio_venta ?? 0 }}"
                            data-producto="{{ $variante->producto->nombre }}"
                            data-codigo-producto="{{ $variante->producto->codigo }}"
                            data-codigo-variante="{{ $variante->codigo_variante }}"
                            data-talla="{{ $variante->talla?->nombre ?? 'Sin talla' }}"
                            data-afecto-igv="{{ (int) ($variante->producto->afecto_igv ?? 1) }}"
                        >
                            {{ $variante->codigo_variante }} - {{ $variante->producto->nombre }} - {{ $variante->talla?->nombre ?? 'Sin talla' }}
                        </option>
                    @endforeach
                </select>
                <div class="help-text-soft mt-2" id="variante_resumen">Seleccione un producto para ver stock y precio</div>
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
                    <input disabled type="number" name="precio_venta" id="precio_venta" class="form-control bg-white text-end fw-bold" step="0.01">
                </div>
            </div>

            <div class="col-md-3">
                <label for="cantidad" class="form-label fw-medium text-secondary small">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control text-center" min="1" value="1">
            </div>

            <div class="col-md-3">
                <label for="descuento" class="form-label fw-medium text-secondary small">Descuento</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted">S/</span>
                    <input type="number" name="descuento" id="descuento" class="form-control text-end" value="0" min="0" step="0.01">
                </div>
            </div>

            <div class="col-12 mt-2 text-end">
                <button id="btn_agregar" class="btn btn-primary px-4 shadow-sm" type="button">
                    <i class="fas fa-plus me-2"></i>Agregar al carrito
                </button>
            </div>
        </div>
    </div>
</div>