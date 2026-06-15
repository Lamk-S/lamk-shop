@php
    $comprobanteTicket = $comprobantes->firstWhere('tipo_comprobante', 'TICKET');
    $comprobanteBoleta = $comprobantes->firstWhere('tipo_comprobante', 'BOLETA');
    $comprobanteFactura = $comprobantes->firstWhere('tipo_comprobante', 'FACTURA');

    $defaultComprobanteId = old(
        'comprobante_id',
        optional($comprobanteTicket)->id
        ?? optional($comprobanteBoleta)->id
        ?? optional($comprobanteFactura)->id
    );
@endphp

<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-bottom border-light p-4">
        <h5 class="mb-0 fw-semibold text-dark">
            <i class="fa-solid fa-file-invoice text-info me-2"></i>Datos Generales
        </h5>
    </div>

    <div class="card-body p-4">
        <div id="venta_form_alert" class="alert alert-danger d-none mb-3"></div>

        <div class="row g-3">
            <div class="col-md-12">
                <label for="cliente_id" class="form-label fw-medium text-secondary small">
                    Cliente
                    <span class="text-muted">(opcional para boleta rápida)</span>
                </label>

                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                    <small class="help-text-soft" id="cliente_help">
                        Boleta rápida: puedes dejar el cliente en Consumidor final / Cliente varios.
                    </small>

                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickClienteModal">
                        <i class="fa-solid fa-user-plus me-1"></i>Nuevo
                    </button>
                </div>

                <select name="cliente_id"
                        id="cliente_id"
                        class="form-control selectpicker show-tick border shadow-sm"
                        data-live-search="true"
                        title="Consumidor final / Cliente varios"
                        data-size="6">
                    <option value="" @selected(old('cliente_id') === null || old('cliente_id') === '')>
                        Consumidor final / Cliente varios
                    </option>

                    @foreach ($clientes as $item)
                        <option value="{{ $item->id }}"
                                data-tipo-persona="{{ $item->persona?->tipo_persona }}"
                                data-doc-codigo="{{ $item->persona?->documento?->codigo }}"
                                @selected((string) old('cliente_id') === (string) $item->id)>
                            {{ $item->persona?->nombre_completo ?? $item->persona?->razon_social ?? 'Cliente' }}
                            — {{ $item->persona?->documento?->codigo ?? 'DOC' }} {{ $item->persona?->numero_documento ?? '' }}
                        </option>
                    @endforeach
                </select>

                @error('cliente_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-12">
                <label for="comprobante_id" class="form-label fw-medium text-secondary small">
                    Comprobante
                    <span class="text-muted">(ticket / boleta / factura)</span>
                </label>

                <select name="comprobante_id"
                        id="comprobante_id"
                        class="form-select @error('comprobante_id') is-invalid @enderror">
                    <option value="">Usar ticket por defecto / boleta rápida</option>
                    @foreach ($comprobantes as $item)
                        <option value="{{ $item->id }}"
                                data-tipo="{{ $item->tipo_comprobante }}"
                                @selected((string) old('comprobante_id', $defaultComprobanteId) === (string) $item->id)>
                            {{ $item->tipo_comprobante }} - {{ $item->serie }}
                        </option>
                    @endforeach
                </select>

                @error('comprobante_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                <small class="help-text-soft d-block mt-2" id="comprobante_help">
                    Factura: el cliente debe estar identificado con RUC.
                </small>
            </div>

            <div class="col-md-12">
                <div class="alert alert-light border mb-0 py-2 small" id="cliente_resumen">
                    Se mostrará aquí el tipo de cliente seleccionado.
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-medium text-secondary small">N° Comprobante</label>
                <input type="text"
                       id="numero_comprobante_preview"
                       class="form-control bg-light text-muted"
                       value="Se generará automáticamente al guardar"
                       readonly>
                <small class="text-muted">El número final se asignará según la serie y el correlativo automático.</small>
            </div>

            <div class="col-md-6">
                <label for="fecha" class="form-label fw-medium text-secondary small">Fecha Emisión</label>
                <input readonly type="text" name="fecha" id="fecha" class="form-control bg-light text-muted" value="{{ date('d-m-Y') }}">
                <input type="hidden" name="fecha_emision" value="{{ old('fecha_emision', now()->toDateTimeString()) }}">
            </div>

            <div class="col-md-6">
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
                @error('metodo_pago') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-12">
                <label for="monto_recibido" class="form-label fw-medium text-secondary small">Monto recibido <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                    <input type="number" name="monto_recibido" id="monto_recibido" class="form-control border-start-0 text-end" value="{{ old('monto_recibido', 0) }}" min="0" step="0.01">
                </div>
                @error('monto_recibido') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-12">
                <label for="vuelto_entregado" class="form-label fw-medium text-secondary small">Vuelto entregado</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                    <input disabled type="number" name="vuelto_entregado" id="vuelto_entregado" class="form-control border-start-0 bg-light text-end fw-bold" value="{{ old('vuelto_entregado', 0) }}" step="0.01">
                </div>
            </div>

            <div class="col-md-12">
                <label for="referencia_operacion" class="form-label fw-medium text-secondary small">Referencia de operación</label>
                <input type="text" name="referencia_operacion" id="referencia_operacion" class="form-control @error('referencia_operacion') is-invalid @enderror" value="{{ old('referencia_operacion') }}">
                @error('referencia_operacion') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="card-footer bg-white border-top border-light p-4 text-center">
        <button id="cancelar" type="button" class="btn btn-light w-100 mb-2 py-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
            <i class="fas fa-times me-2"></i>Cancelar Venta
        </button>

        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-6 shadow-sm" id="guardar">
            <i class="fas fa-check-circle me-2"></i>Procesar Venta
        </button>
    </div>
</div>

@include('cliente.partials.quick-create-modal')

@push('js')
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

        function showAlert(message) {
            formAlert.textContent = message;
            formAlert.classList.remove('d-none');
        }

        function hideAlert() {
            formAlert.textContent = '';
            formAlert.classList.add('d-none');
        }

        function selectedCliente() {
            return clienteSelect.options[clienteSelect.selectedIndex];
        }

        function selectedComprobante() {
            return comprobanteSelect.options[comprobanteSelect.selectedIndex];
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
            const label = selected.textContent.trim();

            clienteResumen.textContent = label;

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
                    showAlert('La factura requiere un cliente jurídico con documento RUC. Selecciona otro comprobante o registra el cliente correcto.');
                    return false;
                }
            }

            hideAlert();
            return true;
        }

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

        if (formVenta) {
            formVenta.addEventListener('submit', function (e) {
                if (!validateFacturaClient()) {
                    e.preventDefault();
                    e.stopPropagation();
                    clienteSelect.focus();
                    return false;
                }
            });
        }

        updateClienteSummary();
        updatePreview();
        validateFacturaClient();
    });
</script>
@endpush