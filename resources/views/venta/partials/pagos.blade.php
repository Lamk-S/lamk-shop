@php
    $clienteGenerico = $clienteGenerico ?? null;
    $defaultClienteId = old('cliente_id', optional($clienteGenerico)->id);
    $defaultComprobanteId = old('comprobante_id', optional($comprobantes->firstWhere('tipo_comprobante', 'TICKET'))->id);
    $oldPagos = old('pagos', []);
@endphp

<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
        <i class="fa-solid fa-file-invoice text-info fs-5 me-2"></i>
        <h5 class="mb-0 fw-semibold text-dark">Datos Generales</h5>
    </div>

    <div class="card-body p-4">
        <div id="venta_form_alert" class="alert alert-danger d-none mb-3"></div>

        <div class="row g-4">
            <div class="col-12">
                <label for="cliente_id" class="form-label fw-medium text-secondary small">
                    Cliente <span class="text-muted">(opcional para boleta rápida)</span>
                </label>

                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                    <small class="help-text-soft" id="cliente_help">
                        Boleta rápida: puedes dejar el cliente en Consumidor final / Cliente varios.
                    </small>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickClienteModal">
                        <i class="fa-solid fa-user-plus me-1"></i>Nuevo
                    </button>
                </div>

                <select
                    name="cliente_id"
                    id="cliente_id"
                    class="form-control selectpicker show-tick border shadow-sm"
                    data-live-search="true"
                    title="Consumidor final / Cliente varios"
                    data-size="6"
                >
                    <option value="" @selected($defaultClienteId === null || $defaultClienteId === '')>
                        Consumidor final / Cliente varios
                    </option>

                    @if($clienteGenerico)
                        <option value="{{ $clienteGenerico->id }}" @selected((string) $defaultClienteId === (string) $clienteGenerico->id)>
                            CONSUMIDOR FINAL — DNI 00000000
                        </option>
                    @endif

                    @foreach ($clientes as $item)
                        <option
                            value="{{ $item->id }}"
                            data-tipo-persona="{{ $item->persona?->tipo_persona }}"
                            data-doc-codigo="{{ strtoupper((string) ($item->persona?->documento?->codigo ?? '')) }}"
                            data-doc-numero="{{ $item->persona?->numero_documento }}"
                            @selected((string) old('cliente_id') === (string) $item->id)
                        >
                            {{ $item->persona?->nombre_completo ?? $item->persona?->razon_social ?? 'Cliente' }}
                            — {{ $item->persona?->documento?->codigo ?? 'DOC' }} {{ $item->persona?->numero_documento ?? '' }}
                        </option>
                    @endforeach
                </select>

                @error('cliente_id')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-12">
                <label for="comprobante_id" class="form-label fw-medium text-secondary small">
                    Comprobante <span class="text-muted">(ticket / boleta / factura)</span>
                </label>

                <select
                    name="comprobante_id"
                    id="comprobante_id"
                    class="form-control selectpicker show-tick border shadow-sm"
                    data-live-search="true"
                    title="Seleccione comprobante"
                    data-size="6"
                >
                    <option value="">Usar ticket por defecto / boleta rápida</option>
                    @foreach ($comprobantes as $item)
                        @php
                            $nextCorrelativo = str_pad((string) ((int) ($item->correlativo_actual ?? 0) + 1), 8, '0', STR_PAD_LEFT);
                        @endphp
                        <option
                            value="{{ $item->id }}"
                            data-tipo="{{ $item->tipo_comprobante }}"
                            data-serie="{{ $item->serie }}"
                            data-correlativo-actual="{{ $nextCorrelativo }}"
                            @selected((string) old('comprobante_id', $defaultComprobanteId) === (string) $item->id)
                        >
                            {{ $item->tipo_comprobante }} - {{ $item->serie }} (N° {{ $nextCorrelativo }})
                        </option>
                    @endforeach
                </select>

                @error('comprobante_id')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror

                <small class="help-text-soft d-block mt-2" id="comprobante_help">
                    Factura: el cliente debe estar identificado con RUC.
                </small>
            </div>

            <div class="col-12">
                <div class="alert alert-light border mb-0 py-2 small" id="cliente_resumen">
                    Se mostrará aquí el cliente seleccionado.
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-medium text-secondary small">N° Comprobante</label>
                <input
                    type="text"
                    id="numero_comprobante_preview"
                    class="form-control bg-light text-muted"
                    value="Se generará automáticamente al guardar"
                    readonly
                >
                <small class="text-muted">El número final se asignará según la serie y el correlativo automático.</small>
            </div>

            <div class="col-12">
                <label class="form-label fw-medium text-secondary small">Fecha Emisión</label>
                <input readonly type="text" name="fecha" id="fecha" class="form-control bg-light text-muted" value="{{ date('d-m-Y') }}">
                <input type="hidden" name="fecha_emision" value="{{ old('fecha_emision', now()->toDateTimeString()) }}">
            </div>

            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnModoSimple">
                        <i class="fa-solid fa-credit-card me-1"></i>Modo simple
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" id="btnModoMixto">
                        <i class="fa-solid fa-layer-group me-1"></i>Modo mixto
                    </button>
                </div>
                <small class="help-text-soft d-block mt-2">
                    El modo simple usa un solo método de pago. El modo mixto permite varios pagos en una misma venta.
                </small>
            </div>

            <div class="col-12">
                <input type="hidden" id="venta_payment_mode" value="SIMPLE">
            </div>

            <div class="col-12">
                <div id="simplePaymentBlock" class="row g-3">
                    <div class="col-12">
                        <label for="metodo_pago" class="form-label fw-medium text-secondary small">
                            Método de pago <span class="text-danger">*</span>
                        </label>
                        <select name="metodo_pago" id="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror">
                            <option value="EFECTIVO" @selected(old('metodo_pago', 'EFECTIVO') === 'EFECTIVO')>EFECTIVO</option>
                            <option value="TARJETA" @selected(old('metodo_pago') === 'TARJETA')>TARJETA</option>
                            <option value="TRANSFERENCIA" @selected(old('metodo_pago') === 'TRANSFERENCIA')>TRANSFERENCIA</option>
                            <option value="YAPE" @selected(old('metodo_pago') === 'YAPE')>YAPE</option>
                            <option value="PLIN" @selected(old('metodo_pago') === 'PLIN')>PLIN</option>
                            <option value="OTRO" @selected(old('metodo_pago') === 'OTRO')>OTRO</option>
                        </select>
                        @error('metodo_pago')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="monto_recibido" class="form-label fw-medium text-secondary small">
                            Monto recibido <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input
                                type="number"
                                name="monto_recibido"
                                id="monto_recibido"
                                class="form-control border-start-0 text-end"
                                value="{{ old('monto_recibido', 0) }}"
                                min="0"
                                step="0.01"
                            >
                        </div>
                        @error('monto_recibido')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="vuelto_entregado" class="form-label fw-medium text-secondary small">Vuelto entregado</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input
                                disabled
                                type="number"
                                name="vuelto_entregado"
                                id="vuelto_entregado"
                                class="form-control border-start-0 bg-light text-end fw-bold"
                                value="{{ old('vuelto_entregado', 0) }}"
                                step="0.01"
                            >
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="referencia_operacion" class="form-label fw-medium text-secondary small">Referencia de operación</label>
                        <input type="text" name="referencia_operacion" id="referencia_operacion" class="form-control @error('referencia_operacion') is-invalid @enderror" value="{{ old('referencia_operacion') }}">
                        @error('referencia_operacion')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div id="mixedPaymentBlock" class="d-none">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom border-light p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-semibold text-dark">Pagos mixtos</h6>
                                <small class="text-muted">Agrega uno o varios pagos hasta completar el total.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAgregarPago">
                                <i class="fa-solid fa-plus me-1"></i>Agregar pago
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0" id="tabla_pagos">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Método</th>
                                            <th class="text-end">Monto</th>
                                            <th>Referencia</th>
                                            <th class="text-center" style="width: 60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th class="text-end">Total pagos</th>
                                            <th class="text-end" id="pagos_total_display">S/ 0.00</th>
                                            <th class="text-end">Pendiente</th>
                                            <th class="text-end" id="pagos_pendiente_display">S/ 0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @error('pagos')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>
    </div>

    {{-- Aquí están tus botones integrados en el pie de la tarjeta --}}
    <div class="card-footer bg-white border-top border-light p-4 text-center">
        <button id="cancelar" type="button" class="btn btn-light w-100 mb-2 py-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
            <i class="fas fa-times me-2"></i>Cancelar Venta
        </button>

        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-6 shadow-sm" id="guardar">
            <i class="fas fa-check-circle me-2"></i>Procesar Venta
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formVenta = document.getElementById('formVenta');
        const clienteSelect = document.getElementById('cliente_id');
        const comprobanteSelect = document.getElementById('comprobante_id');
        const clienteHelp = document.getElementById('cliente_help');
        const clienteResumen = document.getElementById('cliente_resumen');
        const comprobanteHelp = document.getElementById('comprobante_help');
        const preview = document.getElementById('numero_comprobante_preview');
        const formAlert = document.getElementById('venta_form_alert');

        const paymentMode = document.getElementById('venta_payment_mode');
        const simplePaymentBlock = document.getElementById('simplePaymentBlock');
        const mixedPaymentBlock = document.getElementById('mixedPaymentBlock');
        const btnModoSimple = document.getElementById('btnModoSimple');
        const btnModoMixto = document.getElementById('btnModoMixto');
        const btnAgregarPago = document.getElementById('btnAgregarPago');
        const pagosTbody = document.querySelector('#tabla_pagos tbody');
        const pagosTotalDisplay = document.getElementById('pagos_total_display');
        const pagosPendienteDisplay = document.getElementById('pagos_pendiente_display');
        const metodoPagoSimple = document.getElementById('metodo_pago');
        const montoRecibido = document.getElementById('monto_recibido');
        const vueltoEntregado = document.getElementById('vuelto_entregado');
        const referenciaOperacion = document.getElementById('referencia_operacion');

        let paymentRows = @json($oldPagos, JSON_UNESCAPED_UNICODE);

        function showAlert(message) {
            formAlert.textContent = message;
            formAlert.classList.remove('d-none');
        }

        function hideAlert() {
            formAlert.textContent = '';
            formAlert.classList.add('d-none');
        }

        function round(num, decimales = 2) {
            return Number(parseFloat(num).toFixed(decimales));
        }

        function selectedComprobante() {
            return comprobanteSelect.options[comprobanteSelect.selectedIndex];
        }

        function selectedCliente() {
            return clienteSelect.options[clienteSelect.selectedIndex];
        }

        function updatePreview() {
            const selected = selectedComprobante();
            if (!selected || !selected.value) {
                preview.value = 'Se generará automáticamente al guardar';
                return;
            }
            preview.value = selected.textContent.trim();
        }

        function updateClienteSummary() {
            const selected = selectedCliente();

            if (!selected || !selected.value) {
                clienteHelp.textContent = 'Boleta rápida: puedes dejar el cliente en Consumidor final / Cliente varios.';
                clienteResumen.textContent = 'Operación rápida: se emitirá al cliente genérico del sistema.';
                return;
            }

            const tipo = (selected.dataset.tipoPersona || '').toString().toLowerCase();
            const doc = (selected.dataset.docCodigo || '').toString().toUpperCase();
            clienteResumen.textContent = selected.textContent.trim();

            if (tipo === 'juridica' && doc === 'RUC') {
                clienteHelp.textContent = 'Cliente jurídico detectado: la factura es la opción correcta.';
            } else if (tipo === 'natural') {
                clienteHelp.textContent = 'Cliente natural detectado: boleta o ticket, según la operación.';
            } else {
                clienteHelp.textContent = 'Cliente seleccionado.';
            }
        }

        function validateFacturaClient() {
            const comp = selectedComprobante();
            const cli = selectedCliente();
            const tipoComprobante = (comp?.dataset.tipo || '').toString().toUpperCase();
            const tipoPersona = (cli?.dataset.tipoPersona || '').toString().toLowerCase();
            const docCodigo = (cli?.dataset.docCodigo || '').toString().toUpperCase();

            if (tipoComprobante === 'FACTURA') {
                if (!cli || !cli.value) {
                    showAlert('La factura requiere un cliente identificado con RUC.');
                    return false;
                }
                if (!(tipoPersona === 'juridica' && docCodigo === 'RUC')) {
                    showAlert('La factura requiere un cliente jurídico con documento RUC. Selecciona otro comprobante o cambia de cliente.');
                    return false;
                }
            }

            hideAlert();
            return true;
        }

        function updateSimpleChange() {
            const total = Number(document.getElementById('inputTotal').value) || 0;
            const recibido = Number(montoRecibido.value) || 0;
            const vuelto = round(Math.max(0, recibido - total));
            vueltoEntregado.value = vuelto.toFixed(2);
        }

        function updateMixedTotals() {
            const totalVenta = Number(document.getElementById('inputTotal').value) || 0;
            const totalPagos = paymentRows.reduce((acc, row) => acc + (Number(row.monto) || 0), 0);
            const pendiente = round(Math.max(0, totalVenta - totalPagos));

            pagosTotalDisplay.textContent = `S/ ${round(totalPagos).toFixed(2)}`;
            pagosPendienteDisplay.textContent = `S/ ${pendiente.toFixed(2)}`;
        }

        function renderPaymentRows() {
            pagosTbody.innerHTML = '';

            paymentRows.forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <select class="form-select form-select-sm payment-method">
                            <option value="EFECTIVO" ${String(row.metodo_pago || '').toUpperCase() === 'EFECTIVO' ? 'selected' : ''}>EFECTIVO</option>
                            <option value="TARJETA" ${String(row.metodo_pago || '').toUpperCase() === 'TARJETA' ? 'selected' : ''}>TARJETA</option>
                            <option value="TRANSFERENCIA" ${String(row.metodo_pago || '').toUpperCase() === 'TRANSFERENCIA' ? 'selected' : ''}>TRANSFERENCIA</option>
                            <option value="YAPE" ${String(row.metodo_pago || '').toUpperCase() === 'YAPE' ? 'selected' : ''}>YAPE</option>
                            <option value="PLIN" ${String(row.metodo_pago || '').toUpperCase() === 'PLIN' ? 'selected' : ''}>PLIN</option>
                            <option value="OTRO" ${String(row.metodo_pago || '').toUpperCase() === 'OTRO' ? 'selected' : ''}>OTRO</option>
                        </select>
                        <input type="hidden" name="pagos[${index}][metodo_pago]" class="payment-method-hidden" value="${row.metodo_pago || 'EFECTIVO'}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-end payment-amount" value="${row.monto ?? ''}" placeholder="0.00">
                        <input type="hidden" name="pagos[${index}][monto]" class="payment-amount-hidden" value="${row.monto ?? ''}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm payment-reference" value="${row.referencia_operacion ?? ''}" maxlength="100" placeholder="Referencia">
                        <input type="hidden" name="pagos[${index}][referencia_operacion]" class="payment-reference-hidden" value="${row.referencia_operacion ?? ''}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-payment border-0">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;

                pagosTbody.appendChild(tr);

                const methodSelect = tr.querySelector('.payment-method');
                const amountInput = tr.querySelector('.payment-amount');
                const referenceInput = tr.querySelector('.payment-reference');
                const removeButton = tr.querySelector('.btn-remove-payment');

                methodSelect.addEventListener('change', function () {
                    tr.querySelector('.payment-method-hidden').value = this.value;
                    paymentRows[index].metodo_pago = this.value;
                });

                amountInput.addEventListener('input', function () {
                    tr.querySelector('.payment-amount-hidden').value = this.value;
                    paymentRows[index].monto = this.value;
                    updateMixedTotals();
                });

                referenceInput.addEventListener('input', function () {
                    tr.querySelector('.payment-reference-hidden').value = this.value;
                    paymentRows[index].referencia_operacion = this.value;
                });

                removeButton.addEventListener('click', function () {
                    paymentRows.splice(index, 1);
                    renderPaymentRows();
                });
            });

            updateMixedTotals();
        }

        function addPaymentRow() {
            const totalVenta = Number(document.getElementById('inputTotal').value) || 0;
            const totalPagos = paymentRows.reduce((acc, row) => acc + (Number(row.monto) || 0), 0);
            const pendiente = round(Math.max(0, totalVenta - totalPagos));

            paymentRows.push({
                metodo_pago: 'EFECTIVO',
                monto: pendiente > 0 ? pendiente.toFixed(2) : '',
                referencia_operacion: ''
            });

            renderPaymentRows();
        }

        function validateMixedPayments() {
            const totalVenta = Number(document.getElementById('inputTotal').value) || 0;
            const totalPagos = paymentRows.reduce((acc, row) => acc + (Number(row.monto) || 0), 0);

            if (paymentRows.length === 0) {
                showAlert('Debes registrar al menos un pago en el modo mixto.');
                return false;
            }

            if (totalPagos <= 0) {
                showAlert('El monto total de los pagos añadidos debe ser mayor a cero.');
                return false;
            }

            if (Math.abs(round(totalPagos) - round(totalVenta)) > 0.01) {
                showAlert(`La suma de pagos (S/ ${totalPagos.toFixed(2)}) no coincide con el total de la venta (S/ ${totalVenta.toFixed(2)}).`);
                return false;
            }

            return true;
        }

        function togglePaymentMode(mode) {
            const normalized = (mode || 'SIMPLE').toUpperCase();
            paymentMode.value = normalized;

            if (normalized === 'MIXTO') {
                simplePaymentBlock.classList.add('d-none');
                mixedPaymentBlock.classList.remove('d-none');

                montoRecibido.value = '0.00';
                referenciaOperacion.value = '';

                if (paymentRows.length === 0) {
                    const totalVenta = Number(document.getElementById('inputTotal').value) || 0;
                    paymentRows.push({
                        metodo_pago: 'EFECTIVO',
                        monto: totalVenta > 0 ? totalVenta.toFixed(2) : '',
                        referencia_operacion: ''
                    });
                }

                renderPaymentRows();
            } else {
                mixedPaymentBlock.classList.add('d-none');
                simplePaymentBlock.classList.remove('d-none');

                const totalVenta = Number(document.getElementById('inputTotal').value) || 0;
                montoRecibido.value = totalVenta.toFixed(2);
                updateSimpleChange();
            }
        }

        window.syncPaymentTotals = function () {
            if (paymentMode.value === 'MIXTO') {
                updateMixedTotals();
            } else {
                const totalVenta = Number(document.getElementById('inputTotal').value) || 0;
                montoRecibido.value = totalVenta.toFixed(2);
                updateSimpleChange();
            }
        };

        window.validateVentaClient = validateFacturaClient;

        // NUEVA VALIDACIÓN COMPLETA DE PAGOS
        window.validateVentaPayments = function () {
            const totalVenta = Number(document.getElementById('inputTotal').value) || 0;

            if (paymentMode.value === 'MIXTO') {
                return validateMixedPayments();
            } else {
                const metodo = document.getElementById('metodo_pago').value;
                const recibido = Number(document.getElementById('monto_recibido').value) || 0;

                if (metodo === 'EFECTIVO' && recibido < totalVenta) {
                    showAlert(`El monto recibido (S/ ${recibido.toFixed(2)}) no puede ser menor al total de la venta (S/ ${totalVenta.toFixed(2)}).`);
                    return false;
                }
                
                if (metodo !== 'EFECTIVO' && Math.abs(recibido - totalVenta) > 0.01) {
                    showAlert(`Para pagos con ${metodo}, el monto recibido (S/ ${recibido.toFixed(2)}) debe ser igual al total exacto.`);
                    return false;
                }
            }
            hideAlert();
            return true;
        };

        clienteSelect.addEventListener('change', function () {
            updateClienteSummary();
            validateFacturaClient();
        });

        comprobanteSelect.addEventListener('change', function () {
            const selected = selectedComprobante();

            if (!selected || !selected.value) {
                comprobanteHelp.textContent = 'Factura requiere cliente con RUC. Boleta rápida puede usar cliente genérico.';
                updatePreview();
                hideAlert();
                return;
            }

            const tipo = (selected.dataset.tipo || '').toString().toUpperCase();
            if (tipo === 'FACTURA') {
                comprobanteHelp.textContent = 'Factura seleccionada: el cliente debe estar identificado con RUC.';
            } else if (tipo === 'BOLETA') {
                comprobanteHelp.textContent = 'Boleta seleccionada: puedes usar cliente registrado o consumidor final.';
            } else if (tipo === 'TICKET') {
                comprobanteHelp.textContent = 'Ticket seleccionado: operación rápida en mostrador.';
            } else {
                comprobanteHelp.textContent = 'Documento seleccionado.';
            }

            updatePreview();
            validateFacturaClient();
        });

        btnModoSimple.addEventListener('click', function () {
            togglePaymentMode('SIMPLE');
        });

        btnModoMixto.addEventListener('click', function () {
            togglePaymentMode('MIXTO');
        });

        btnAgregarPago.addEventListener('click', addPaymentRow);
        montoRecibido.addEventListener('input', updateSimpleChange);

        metodoPagoSimple.addEventListener('change', function () {
            if (paymentMode.value === 'SIMPLE' && this.value === 'EFECTIVO') {
                updateSimpleChange();
            }
        });

        formVenta.addEventListener('submit', function (e) {
            if (!validateFacturaClient()) {
                e.preventDefault();
                return false;
            }

            if (paymentMode.value === 'MIXTO') {
                if (!validateMixedPayments()) {
                    e.preventDefault();
                    return false;
                }
                
                // Deshabilitamos inputs simples
                montoRecibido.disabled = true;
                referenciaOperacion.disabled = true;
                metodoPagoSimple.disabled = true; 

                // Solo inyectamos el metodo mixto, JAMÁS inyectar monto_recibido aquí
                const hiddenMetodo = document.createElement('input');
                hiddenMetodo.type = 'hidden';
                hiddenMetodo.name = 'metodo_pago';
                hiddenMetodo.value = 'MIXTO';
                formVenta.appendChild(hiddenMetodo);

            } else {
                // Modo simple: limpiamos los pagos mixtos
                pagosTbody.innerHTML = '';
                const inputsOcultosMixtos = mixedPaymentBlock.querySelectorAll('input, select');
                inputsOcultosMixtos.forEach(input => input.disabled = true);
                
                montoRecibido.disabled = false;
                referenciaOperacion.disabled = false;
            }
        });

        if (Array.isArray(paymentRows) && paymentRows.length > 0) {
            togglePaymentMode('MIXTO');
        } else {
            togglePaymentMode('SIMPLE');
        }

        updateClienteSummary();
        updatePreview();
        validateFacturaClient();
    });
</script>