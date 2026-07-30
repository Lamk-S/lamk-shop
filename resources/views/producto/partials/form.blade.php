<div class="row g-4">
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold text-uppercase text-primary mb-4 border-bottom pb-2">
                    <i class="fas fa-info-circle me-2"></i>Ficha de Identificación de Producto
                </h6>
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 small mb-4 d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>Por favor, verifique las alertas e inconsistencias en el formulario.
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label fw-bold text-secondary small text-uppercase">
                            Código (SKU) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                            <input type="text" name="codigo" id="codigo" class="form-control border-start-0 text-uppercase @error('codigo') is-invalid @enderror" value="{{ old('codigo', $producto->codigo ?? '') }}" placeholder="Ej. NIKE-PEG39" autocomplete="off" required>
                        </div>
                        @error('codigo') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="nombre" class="form-label fw-bold text-secondary small text-uppercase">
                            Nombre Comercial <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control shadow-sm @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto->nombre ?? '') }}" placeholder="Ej. Zapatillas Running Air Zoom Pegasus 39" required>
                        @error('nombre') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="descripcion" class="form-label fw-bold text-secondary small text-uppercase">
                            Descripción Técnica <span class="text-muted fw-normal text-lowercase">(Opcional)</span>
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control shadow-sm @error('descripcion') is-invalid @enderror" placeholder="Indique materiales, tipo de pisada..." style="resize: none;">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                        @error('descripcion') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="precio_compra" class="form-label fw-bold text-secondary small text-uppercase">
                            Costo Base <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input type="number" step="0.01" min="0" name="precio_compra" id="precio_compra" class="form-control border-start-0 text-end fw-medium @error('precio_compra') is-invalid @enderror" value="{{ old('precio_compra', $producto->precio_compra ?? '0.00') }}" required>
                        </div>
                        @error('precio_compra') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="precio_venta" class="form-label fw-bold text-secondary small text-uppercase">
                            Precio de Venta <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" class="form-control border-start-0 text-end fw-bold text-success @error('precio_venta') is-invalid @enderror" value="{{ old('precio_venta', $producto->precio_venta ?? '0.00') }}" required>
                        </div>
                        @error('precio_venta') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="stock_minimo" class="form-label fw-bold text-secondary small text-uppercase">
                            Stock Mínimo (Alerta) <span class="text-danger">*</span>
                        </label>
                        <input type="number" min="0" name="stock_minimo" id="stock_minimo" class="form-control shadow-sm text-center @error('stock_minimo') is-invalid @enderror" value="{{ old('stock_minimo', $producto->stock_minimo ?? 5) }}" required>
                        @error('stock_minimo') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="afecto_igv" class="form-label fw-bold text-secondary small text-uppercase">Régimen Impositivo</label>
                        <select name="afecto_igv" id="afecto_igv" class="form-select shadow-sm @error('afecto_igv') is-invalid @enderror">
                            <option value="1" @selected((string) $afectoIgvActual === '1')>Operación Afecta (18%)</option>
                            <option value="0" @selected((string) $afectoIgvActual === '0')>Exonerada / Inafecta</option>
                        </select>
                        @error('afecto_igv') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold text-uppercase text-success mb-4 border-bottom pb-2">
                    <i class="fas fa-tags me-2"></i>Clasificación y Atributos
                </h6>
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="tipo_producto" class="form-label fw-bold text-secondary small text-uppercase">
                            Clasificación Principal <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_producto" id="tipo_producto" class="form-control selectpicker show-tick border shadow-sm @error('tipo_producto') is-invalid @enderror" required>
                            <option value="">Seleccione tipo...</option>
                            @isset($optionsTipoProducto)
                                @foreach($optionsTipoProducto as $value => $label)
                                    <option value="{{ $value }}" @selected($tipoProductoActual === $value)>
                                        {{ $value === 'ZAPATILLA' ? '👟 ' : ($value === 'ROPA' ? '👕 ' : '🎒 ') }}{{ $label }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('tipo_producto') 
                            <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="marca_id" class="form-label fw-bold text-secondary small text-uppercase">
                            Marca <span class="text-danger">*</span>
                        </label>
                        <select name="marca_id" id="marca_id" 
                                class="form-control selectpicker show-tick border shadow-sm" 
                                data-live-search="true" 
                                data-size="6"
                                title="Seleccione marca..." required>
                            @foreach($marcas as $item)
                                <option value="{{ $item->id }}" @selected((string) old('marca_id', $producto->marca_id ?? '') === (string) $item->id)>
                                    {{ strtoupper($item->nombre) }}
                                </option>
                            @endforeach
                        </select>
                        @error('marca_id') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="categoria_id" class="form-label fw-bold text-secondary small text-uppercase">
                            Segmentos (Múltiple) <span class="text-danger">*</span>
                        </label>
                        <select name="categoria_id[]" id="categoria_id" 
                                class="form-control selectpicker show-tick border shadow-sm" 
                                data-live-search="true" 
                                data-size="6" 
                                multiple
                                data-selected-text-format="count > 3"
                                data-count-selected-text="{0} categorías seleccionadas"
                                title="Seleccione segmentos..." required>
                            @foreach($categorias as $item)
                                <option value="{{ $item->id }}" @selected(in_array($item->id, $selectedCategorias))>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded-3 shadow-sm d-flex align-items-center justify-content-between">
                            <div>
                                <div class="form-check form-switch mb-0">
                                    <input type="hidden" name="maneja_tallas" value="0">
                                    <input class="form-check-input" 
                                        type="checkbox" 
                                        role="switch" 
                                        id="maneja_tallas" 
                                        name="maneja_tallas" 
                                        value="1" 
                                        @checked((string) $manejaTallasActual === '1')>
                                    <label class="form-check-label fw-bold text-dark small" for="maneja_tallas">
                                        Segmentación por Tallas
                                    </label>
                                </div>
                                <div class="text-muted mt-1" style="font-size:0.75rem;">
                                    Zapatillas y Ropa exigen apertura de tallas. Accesorios se manejan como Talla Única.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label for="img_path" class="form-label fw-bold text-secondary small text-uppercase">
                            Fotografía <span class="text-muted fw-normal text-lowercase">(JPG / PNG)</span>
                        </label>
                        <input type="file" name="img_path" id="img_path" class="form-control shadow-sm @error('img_path') is-invalid @enderror" accept="image/*">
                        @error('img_path') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                        
                        @if(isset($producto) && $producto->img_path)
                            <div class="mt-3 text-center border p-2 rounded-3 bg-light shadow-sm">
                                <span class="text-muted small d-block mb-2">Imagen en Servidor:</span>
                                <img src="{{ asset('storage/' . $producto->img_path) }}" alt="{{ $producto->nombre }}" class="img-fluid rounded-3 border-light" style="max-height: 120px; object-fit: contain;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        @include('producto.partials.variantes')
    </div>

    <div class="col-12 mt-3">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center text-secondary small bg-light px-3 py-2 rounded-3 border">
                    <i class="fa-solid fa-circle-nodes text-primary me-2"></i>
                    <span><strong>Nota:</strong> El stock general se calcula automáticamente sumando el inventario de sus variantes.</span>
                </div>
                <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                    <a href="{{ route('productos.index') }}" class="btn btn-light px-4 rounded-3 border shadow-sm">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-3">
                        <i class="fas fa-save me-2"></i>Guardar Producto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>