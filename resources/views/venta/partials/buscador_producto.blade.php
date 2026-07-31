<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-bottom border-light p-4">
        <h5 class="mb-0 fw-semibold text-dark">
            <i class="fa-solid fa-magnifying-glass text-primary me-2"></i>Búsqueda Manual
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3 bg-light p-3 rounded-3 border">
            <div class="col-md-12">
                <label for="variante_id" class="form-label fw-medium text-secondary small">Buscar producto o talla</label>
                <select name="variante_id" id="variante_id" class="form-control selectpicker shadow-sm border-0" data-live-search="true" data-size="6" title="Escriba el nombre o código...">
                    <option value="">Seleccione un producto</option>
                    @foreach ($variantes as $variante)
                        @php
                            $nombreProducto = $variante->producto->nombre;
                            $talla = $variante->talla?->nombre ?? 'Única';
                            $stock = $variante->stock_actual ?? 0;
                        @endphp
                        @if($stock > 0)
                            <option
                                value="{{ $variante->id }}"
                                data-stock="{{ $stock }}"
                                data-precio="{{ $variante->producto->precio_venta ?? 0 }}"
                                data-producto="{{ $nombreProducto }}"
                                data-codigo="{{ $variante->codigo_variante }}"
                                data-talla="{{ $talla }}"
                                data-afecto-igv="{{ $variante->producto->afecto_igv ? 1 : 0 }}"
                                data-subtext="Stock: {{ $stock }}"
                            >
                                {{ $variante->codigo_variante }} | {{ $nombreProducto }} - Talla: {{ $talla }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <div class="help-text-soft mt-2 text-muted small" id="variante_resumen">
                    <i class="fas fa-info-circle me-1"></i>Seleccione para ver detalles
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium text-secondary small">Stock Disp.</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fas fa-box"></i></span>
                    <input type="text" id="stock" class="form-control bg-white text-center fw-bold text-primary" readonly placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium text-secondary small">Precio Venta</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted">S/</span>
                    <input type="text" id="precio_venta" class="form-control bg-white text-end fw-bold" readonly placeholder="0.00">
                </div>
            </div>
            <div class="col-md-3">
                <label for="cantidad" class="form-label fw-medium text-secondary small">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control text-center fw-bold" min="1" value="1">
            </div>
            <div class="col-md-3">
                <label for="descuento" class="form-label fw-medium text-secondary small">Desc. (S/)</label>
                <input type="number" name="descuento" id="descuento" class="form-control text-end" value="0.00" min="0" step="0.50">
            </div>
            <div class="col-12 mt-2 text-end">
                <button id="btn_agregar" class="btn btn-primary px-4 shadow-sm" type="button" disabled>
                    <i class="fas fa-cart-plus me-2"></i>Agregar al carrito
                </button>
            </div>
        </div>
    </div>
</div>