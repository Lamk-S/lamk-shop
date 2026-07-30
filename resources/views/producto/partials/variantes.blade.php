@php
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
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h5 class="mb-1 fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-boxes-packing text-primary me-2"></i>Control de Variantes y Stock Inicial
                </h5>
                <p class="text-muted mb-0 small">
                    Defina la curva física de mercadería. El sistema exige asociar al menos una variante (Talla/Color).
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm px-3 rounded-3 fw-medium shadow-sm" id="add-variant-row">
                    <i class="fas fa-plus me-1"></i>Añadir Talla
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body p-4 bg-light bg-opacity-50">
        @error('variantes')
            <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 mb-3 small d-flex align-items-center">
                <i class="fas fa-triangle-exclamation me-2"></i>{{ $message }}
            </div>
        @enderror

        <div class="table-responsive bg-white shadow-sm rounded-3 border">
            <table class="table table-hover align-middle mb-0" id="variant-table">
                <thead class="table-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 fw-bold" style="min-width: 250px;">Talla Asignada <span class="text-danger">*</span></th>
                        <th style="width: 150px;" class="text-center fw-bold">Stock Inicial</th>
                        <th style="width: 150px;" class="text-center fw-bold">Mínimo Alerta</th>
                        <th style="width: 80px;" class="text-center text-danger pe-4"><i class="fa-solid fa-trash-can"></i></th>
                    </tr>
                </thead>
                <tbody id="variant-rows">
                    @foreach($variantRows as $index => $row)
                        <tr class="variant-row">
                            <td class="ps-4">
                                <select name="variantes[{{ $index }}][talla_id]" class="form-select shadow-sm variant-talla @error("variantes.$index.talla_id") is-invalid @enderror" required>
                                    <option value="">Seleccione dimensión...</option>
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
                            </td>
                            <td>
                                <input type="number" min="0" name="variantes[{{ $index }}][stock_actual]" class="form-control shadow-sm text-center variant-stock-qty @error("variantes.$index.stock_actual") is-invalid @enderror" value="{{ old("variantes.$index.stock_actual", $row['stock_actual'] ?? 0) }}" required>
                            </td>
                            <td>
                                <input type="number" min="0" name="variantes[{{ $index }}][stock_minimo]" class="form-control shadow-sm text-center @error("variantes.$index.stock_minimo") is-invalid @enderror" value="{{ old("variantes.$index.stock_minimo", $row['stock_minimo'] ?? 0) }}" required>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-light border text-danger remove-variant-row shadow-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light border-top fw-bold text-dark">
                    <tr>
                        <td colspan="2" class="text-end py-3 text-secondary small text-uppercase">Total Inventario:</td>
                        <td class="text-center py-3 text-primary fs-6" id="total-stock-preview">0 unid.</td>
                        <td class="bg-light"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<template id="variant-row-template">
    <tr class="variant-row">
        <td class="ps-4">
            <select name="variantes[__INDEX__][talla_id]" class="form-select shadow-sm variant-talla" required>
                <option value="">Seleccione dimensión...</option>
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
            <input type="number" min="0" name="variantes[__INDEX__][stock_actual]" class="form-control shadow-sm text-center variant-stock-qty" value="0" required>
        </td>
        <td>
            <input type="number" min="0" name="variantes[__INDEX__][stock_minimo]" class="form-control shadow-sm text-center" value="0" required>
        </td>
        <td class="text-center pe-4">
            <button type="button" class="btn btn-sm btn-light border text-danger remove-variant-row shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</template>

@push('js')
<script>
    (function () {
        const tipoProducto = document.getElementById('tipo_producto');
        const manejaTallas = document.getElementById('maneja_tallas');
        const addBtn = document.getElementById('add-variant-row');
        const rowsContainer = document.getElementById('variant-rows');
        const template = document.getElementById('variant-row-template');
        const totalStockPreview = document.getElementById('total-stock-preview');
        const tallaUnicaId = @json($tallaUnicaId);
        
        const reglasTallas = {!! $reglasTallas ?? '{}' !!};

        if (!tipoProducto || !manejaTallas || !addBtn || !rowsContainer || !template) return;

        let rowIndex = rowsContainer.querySelectorAll('.variant-row').length;

        function syncUI() {
            const tipo = tipoProducto.value;
            const reglasDelTipo = reglasTallas[tipo] || [];

            if (reglasDelTipo.length > 0) {
                if (reglasDelTipo.includes('UNICA')) {
                    manejaTallas.checked = false;
                } else {
                    manejaTallas.checked = true;
                }
                manejaTallas.style.opacity = '0.65';
                manejaTallas.style.pointerEvents = 'none';
            } else {
                manejaTallas.style.opacity = '1';
                manejaTallas.style.pointerEvents = 'auto';
            }

            const isTallaUnica = !manejaTallas.checked;

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
            }
        }

        function unlockVariantSelects() {
            rowsContainer.querySelectorAll('.variant-row').forEach(row => {
                const selectTalla = row.querySelector('.variant-talla');
                if (selectTalla) {
                    selectTalla.removeAttribute('disabled');
                    if (selectTalla.value == tallaUnicaId && rowsContainer.querySelectorAll('.variant-row').length > 1) {
                        selectTalla.value = '';
                    }
                }
                removeHiddenTallaInputs(row);
            });
        }

        function removeHiddenTallaInputs(row) {
            row.querySelectorAll('.hidden-talla-id-fix').forEach(el => el.remove());
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
            totalStockPreview.textContent = `${total.toLocaleString('es-PE')} unid.`;
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

        manejaTallas.addEventListener('click', function (e) {
            const tipo = tipoProducto.value;
            const reglasDelTipo = reglasTallas[tipo] || [];
            
            if (reglasDelTipo.length > 0) {
                e.preventDefault();
            }
        });

        tipoProducto.addEventListener('change', syncUI);
        manejaTallas.addEventListener('change', syncUI);

        syncUI();
        calculateTotalStockAggregated();
    })();
</script>
@endpush