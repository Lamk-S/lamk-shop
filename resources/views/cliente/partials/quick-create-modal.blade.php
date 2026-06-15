<div class="modal fade" id="quickClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="quickClienteForm" action="{{ route('clientes.quick.store') }}" method="post">
                @csrf

                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-semibold mb-1">Registrar cliente rápido</h5>
                        <small class="text-muted">Alta express para venta mostrador o cliente recurrente.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body pt-3">
                    <div class="alert alert-info border-0">
                        Para boleta rápida puedes seguir con “Consumidor final”. Para factura, el cliente debe ser una persona jurídica con RUC.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quick_cliente_tipo_persona" class="form-label fw-medium">Tipo de persona <span class="text-danger">*</span></label>
                            <select name="tipo_persona" id="quick_cliente_tipo_persona" class="form-select" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="natural">Natural</option>
                                <option value="juridica">Jurídica</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="quick_cliente_documento_id" class="form-label fw-medium">Tipo de documento <span class="text-danger">*</span></label>
                            <select name="documento_id" id="quick_cliente_documento_id" class="form-select" required>
                                <option value="" selected disabled>Seleccione...</option>
                                @foreach ($documentos as $documento)
                                    <option value="{{ $documento->id }}" data-codigo="{{ $documento->codigo }}">
                                        {{ $documento->codigo }} - {{ $documento->tipo_documento }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 persona-natural-field">
                            <label for="quick_cliente_nombres" class="form-label fw-medium">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" id="quick_cliente_nombres" class="form-control" placeholder="Juan">
                        </div>

                        <div class="col-md-6 persona-natural-field">
                            <label for="quick_cliente_apellidos" class="form-label fw-medium">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" id="quick_cliente_apellidos" class="form-control" placeholder="Pérez">
                        </div>

                        <div class="col-12 persona-juridica-field">
                            <label for="quick_cliente_razon_social" class="form-label fw-medium">Razón social <span class="text-danger">*</span></label>
                            <input type="text" name="razon_social" id="quick_cliente_razon_social" class="form-control" placeholder="Lamk Sports S.A.C.">
                        </div>

                        <div class="col-md-6">
                            <label for="quick_cliente_numero_documento" class="form-label fw-medium">Número de documento <span class="text-danger">*</span></label>
                            <input type="text" name="numero_documento" id="quick_cliente_numero_documento" class="form-control" placeholder="74839201 / 20123456789" required>
                        </div>

                        <div class="col-md-6">
                            <label for="quick_cliente_telefono" class="form-label fw-medium">Teléfono</label>
                            <input type="text" name="telefono" id="quick_cliente_telefono" class="form-control" placeholder="987654321">
                        </div>

                        <div class="col-md-12">
                            <label for="quick_cliente_email" class="form-label fw-medium">Correo electrónico</label>
                            <input type="email" name="email" id="quick_cliente_email" class="form-control" placeholder="cliente@correo.com">
                        </div>

                        <div class="col-md-12">
                            <label for="quick_cliente_direccion" class="form-label fw-medium">Dirección</label>
                            <input type="text" name="direccion" id="quick_cliente_direccion" class="form-control" placeholder="Av. Principal 123">
                        </div>

                        <input type="hidden" name="estado" value="1">
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('quickClienteModal');
        if (!modal) return;

        const form = modal.querySelector('#quickClienteForm');
        const tipo = modal.querySelector('#quick_cliente_tipo_persona');
        const documento = modal.querySelector('#quick_cliente_documento_id');
        const naturalFields = modal.querySelectorAll('.persona-natural-field');
        const juridicaFields = modal.querySelectorAll('.persona-juridica-field');
        const nombres = modal.querySelector('#quick_cliente_nombres');
        const apellidos = modal.querySelector('#quick_cliente_apellidos');
        const razonSocial = modal.querySelector('#quick_cliente_razon_social');

        function toggle() {
            const value = tipo.value;

            if (value === 'natural') {
                naturalFields.forEach(el => el.style.display = 'block');
                juridicaFields.forEach(el => el.style.display = 'none');
                nombres.required = true;
                apellidos.required = true;
                razonSocial.required = false;
                razonSocial.value = '';
            } else if (value === 'juridica') {
                naturalFields.forEach(el => el.style.display = 'none');
                juridicaFields.forEach(el => el.style.display = 'block');
                nombres.required = false;
                apellidos.required = false;
                razonSocial.required = true;

                const rucOption = Array.from(documento.options).find(opt => (opt.dataset.codigo || '').toUpperCase() === 'RUC');
                if (rucOption) documento.value = rucOption.value;
            } else {
                naturalFields.forEach(el => el.style.display = 'none');
                juridicaFields.forEach(el => el.style.display = 'none');
            }
        }

        async function submitQuickCliente(event) {
            event.preventDefault();

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    const message = data?.message || 'No se pudo registrar el cliente.';
                    throw new Error(message);
                }

                const cliente = data.cliente;
                const select = document.getElementById('cliente_id');

                if (select && cliente) {
                    const text = `${cliente.label} — ${cliente.documento} ${cliente.numero_documento}`;
                    const option = new Option(text, cliente.id, true, true);
                    select.add(option);

                    if (window.jQuery && typeof jQuery.fn.selectpicker === 'function') {
                        jQuery(select).selectpicker('refresh');
                        jQuery(select).selectpicker('val', String(cliente.id));
                    } else {
                        select.value = cliente.id;
                    }

                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }

                form.reset();
                toggle();

                bootstrap.Modal.getOrCreateInstance(modal).hide();

                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1800,
                        icon: 'success',
                        title: data.message || 'Cliente registrado correctamente'
                    });
                }
            } catch (error) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'No se pudo registrar el cliente.'
                    });
                } else {
                    alert(error.message || 'No se pudo registrar el cliente.');
                }
            }
        }

        toggle();
        tipo.addEventListener('change', toggle);
        form.addEventListener('submit', submitQuickCliente);
    });
</script>
@endpush