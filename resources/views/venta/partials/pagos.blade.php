@php
    $clienteGenerico = $clienteGenerico ?? null;
    $defaultClienteId = old('cliente_id', optional($clienteGenerico)->id);
    $defaultComprobanteId = old('comprobante_id', optional($comprobantes->firstWhere('tipo_comprobante', 'TICKET'))->id);
    $oldPagos = old('pagos', []);
@endphp

<div class="card border-0 shadow-sm rounded-4 d-flex flex-column">
    <div class="card-header bg-white border-bottom border-light p-3 p-md-4 d-flex align-items-center">
        <i class="fa-solid fa-file-invoice text-primary fs-5 me-2"></i>
        <h5 class="mb-0 fw-bold text-dark">Datos de Pago</h5>
    </div>

    <div class="card-body p-3 p-md-4 flex-grow-1">
        <div id="venta_form_alert" class="alert alert-danger d-none mb-4 fs-7 shadow-sm border-0"></div>

        <div class="row g-4">
            <!-- Sección Cliente -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="cliente_id" class="form-label fw-bold text-dark mb-0 fs-7">Cliente</label>
                    <button type="button" class="btn btn-sm btn-light text-primary fw-medium border-0 px-2 py-1" data-bs-toggle="modal" data-bs-target="#quickClienteModal">
                        <i class="fa-solid fa-plus me-1"></i>Nuevo
                    </button>
                </div>
                <select name="cliente_id" id="cliente_id" class="selectpicker" data-width="100%" data-style="btn-white border shadow-sm" data-live-search="true" title="Consumidor final / Cliente varios" data-size="7">
                    <option value="" @selected($defaultClienteId === null || $defaultClienteId === '')>Consumidor final / Cliente varios</option>
                    @if($clienteGenerico)
                        <option value="{{ $clienteGenerico->id }}" @selected((string) $defaultClienteId === (string) $clienteGenerico->id)>
                            CONSUMIDOR FINAL — DNI 00000000
                        </option>
                    @endif
                    @foreach ($clientes as $item)
                        <option value="{{ $item->id }}" data-tipo-persona="{{ $item->persona?->tipo_persona }}" data-doc-codigo="{{ strtoupper((string) ($item->persona?->documento?->codigo ?? '')) }}" data-doc-numero="{{ $item->persona?->numero_documento }}" @selected((string) old('cliente_id') === (string) $item->id)>
                            {{ $item->persona?->nombre_completo ?? $item->persona?->razon_social ?? 'Cliente' }} — {{ $item->persona?->documento?->codigo ?? 'DOC' }} {{ $item->persona?->numero_documento ?? '' }}
                        </option>
                    @endforeach
                </select>
                <div class="mt-2 text-muted fs-7 bg-light rounded px-2 py-1" id="cliente_resumen">
                    Operación rápida: Consumidor final.
                </div>
            </div>

            <!-- Sección Comprobante -->
            <div class="col-12">
                <label for="comprobante_id" class="form-label fw-bold text-dark fs-7">Comprobante</label>
                <select name="comprobante_id" id="comprobante_id" class="selectpicker" data-width="100%" data-style="btn-white border shadow-sm" data-live-search="true">
                    <option value="">Ticket por defecto</option>
                    @foreach ($comprobantes as $item)
                        @php $nextCorrelativo = str_pad((string) ((int) ($item->correlativo_actual ?? 0) + 1), 8, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $item->id }}" data-tipo="{{ $item->tipo_comprobante }}" @selected((string) old('comprobante_id', $defaultComprobanteId) === (string) $item->id)>
                            {{ $item->tipo_comprobante }} - {{ $item->serie }} (N° {{ $nextCorrelativo }})
                        </option>
                    @endforeach
                </select>
                <small class="text-danger d-block mt-1 d-none fs-7" id="comprobante_help">Factura requiere RUC.</small>
            </div>

            <div class="col-12">
                <input type="hidden" name="fecha_emision" value="{{ old('fecha_emision', now()->toDateTimeString()) }}">
                <div class="d-grid gap-2 d-sm-flex">
                    <button type="button" class="btn btn-primary btn-sm flex-sm-grow-1" id="btnModoSimple">
                        <i class="fa-solid fa-wallet me-1"></i>Pago Simple
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-sm-grow-1" id="btnModoMixto">
                        <i class="fa-solid fa-layer-group me-1"></i>Pago Mixto
                    </button>
                </div>
            </div>

            <div class="col-12">
                <input type="hidden" id="venta_payment_mode" value="SIMPLE">
                <input type="hidden" name="metodo_pago" id="real_metodo_pago" value="{{ old('metodo_pago', 'EFECTIVO') }}">
            </div>

            <!-- PAGO SIMPLE -->
            <div class="col-12" id="simplePaymentBlock">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-bold text-dark fs-7">Método</label>
                        <select id="metodo_pago" class="form-select text-dark shadow-none border">
                            <option value="EFECTIVO" @selected(old('metodo_pago', 'EFECTIVO') === 'EFECTIVO')>EFECTIVO</option>
                            <option value="TARJETA" @selected(old('metodo_pago') === 'TARJETA')>TARJETA</option>
                            <option value="TRANSFERENCIA" @selected(old('metodo_pago') === 'TRANSFERENCIA')>TRANSFERENCIA</option>
                            <option value="YAPE" @selected(old('metodo_pago') === 'YAPE')>YAPE</option>
                            <option value="PLIN" @selected(old('metodo_pago') === 'PLIN')>PLIN</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-bold text-dark fs-7">Recibido (S/)</label>
                        <input type="number" id="monto_recibido" name="monto_recibido" class="form-control font-monospace fw-bold text-primary shadow-none border" value="{{ old('monto_recibido', 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-bold text-dark fs-7">Vuelto (S/)</label>
                        <input type="text" id="vuelto_entregado" class="form-control font-monospace bg-light border-0" value="0.00" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-bold text-dark fs-7">Referencia (Opcional)</label>
                        <input type="text" id="referencia_operacion" name="referencia_operacion" class="form-control shadow-none border">
                    </div>
                </div>
            </div>

            <!-- PAGO MIXTO -->
            <div class="col-12 d-none" id="mixedPaymentBlock">
                <div class="border rounded-4 overflow-hidden">
                    <div class="bg-light border-bottom p-2 p-sm-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-7">Desglose de pagos</span>
                        <button type="button" class="btn btn-sm btn-dark px-3 py-1 rounded-pill" id="btnAgregarPago">
                            <i class="fa-solid fa-plus me-1"></i>Añadir
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm align-middle mb-0" id="tabla_pagos" style="min-width: 400px;">
                            <tbody class="border-bottom"></tbody>
                            <tfoot class="bg-white">
                                <tr>
                                    <td class="fs-7 fw-bold text-end pt-2">Abonado:</td>
                                    <td class="fs-7 font-monospace text-success pt-2 text-end" id="pagos_total_display">S/ 0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="fs-7 fw-bold text-end pb-2">Falta:</td>
                                    <td class="fs-7 font-monospace text-danger pb-2 text-end" id="pagos_pendiente_display">S/ 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white border-top border-light p-3 p-md-4 mt-auto">
        <div class="row g-2">
            <div class="col-12 col-sm-4">
                <button type="button" class="btn btn-light w-100 py-2 fw-medium border shadow-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    Cancelar
                </button>
            </div>
            <div class="col-12 col-sm-8">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" id="guardar">
                    <i class="fas fa-check me-1"></i>Procesar Venta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const DOM = {
            form: document.getElementById('formVenta'),
            cliente: document.getElementById('cliente_id'),
            comprobante: document.getElementById('comprobante_id'),
            clienteResumen: document.getElementById('cliente_resumen'),
            compHelp: document.getElementById('comprobante_help'),
            alert: document.getElementById('venta_form_alert'),
            payMode: document.getElementById('venta_payment_mode'),
            realMethod: document.getElementById('real_metodo_pago'),
            simpleBlock: document.getElementById('simplePaymentBlock'),
            mixedBlock: document.getElementById('mixedPaymentBlock'),
            btnSimple: document.getElementById('btnModoSimple'),
            btnMixto: document.getElementById('btnModoMixto'),
            btnAgregarPago: document.getElementById('btnAgregarPago'),
            pagosTbody: document.querySelector('#tabla_pagos tbody'),
            totalDisplay: document.getElementById('pagos_total_display'),
            pendienteDisplay: document.getElementById('pagos_pendiente_display'),
            simpleMethod: document.getElementById('metodo_pago'),
            montoRecibido: document.getElementById('monto_recibido'),
            vueltoEntregado: document.getElementById('vuelto_entregado'),
            referencia: document.getElementById('referencia_operacion'),
            totalInput: document.getElementById('inputTotal')
        };

        let paymentRows = @json($oldPagos, JSON_UNESCAPED_UNICODE);

        const round = num => Number(parseFloat(num).toFixed(2));
        const showAlert = msg => { DOM.alert.textContent = msg; DOM.alert.classList.remove('d-none'); };
        const hideAlert = () => { DOM.alert.classList.add('d-none'); };

        function syncFormState() {
            const isMixto = DOM.payMode.value === 'MIXTO';
            DOM.realMethod.value = isMixto ? 'MIXTO' : DOM.simpleMethod.value;
            DOM.simpleMethod.removeAttribute('name');
            DOM.montoRecibido.disabled = isMixto;
            DOM.referencia.disabled = isMixto;
            
            document.querySelectorAll('.mixed-input').forEach(el => el.disabled = !isMixto);
        }

        function updateClienteSummary() {
            const opt = DOM.cliente.options[DOM.cliente.selectedIndex];
            if (!opt.value) {
                DOM.clienteResumen.textContent = 'Consumidor final.';
                return;
            }
            DOM.clienteResumen.textContent = opt.textContent.trim();
        }

        function validateFacturaClient() {
            const comp = DOM.comprobante.options[DOM.comprobante.selectedIndex];
            const cli = DOM.cliente.options[DOM.cliente.selectedIndex];
            
            const isFactura = comp?.dataset.tipo?.toUpperCase() === 'FACTURA';
            const isRUC = cli?.dataset.docCodigo?.toUpperCase() === 'RUC';

            if (isFactura && !isRUC) {
                DOM.compHelp.classList.remove('d-none');
                showAlert('Para emitir FACTURA, selecciona un cliente jurídico con RUC.');
                return false;
            }
            DOM.compHelp.classList.add('d-none');
            hideAlert();
            return true;
        }

        function updateSimpleChange() {
            const total = Number(DOM.totalInput.value) || 0;
            const recibido = Number(DOM.montoRecibido.value) || 0;
            DOM.vueltoEntregado.value = Math.max(0, recibido - total).toFixed(2);
        }

        function updateMixedTotals() {
            const total = round(DOM.totalInput.value) || 0;
            const pagado = round(paymentRows.reduce((sum, r) => sum + (Number(r.monto) || 0), 0));
            const pendiente = Math.max(0, total - pagado);
            
            DOM.totalDisplay.textContent = `S/ ${pagado.toFixed(2)}`;
            DOM.pendienteDisplay.textContent = `S/ ${pendiente.toFixed(2)}`;
        }

        function renderPaymentRows() {
            DOM.pagosTbody.innerHTML = paymentRows.map((row, i) => `
                <tr class="px-2">
                    <td class="p-1">
                        <select class="form-select form-select-sm shadow-none mixed-method border-light">
                            ${['EFECTIVO','TARJETA','TRANSFERENCIA','YAPE','PLIN'].map(m => 
                                `<option value="${m}" ${row.metodo_pago === m ? 'selected' : ''}>${m}</option>`
                            ).join('')}
                        </select>
                        <input type="hidden" name="pagos[${i}][metodo_pago]" class="mixed-input hidden-method" value="${row.metodo_pago || 'EFECTIVO'}">
                    </td>
                    <td class="p-1" style="width: 100px;">
                        <input type="number" step="0.01" class="form-control form-control-sm text-end shadow-none border-light mixed-amount" value="${row.monto ?? ''}" placeholder="0.00">
                        <input type="hidden" name="pagos[${i}][monto]" class="mixed-input hidden-amount" value="${row.monto ?? ''}">
                    </td>
                    <td class="p-1 text-center" style="width: 40px;">
                        <button type="button" class="btn btn-sm text-danger btn-remove-payment p-0">
                            <i class="fa-solid fa-circle-xmark fs-5"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            updateMixedTotals();
            syncFormState();
        }

        DOM.pagosTbody.addEventListener('input', e => {
            const tr = e.target.closest('tr');
            if (!tr) return;
            const index = Array.from(DOM.pagosTbody.children).indexOf(tr);

            if (e.target.classList.contains('mixed-amount')) {
                tr.querySelector('.hidden-amount').value = e.target.value;
                paymentRows[index].monto = e.target.value;
                updateMixedTotals();
            }
        });

        DOM.pagosTbody.addEventListener('change', e => {
            if (e.target.classList.contains('mixed-method')) {
                const tr = e.target.closest('tr');
                const index = Array.from(DOM.pagosTbody.children).indexOf(tr);
                tr.querySelector('.hidden-method').value = e.target.value;
                paymentRows[index].metodo_pago = e.target.value;
            }
        });

        DOM.pagosTbody.addEventListener('click', e => {
            const btn = e.target.closest('.btn-remove-payment');
            if (btn) {
                const index = Array.from(DOM.pagosTbody.children).indexOf(btn.closest('tr'));
                paymentRows.splice(index, 1);
                renderPaymentRows();
            }
        });

        function togglePaymentMode(mode) {
            DOM.payMode.value = mode;
            if (mode === 'MIXTO') {
                DOM.btnMixto.classList.replace('btn-outline-secondary', 'btn-primary');
                DOM.btnSimple.classList.replace('btn-primary', 'btn-outline-secondary');
                DOM.simpleBlock.classList.add('d-none');
                DOM.mixedBlock.classList.remove('d-none');
                
                if (paymentRows.length === 0) {
                    paymentRows.push({ metodo_pago: 'EFECTIVO', monto: DOM.totalInput.value || '' });
                }
                renderPaymentRows();
            } else {
                DOM.btnSimple.classList.replace('btn-outline-secondary', 'btn-primary');
                DOM.btnMixto.classList.replace('btn-primary', 'btn-outline-secondary');
                DOM.mixedBlock.classList.add('d-none');
                DOM.simpleBlock.classList.remove('d-none');
                
                DOM.montoRecibido.value = DOM.totalInput.value || 0;
                updateSimpleChange();
                syncFormState();
            }
        }

        DOM.cliente.addEventListener('change', () => { updateClienteSummary(); validateFacturaClient(); });
        DOM.comprobante.addEventListener('change', validateFacturaClient);
        DOM.btnSimple.addEventListener('click', () => togglePaymentMode('SIMPLE'));
        DOM.btnMixto.addEventListener('click', () => togglePaymentMode('MIXTO'));
        DOM.montoRecibido.addEventListener('input', updateSimpleChange);
        DOM.simpleMethod.addEventListener('change', () => { if(DOM.simpleMethod.value === 'EFECTIVO') updateSimpleChange(); });
        
        DOM.btnAgregarPago.addEventListener('click', () => {
            const total = Number(DOM.totalInput.value) || 0;
            const pagado = paymentRows.reduce((s, r) => s + Number(r.monto||0), 0);
            const pendiente = Math.max(0, total - pagado);
            paymentRows.push({ metodo_pago: 'EFECTIVO', monto: pendiente > 0 ? pendiente.toFixed(2) : '' });
            renderPaymentRows();
        });

        window.syncPaymentTotals = function () {
            if (DOM.payMode.value === 'MIXTO') {
                updateMixedTotals();
            } else {
                const recibido = Number(DOM.montoRecibido.value) || 0;
                const total = Number(DOM.totalInput.value) || 0;
                
                if (recibido < total) {
                    DOM.montoRecibido.value = total.toFixed(2);
                }
                updateSimpleChange();
            }
        };

        DOM.form.addEventListener('submit', function (e) {
            syncFormState();
            if (!validateFacturaClient()) {
                e.preventDefault(); return;
            }
            if (DOM.payMode.value === 'MIXTO') {
                const total = round(DOM.totalInput.value);
                const pagado = round(paymentRows.reduce((s, r) => s + Number(r.monto||0), 0));
                if (paymentRows.length === 0 || pagado <= 0) {
                    showAlert('Registra al menos un pago en modo mixto.'); e.preventDefault(); return;
                }
                if (Math.abs(pagado - total) > 0.01) {
                    showAlert(`La suma de pagos (S/ ${pagado}) no coincide con el total (S/ ${total}).`);
                    e.preventDefault(); return;
                }
            } else {
                const total = round(DOM.totalInput.value);
                const recibido = round(DOM.montoRecibido.value);
                if (DOM.simpleMethod.value === 'EFECTIVO' && recibido < total) {
                    showAlert('El efectivo recibido no puede ser menor al total.'); e.preventDefault(); return;
                }
                DOM.pagosTbody.innerHTML = '';
            }
        });

        togglePaymentMode(paymentRows.length > 0 ? 'MIXTO' : 'SIMPLE');
        updateClienteSummary();
    });
</script>