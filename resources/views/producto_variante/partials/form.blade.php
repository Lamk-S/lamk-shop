<div class="row g-4">
    <div class="col-lg-6">
        <label for="producto_id" class="form-label fw-bold text-secondary small text-uppercase">
            Producto maestro <span class="text-danger">*</span>
        </label>
        <select name="producto_id" id="producto_id"
            class="selectpicker form-control border shadow-sm @error('producto_id') is-invalid @enderror"
            data-width="100%" data-live-search="true" data-size="5"
            title="Busque y seleccione un producto..." required>
            @foreach($productos as $producto)
                <option value="{{ $producto->id }}" data-tipo="{{ $producto->tipo_producto->value }}" 
                    @selected(old('producto_id', $productoVariante->producto_id ?? '') == $producto->id)>
                    {{ $producto->codigo }} — {{ $producto->nombre }} ({{ ucfirst(strtolower($producto->tipo_producto->value)) }})
                </option>
            @endforeach
        </select>
        @error('producto_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
    </div>

    <div class="col-lg-6">
        <label for="talla_id" class="form-label fw-bold text-secondary small text-uppercase">
            Talla compatible <span class="text-danger">*</span>
        </label>
        <select name="talla_id" id="talla_id"
            class="selectpicker form-control border shadow-sm @error('talla_id') is-invalid @enderror"
            data-width="100%" data-live-search="true" data-size="5" required>
        </select>
        @error('talla_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
        
        <div class="bg-light border rounded-3 p-2 mt-3 text-muted small d-flex align-items-center shadow-sm" id="talla-helper">
            <i class="fas fa-info-circle me-2"></i>Selecciona un producto para filtrar las tallas válidas.
        </div>
    </div>

    <div class="col-lg-2 col-md-4">
        <label for="stock_actual" class="form-label fw-bold text-secondary small text-uppercase">
            Stock actual <span class="text-danger">*</span>
        </label>
        <input type="number" min="0" name="stock_actual" id="stock_actual"
            class="form-control text-center shadow-sm @error('stock_actual') is-invalid @enderror"
            value="{{ old('stock_actual', $productoVariante->stock_actual ?? 0) }}" required>
        @error('stock_actual') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
    </div>

    <div class="col-lg-2 col-md-4">
        <label for="stock_minimo" class="form-label fw-bold text-secondary small text-uppercase">
            Stock mínimo <span class="text-danger">*</span>
        </label>
        <input type="number" min="0" name="stock_minimo" id="stock_minimo"
            class="form-control text-center shadow-sm @error('stock_minimo') is-invalid @enderror"
            value="{{ old('stock_minimo', $productoVariante->stock_minimo ?? 0) }}" required>
        @error('stock_minimo') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
    </div>

    <div class="col-lg-2 col-md-4">
        <label for="estado" class="form-label fw-bold text-secondary small text-uppercase">Estado</label>
        <select name="estado" id="estado" class="form-select shadow-sm @error('estado') is-invalid @enderror">
            <option value="1" @selected(old('estado', $productoVariante->estado ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('estado', $productoVariante->estado ?? 1) == 0)>Inactivo</option>
        </select>
        @error('estado') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
    </div>
    
    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
        <a href="{{ route('producto-variantes.index') }}" class="btn btn-light border px-4 shadow-sm">Cancelar</a>
        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
            <i class="fas fa-save me-2"></i>{{ isset($productoVariante->id) ? 'Guardar Cambios' : 'Registrar Variante' }}
        </button>
    </div>
</div>

<script id="tallas-data" type="application/json">
@json($tallas)
</script>

@push('js')
<script>
    $(function () {
        const tallas = JSON.parse(document.getElementById('tallas-data').textContent || '[]');
        const productoSelect = $('#producto_id');
        const tallaSelect = $('#talla_id');
        const helper = $('#talla-helper');
        
        let selectedTalla = @json(old('talla_id', $productoVariante->talla_id ?? ''));
        const reglas = {!! $reglasTallas !!};

        const labels = { CALZADO: 'Calzado', ROPA: 'Ropa', UNICA: 'Única' };

        function setMensaje(tipo) {
            if (tipo === 'ZAPATILLA') {
                helper.html('<i class="fas fa-shoe-prints text-primary me-2"></i>Calzado: Solo se permiten tallas de <b>Calzado</b>.');
            } else if (tipo === 'ROPA') {
                helper.html('<i class="fas fa-shirt text-primary me-2"></i>Ropa: Solo se permiten tallas de <b>Ropa</b>.');
            } else if (tipo === 'ACCESORIO') {
                helper.html('<i class="fas fa-box text-primary me-2"></i>Accesorio: Restringido automáticamente a <b>Talla Única</b>.');
            } else {
                helper.html('<i class="fas fa-info-circle me-2"></i>Selecciona un producto para filtrar las tallas válidas.');
            }
        }

        function rebuildTallas(tipo) {
            const permitidas = reglas[tipo] || [];
            const currentValue = String(selectedTalla || '');
            const filtradas = tallas.filter(t => permitidas.includes(String(t.tipo_talla)));

            if(tallaSelect.data('selectpicker')) { tallaSelect.selectpicker('destroy'); }
            tallaSelect.empty();

            if (!tipo) {
                tallaSelect.prop('disabled', true);
                setMensaje('');
                tallaSelect.selectpicker({ noneSelectedText: 'Seleccione primero un producto...' });
                return;
            }

            tallaSelect.prop('disabled', false);

            if (!filtradas.length) {
                tallaSelect.append('<option value="" disabled>No hay tallas disponibles</option>');
            } else {
                const grupos = {};
                filtradas.forEach(t => {
                    const key = String(t.tipo_talla);
                    if (!grupos[key]) grupos[key] = [];
                    grupos[key].push(t);
                });

                Object.keys(grupos).forEach(grupo => {
                    tallaSelect.append(`<optgroup label="${labels[grupo] || grupo}"></optgroup>`);
                    const lastGroup = tallaSelect.find('optgroup').last();
                    grupos[grupo].forEach(t => {
                        lastGroup.append(`<option value="${t.id}">${t.codigo} — ${t.nombre}</option>`);
                    });
                });
            }

            tallaSelect.selectpicker({ noneSelectedText: 'Seleccione primero un producto...' });
            setMensaje(tipo);

            const validIds = new Set(filtradas.map(t => String(t.id)));
            const finalValue = validIds.has(currentValue) ? currentValue : '';
            selectedTalla = finalValue;
            tallaSelect.selectpicker('val', finalValue);
        }

        productoSelect.selectpicker();
        productoSelect.on('changed.bs.select', function() {
            rebuildTallas(String(productoSelect.find('option:selected').data('tipo') || ''));
        });
        
        rebuildTallas(String(productoSelect.find('option:selected').data('tipo') || ''));
    });
</script>
@endpush