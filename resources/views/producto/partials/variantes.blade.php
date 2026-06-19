@php
    $editing = isset($producto) && $producto->exists;
    $variantRows = $variantRows ?? [
        [
            'id' => null,
            'talla_id' => '',
            'codigo_barra' => '',
            'stock_actual' => 0,
            'stock_minimo' => 0,
            'estado' => 1,
        ],
    ];
    $tallaUnicaId = $tallaUnica?->id ?? '';
@endphp

<div class="card border-0 shadow-sm rounded-4 mt-2">
    <div class="card-header bg-white border-bottom border-light p-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h5 class="mb-1 fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-boxes-packing text-primary me-2"></i>Estructura de Variantes y Stock Inicial
                </h5>
                <p class="text-muted mb-0 small">
                    Defina la curva física de mercadería. El sistema exige asociar al menos una variante de almacenamiento.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm px-3 rounded-3" id="add-variant-row">
                    <i class="fas fa-plus me-1"></i>Añadir Variante de Talla
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body p-4">
        @error('variantes')
            <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 mb-3 small d-flex align-items-center">
                <i class="fas fa-triangle-exclamation me-2"></i>{{ $message }}
            </div>
        @enderror

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" id="variant-table">
                <thead class="table-light text-secondary small text-uppercase">
                    <tr>
                        <th style="min-width: 250px;">Talla Asignada <span class="text-danger">*</span></th>
                        <th style="min-width: 200px;">Código de Barras de Variante <span class="text-muted fw-normal">(Opcional)</span></th>
                        <th style="width: 150px;" class="text-center">Stock Físico Inicial</th>
                        <th style="width: 150px;" class="text-center">Mínimo Alerta</th>
                        <th style="width: 140px;" class="text-center">Disponibilidad</th>
                        <th style="width: 80px;" class="text-center text-danger"><i class="fa-solid fa-trash-can"></i></th>
                    </tr>
                </thead>
                <tbody id="variant-rows">
                    @foreach($variantRows as $index => $row)
                        <tr class="variant-row">
                            <td>
                                <select name="variantes[{{ $index }}][talla_id]" class="form-select form-select-sm variant-talla @error("variantes.$index.talla_id") is-invalid @enderror">
                                    <option value="">Seleccione una dimensión...</option>
                                    <optgroup label="Calzados Comerciales">
                                        @foreach($tallasCalzado as $talla)
                                            <option value="{{ $talla->id }}" @selected((string)($row['talla_id'] ?? '') === (string)$talla->id)>
                                                {{ $talla->codigo }} — Talla {{ $talla->nombre }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Prendas / Textiles">
                                        @foreach($tallasRopa as $talla)
                                            <option value="{{ $talla->id }}" @selected((string)($row['talla_id'] ?? '') === (string)$talla->id)>
                                                {{ $talla->codigo }} — Etiqueta {{ $talla->nombre }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @if($tallaUnica)
                                        <optgroup label="Estándar">
                                            <option value="{{ $tallaUnica->id }}" @selected((string)($row['talla_id'] ?? '') === (string)$tallaUnica->id)>
                                                {{ $tallaUnica->codigo }} — {{ $tallaUnica->nombre }}
                                            </option>
                                        </optgroup>
                                    @endif
                                </select>
                                @error("variantes.$index.talla_id")
                                    <div class="text-danger mt-1 small" style="font-size:0.75rem;">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="text" name="variantes[{{ $index }}][codigo_barra]" class="form-control form-control-sm variant-barcode @error("variantes.$index.codigo_barra") is-invalid @enderror" value="{{ old("variantes.$index.codigo_barra", $row['codigo_barra'] ?? '') }}" placeholder="Código de barras específico">
                                @error("variantes.$index.codigo_barra")
                                    <div class="text-danger mt-1 small" style="font-size:0.75rem;">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" min="0" name="variantes[{{ $index }}][stock_actual]" class="form-control form-control-sm text-center variant-stock-qty @error("variantes.$index.stock_actual") is-invalid @enderror" value="{{ old("variantes.$index.stock_actual", $row['stock_actual'] ?? 0) }}">
                                @error("variantes.$index.stock_actual")
                                    <div class="text-danger mt-1 small" style="font-size:0.75rem;">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" min="0" name="variantes[{{ $index }}][stock_minimo]" class="form-control form-control-sm text-center @error("variantes.$index.stock_minimo") is-invalid @enderror" value="{{ old("variantes.$index.stock_minimo", $row['stock_minimo'] ?? 0) }}">
                                @error("variantes.$index.stock_minimo")
                                    <div class="text-danger mt-1 small" style="font-size:0.75rem;">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <select name="variantes[{{ $index }}][estado]" class="form-select form-select-sm">
                                    <option value="1" @selected((string)($row['estado'] ?? 1) === '1')>Activo</option>
                                    <option value="0" @selected((string)($row['estado'] ?? 1) === '0')>Inactivo</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-variant-row border-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light border-top fw-bold text-dark">
                    <tr>
                        <td colspan="2" class="text-end py-3 text-secondary small text-uppercase">Carga Total de Inventario Registrada:</td>
                        <td class="text-center py-3 text-primary fs-6" id="total-stock-preview">0 unid.</td>
                        <td colspan="3" class="bg-white"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- COMPONENTE TEMPLATE PARA INCORPORACIÓN DE FILAS DINÁMICAS --}}
<template id="variant-row-template">
    <tr class="variant-row">
        <td>
            <select name="variantes[__INDEX__][talla_id]" class="form-select form-select-sm variant-talla">
                <option value="">Seleccione una dimensión...</option>
                <optgroup label="Calzados Comerciales">
                    @foreach($tallasCalzado as $talla)
                        <option value="{{ $talla->id }}">{{ $talla->codigo }} — Talla {{ $talla->nombre }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Prendas / Textiles">
                    @foreach($tallasRopa as $talla)
                        <option value="{{ $talla->id }}">{{ $talla->codigo }} — Etiqueta {{ $talla->nombre }}</option>
                    @endforeach
                </optgroup>
                @if($tallaUnica)
                    <optgroup label="Estándar">
                        <option value="{{ $tallaUnica->id }}">{{ $tallaUnica->codigo }} — {{ $tallaUnica->nombre }}</option>
                    </optgroup>
                @endif
            </select>
        </td>
        <td>
            <input type="text" name="variantes[__INDEX__][codigo_barra]" class="form-control form-control-sm variant-barcode" placeholder="Código de barras específico">
        </td>
        <td>
            <input type="number" min="0" name="variantes[__INDEX__][stock_actual]" class="form-control form-control-sm text-center variant-stock-qty" value="0">
        </td>
        <td>
            <input type="number" min="0" name="variantes[__INDEX__][stock_minimo]" class="form-control form-control-sm text-center" value="0">
        </td>
        <td>
            <select name="variantes[__INDEX__][estado]" class="form-select form-select-sm">
                <option value="1" selected>Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-variant-row border-0">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    (function () {
        const tipoProducto = document.getElementById('tipo_producto');
        const manejaTallas = document.getElementById('maneja_tallas');
        const addBtn = document.getElementById('add-variant-row');
        const rowsContainer = document.getElementById('variant-rows');
        const template = document.getElementById('variant-row-template');
        const generalBarcode = document.getElementById('codigo_barra');
        const totalStockPreview = document.getElementById('total-stock-preview');
        const tallaUnicaId = @json($tallaUnicaId);

        if (!tipoProducto || !manejaTallas || !addBtn || !rowsContainer || !template) return;

        let rowIndex = rowsContainer.querySelectorAll('.variant-row').length;

        function syncUI() {
            const tipo = tipoProducto.value;
            const isTallaUnica = !manejaTallas.checked;

            if (tipo === 'ACCESORIO') {
                if (tipoProducto.dataset.lastValue !== 'ACCESORIO') {
                    manejaTallas.checked = false;
                }
            }

            if (isTallaUnica) {
                addBtn.style.setProperty('display', 'none', 'important');
                adaptToSingleVariant();
            } else {
                addBtn.style.setProperty('display', 'inline-flex', 'important');
                unlockVariantSelects();
            }

            tipoProducto.dataset.lastValue = tipo;
            calculateTotalStockAggregated();
        }

        function adaptToSingleVariant() {
            const rows = rowsContainer.querySelectorAll('.variant-row');
            
            if (rows.length > 1) {
                for (let i = 1; i < rows.length; i++) {
                    rows[i].remove();
                }
                reindexRows();
            }

            const activeRow = rowsContainer.querySelector('.variant-row');
            if (activeRow) {
                const selectTalla = activeRow.querySelector('.variant-talla');
                if (selectTalla && tallaUnicaId) {
                    selectTalla.value = tallaUnicaId;
                    selectTalla.setAttribute('disabled', 'true');
                    
                    removeHiddenTallaInputs(activeRow);
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.className = 'hidden-talla-id-fix';
                    hiddenInput.name = selectTalla.name;
                    hiddenInput.value = tallaUnicaId;
                    activeRow.appendChild(hiddenInput);
                }
                
                syncBarcodeValues();
            }
        }

        function unlockVariantSelects() {
            rowsContainer.querySelectorAll('.variant-row').forEach(row => {
                const selectTalla = row.querySelector('.variant-talla');
                if (selectTalla) {
                    selectTalla.removeAttribute('disabled');
                    if (selectTalla.value === tallaUnicaId && rowIndex > 1) {
                        selectTalla.value = '';
                    }
                }
                removeHiddenTallaInputs(row);
            });
        }

        function removeHiddenTallaInputs(row) {
            row.querySelectorAll('.hidden-talla-id-fix').forEach(el => el.remove());
        }

        function syncBarcodeValues() {
            if (!manejaTallas.checked) {
                const firstBarcode = rowsContainer.querySelector('.variant-barcode');
                if (firstBarcode && generalBarcode) {
                    firstBarcode.value = generalBarcode.value;
                }
            }
        }

        function reindexRows() {
            rowsContainer.querySelectorAll('.variant-row').forEach((row, index) => {
                row.querySelectorAll('input, select').forEach((input) => {
                    if (input.name) {
                        input.name = input.name.replace(/variantes\[\d+\]/, `variantes[${index}]`);
                    }
                });
                const selectTalla = row.querySelector('.variant-talla');
                const hiddenFix = row.querySelector('.hidden-talla-id-fix');
                if (selectTalla && hiddenFix) {
                    hiddenFix.name = selectTalla.name;
                }
            });
            rowIndex = rowsContainer.querySelectorAll('.variant-row').length;
            calculateTotalStockAggregated();
        }

        function calculateTotalStockAggregated() {
            let total = 0;
            rowsContainer.querySelectorAll('.variant-stock-qty').forEach(input => {
                total += parseInt(input.value || 0, 10);
            });
            totalStockPreview.textContent = `${total.toLocaleString('es-PE')} unidades`;
        }

        addBtn.addEventListener('click', function () {
            const html = template.innerHTML.replaceAll('__INDEX__', rowIndex);
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = html.trim();
            const newRow = wrapper.firstElementChild;
            
            rowsContainer.appendChild(newRow);
            rowIndex++;
            reindexRows();
        });

        rowsContainer.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-variant-row');
            if (!btn) return;
            
            const row = btn.closest('.variant-row');
            if (!row) return;

            if (rowsContainer.querySelectorAll('.variant-row').length === 1) {
                row.querySelectorAll('input').forEach(input => {
                    if (input.type === 'number') input.value = 0;
                    else input.value = '';
                });
                const select = row.querySelector('select');
                if (select && !select.disabled) select.selectedIndex = 0;
                calculateTotalStockAggregated();
                return;
            }

            row.remove();
            reindexRows();
        });

        rowsContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('variant-stock-qty')) {
                calculateTotalStockAggregated();
            }
        });

        if (generalBarcode) {
            generalBarcode.addEventListener('input', syncBarcodeValues);
        }

        tipoProducto.addEventListener('change', syncUI);
        manejaTallas.addEventListener('change', syncUI);

        syncUI();
        calculateTotalStockAggregated();
    })();
</script>
@endpush