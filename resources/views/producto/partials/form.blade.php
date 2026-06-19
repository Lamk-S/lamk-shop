@php
    $editing = isset($producto) && $producto->exists;
    $selectedCategorias = old('categoria_id', $editing ? $producto->categorias->pluck('id')->all() : []);
    $tipoProductoActual = old('tipo_producto', $editing ? $producto->tipo_producto : '');
    $manejaTallasActual = old('maneja_tallas', $editing ? (int) $producto->maneja_tallas : 1);
    $afectoIgvActual = old('afecto_igv', $editing ? (int) $producto->afecto_igv : 1);
    $variantRows = old('variantes');

    if (is_null($variantRows)) {
        if ($editing && $producto->relationLoaded('variantes')) {
            $variantRows = $producto->variantes->map(function ($v) {
                return [
                    'id' => $v->id,
                    'talla_id' => $v->talla_id,
                    'codigo_barra' => $v->codigo_barra,
                    'stock_actual' => $v->stock_actual,
                    'stock_minimo' => $v->stock_minimo,
                    'estado' => $v->estado,
                ];
            })->toArray();
        } else {
            $variantRows = [];
        }
    }

    if (empty($variantRows)) {
        $variantRows = [
            [
                'id' => null,
                'talla_id' => '',
                'codigo_barra' => '',
                'stock_actual' => 0,
                'stock_minimo' => 0,
                'estado' => 1,
            ],
        ];
    }
@endphp

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .section-title { font-size: 0.95rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid #f1f3f5; }
    .card-dashboard { border: none; transition: all 0.25s ease; }
    .form-label-custom { font-size: 0.85rem; font-weight: 600; color: #495057; }
    .info-badge-summary { background: #f8f9fa; border-left: 4px solid #0d6efd; border-radius: 8px; }
    .bootstrap-select .dropdown-toggle:focus { outline: none !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important; }
    .bootstrap-select .btn-light { background-color: #fff !important; border-color: #dee2e6 !important; color: #212529 !important; }
    .bootstrap-select .dropdown-menu { border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; border: 1px solid #dee2e6 !important; }
</style>
@endpush

<div class="row g-4">
    {{-- COLUMNA IZQUIERDA: INFORMACIÓN GENERAL DEL REGISTRO --}}
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm rounded-4 h-100 card-dashboard">
            <div class="card-body p-4 p-md-5">
                <h6 class="section-title text-primary">
                    <i class="fas fa-info-circle me-2"></i>Ficha de Identificación de Producto
                </h6>
                
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 border-0 py-2 small mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>Por favor, verifique las alertas e inconsistencias en el formulario.
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label form-label-custom">
                            Código Principal (SKU) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                            <input type="text" name="codigo" id="codigo" class="form-control border-start-0 @error('codigo') is-invalid @enderror" value="{{ old('codigo', $editing ? $producto->codigo : '') }}" placeholder="Ej. NIKE-PEG39" autocomplete="off">
                        </div>
                        @error('codigo') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="codigo_barra" class="form-label form-label-custom">
                            Código de Barras Global <span class="text-muted fw-normal">(EAN / Maestro)</span>
                        </label>
                        <input type="text" name="codigo_barra" id="codigo_barra" class="form-control @error('codigo_barra') is-invalid @enderror" value="{{ old('codigo_barra', $editing ? $producto->codigo_barra : '') }}" placeholder="Ej. 7750123456789">
                        @error('codigo_barra') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="nombre" class="form-label form-label-custom">
                            Nombre Comercial del Producto <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $editing ? $producto->nombre : '') }}" placeholder="Ej. Zapatillas Running Air Zoom Pegasus 39">
                        @error('nombre') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="descripcion" class="form-label form-label-custom">
                            Descripción Técnica o Características <span class="text-muted fw-normal">(Opcional)</span>
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Indique materiales, tipo de pisada, especificaciones comerciales..." style="resize: none;">{{ old('descripcion', $editing ? $producto->descripcion : '') }}</textarea>
                        @error('descripcion') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="precio_compra" class="form-label form-label-custom">
                            Costo de Compra Base <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input type="number" step="0.01" min="0" name="precio_compra" id="precio_compra" class="form-control border-start-0 text-end @error('precio_compra') is-invalid @enderror" value="{{ old('precio_compra', $editing ? $producto->precio_compra : '0.00') }}">
                        </div>
                        @error('precio_compra') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="precio_venta" class="form-label form-label-custom">
                            Precio de Venta Sugerido <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" class="form-control border-start-0 text-end @error('precio_venta') is-invalid @enderror" value="{{ old('precio_venta', $editing ? $producto->precio_venta : '0.00') }}">
                        </div>
                        @error('precio_venta') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="stock_minimo" class="form-label form-label-custom">
                            Stock de Alerta Mínimo Corporativo <span class="text-danger">*</span>
                        </label>
                        <input type="number" min="0" name="stock_minimo" id="stock_minimo" class="form-control text-center @error('stock_minimo') is-invalid @enderror" value="{{ old('stock_minimo', $editing ? $producto->stock_minimo : 5) }}">
                        @error('stock_minimo') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="afecto_igv" class="form-label form-label-custom">Régimen Impositivo (IGV)</label>
                        <select name="afecto_igv" id="afecto_igv" class="form-select @error('afecto_igv') is-invalid @enderror">
                            <option value="1" @selected((string) $afectoIgvActual === '1')>Operación Afecta (Gravada 18%)</option>
                            <option value="0" @selected((string) $afectoIgvActual === '0')>Operación Exonerada / Inafecta</option>
                        </select>
                        @error('afecto_igv') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- COLUMNA DERECHA: LOGÍSTICA, CATEGORIZACIÓN Y MULTI-SELECTS --}}
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm rounded-4 h-100 card-dashboard">
            <div class="card-body p-4 p-md-5">
                <h6 class="section-title text-success">
                    <i class="fas fa-tags me-2"></i>Clasificación, Categorías y Atributos
                </h6>
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="tipo_producto" class="form-label form-label-custom">
                            Línea o Tipo de Producto <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_producto" id="tipo_producto" class="form-select fw-medium @error('tipo_producto') is-invalid @enderror">
                            <option value="">Seleccione tipo...</option>
                            <option value="ZAPATILLA" @selected($tipoProductoActual === 'ZAPATILLA')>👟 Zapatilla</option>
                            <option value="ROPA" @selected($tipoProductoActual === 'ROPA')>👕 Ropa / Textil</option>
                            <option value="ACCESORIO" @selected($tipoProductoActual === 'ACCESORIO')>🎒 Accesorio / Equipo</option>
                        </select>
                        @error('tipo_producto') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="marca_id" class="form-label form-label-custom">
                            Marca Corporativa <span class="text-danger">*</span>
                        </label>
                        {{-- Implementación estricta de selectpicker profesional pedido --}}
                        <select name="marca_id" id="marca_id" 
                                class="form-control selectpicker show-tick border shadow-sm" 
                                data-live-search="true" 
                                data-size="6"
                                title="Seleccione una marca corporativa...">
                            @foreach($marcas as $item)
                                <option value="{{ $item->id }}" @selected((string) old('marca_id', $editing ? $producto->marca_id : '') === (string) $item->id)>
                                    {{ strtoupper($item->nombre) }}
                                </option>
                            @endforeach
                        </select>
                        @error('marca_id') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="categoria_id" class="form-label form-label-custom">
                            Categorías / Segmentos Vinculados <span class="text-danger">*</span>
                        </label>
                        {{-- Implementación estricta de selectpicker profesional múltiple pedido --}}
                        <select name="categoria_id[]" id="categoria_id" 
                                class="form-control selectpicker show-tick border shadow-sm" 
                                data-live-search="true" 
                                data-size="6" 
                                multiple
                                data-selected-text-format="count > 3"
                                data-count-selected-text="{0} categorías seleccionadas"
                                title="Seleccione uno o varios segmentos...">
                            @foreach($categorias as $item)
                                <option value="{{ $item->id }}" @selected(in_array($item->id, $selectedCategorias))>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id') <div class="text-danger mt-1 small"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <div class="p-3 info-badge-summary border shadow-sm d-flex align-items-center justify-content-between">
                            <div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input style-switch" type="checkbox" role="switch" id="maneja_tallas" name="maneja_tallas" value="1" @checked((string) $manejaTallasActual === '1')>
                                    <label class="form-check-label fw-bold text-dark small" for="maneja_tallas">
                                        Segmentación por Tallas / Curvas
                                    </label>
                                </div>
                                <div class="text-muted compact-note mt-1" style="font-size:0.78rem;">
                                    Zapatillas y Ropa exigen apertura matricial de tallas. Los Accesorios se manejan comúnmente como Talla Única.
                                </div>
                            </div>
                        </div>
                        @error('maneja_tallas') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="img_path" class="form-label form-label-custom">
                            Fotografía de Control de Stock <span class="text-muted fw-normal">(Formatos JPG / PNG)</span>
                        </label>
                        <input type="file" name="img_path" id="img_path" class="form-control @error('img_path') is-invalid @enderror" accept="image/*">
                        @error('img_path') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                        
                        @if($editing && $producto->img_path)
                            <div class="mt-3 text-center border p-2 rounded-3 bg-light">
                                <span class="text-muted small d-block mb-2">Imagen en Servidor:</span>
                                <img src="{{ asset('storage/' . $producto->img_path) }}" alt="{{ $producto->nombre }}" class="img-fluid rounded-3 shadow-sm border-light" style="max-height: 120px; object-fit: contain;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA INFERIOR COMPLETA: BLOQUE DINÁMICO DE CURVA DE TALLAS --}}
    <div class="col-12">
        @include('producto.partials.variantes')
    </div>

    {{-- BOTONERA DE ACCIÓN PRINCIPAL --}}
    <div class="col-12 mt-2">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center text-secondary small bg-light px-3 py-2 rounded-3 border">
                    <i class="fa-solid fa-circle-nodes text-primary me-2"></i>
                    <span><strong>Monitor:</strong> Los stocks de variantes acumularán automáticamente el inventario general del catálogo.</span>
                </div>
                <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                    <a href="{{ route('productos.index') }}" class="btn btn-light px-4 rounded-3 border">Regresar al catálogo</a>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-3">
                        <i class="fas fa-save me-2"></i>Guardar Cambios de Producto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>