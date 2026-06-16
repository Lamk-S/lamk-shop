<div class="modal fade" id="quickProveedorModal" tabindex="-1" aria-labelledby="quickProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('proveedores.quick-store') }}" method="POST" novalidate>
                @csrf

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-semibold" id="quickProveedorModalLabel">Crear proveedor rápido</h5>
                        <small class="text-muted">Registro mínimo para compras y cuentas por pagar.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quick_proveedor_tipo_persona" class="form-label fw-medium text-secondary">
                                Tipo de persona <span class="text-danger">*</span>
                            </label>
                            <select name="tipo_persona" id="quick_proveedor_tipo_persona" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="natural" @selected(old('tipo_persona') === 'natural')>Natural</option>
                                <option value="juridica" @selected(old('tipo_persona') === 'juridica')>Jurídica</option>
                            </select>
                            @error('tipo_persona')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="quick_proveedor_documento_id" class="form-label fw-medium text-secondary">
                                Tipo de documento <span class="text-danger">*</span>
                            </label>
                            <select name="documento_id" id="quick_proveedor_documento_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @isset($documentos)
                                    @foreach ($documentos as $documento)
                                        <option
                                            value="{{ $documento->id }}"
                                            data-codigo="{{ strtoupper($documento->codigo) }}"
                                            @selected((string) old('documento_id') === (string) $documento->id)
                                        >
                                            {{ $documento->tipo_documento }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('documento_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="quick_proveedor_numero_documento" class="form-label fw-medium text-secondary">
                                Número de documento <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="numero_documento"
                                id="quick_proveedor_numero_documento"
                                class="form-control"
                                value="{{ old('numero_documento') }}"
                                placeholder="Ej. 20123456789"
                                autocomplete="off"
                                required
                            >
                            @error('numero_documento')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="quick_proveedor_telefono" class="form-label fw-medium text-secondary">
                                Teléfono
                            </label>
                            <input
                                type="text"
                                name="telefono"
                                id="quick_proveedor_telefono"
                                class="form-control"
                                value="{{ old('telefono') }}"
                                placeholder="Ej. 987654321"
                                autocomplete="off"
                            >
                            @error('telefono')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="quick_proveedor_email" class="form-label fw-medium text-secondary">
                                Correo electrónico
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="quick_proveedor_email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="Ej. compras@empresa.com"
                                autocomplete="off"
                            >
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 quick-proveedor-natural-field d-none">
                            <label for="quick_proveedor_nombres" class="form-label fw-medium text-secondary">
                                Nombres <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="nombres"
                                id="quick_proveedor_nombres"
                                class="form-control"
                                value="{{ old('nombres') }}"
                                placeholder="Ej. Juan"
                                autocomplete="off"
                            >
                            @error('nombres')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 quick-proveedor-natural-field d-none">
                            <label for="quick_proveedor_apellidos" class="form-label fw-medium text-secondary">
                                Apellidos <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="apellidos"
                                id="quick_proveedor_apellidos"
                                class="form-control"
                                value="{{ old('apellidos') }}"
                                placeholder="Ej. Pérez"
                                autocomplete="off"
                            >
                            @error('apellidos')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 quick-proveedor-juridica-field d-none">
                            <label for="quick_proveedor_razon_social" class="form-label fw-medium text-secondary">
                                Razón social <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="razon_social"
                                id="quick_proveedor_razon_social"
                                class="form-control"
                                value="{{ old('razon_social') }}"
                                placeholder="Ej. Lamk Sports S.A.C."
                                autocomplete="off"
                            >
                            @error('razon_social')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="quick_proveedor_direccion" class="form-label fw-medium text-secondary">
                                Dirección
                            </label>
                            <input
                                type="text"
                                name="direccion"
                                id="quick_proveedor_direccion"
                                class="form-control"
                                value="{{ old('direccion') }}"
                                placeholder="Ej. Av. Principal 123"
                                autocomplete="off"
                            >
                            @error('direccion')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoPersona = document.getElementById('quick_proveedor_tipo_persona');
        const documentoSelect = document.getElementById('quick_proveedor_documento_id');

        const naturalFields = document.querySelectorAll('.quick-proveedor-natural-field');
        const juridicaFields = document.querySelectorAll('.quick-proveedor-juridica-field');

        const nombres = document.getElementById('quick_proveedor_nombres');
        const apellidos = document.getElementById('quick_proveedor_apellidos');
        const razonSocial = document.getElementById('quick_proveedor_razon_social');

        function setRequired(elements, required) {
            elements.forEach((el) => {
                const input = el.querySelector('input, select, textarea');
                if (input) {
                    input.required = required;
                }
            });
        }

        function selectDocumentoPorCodigo(codigoBuscado) {
            if (!documentoSelect) return;

            const codigo = String(codigoBuscado || '').toUpperCase();
            let found = false;

            Array.from(documentoSelect.options).forEach((option) => {
                const optionCodigo = String(option.dataset.codigo || '').toUpperCase();
                if (optionCodigo === codigo) {
                    option.selected = true;
                    found = true;
                }
            });

            if (!found && documentoSelect.options.length > 0 && !documentoSelect.value) {
                documentoSelect.selectedIndex = 0;
            }
        }

        function toggleFields() {
            const tipo = String(tipoPersona?.value || '').toLowerCase();

            if (tipo === 'natural') {
                naturalFields.forEach((el) => el.classList.remove('d-none'));
                juridicaFields.forEach((el) => el.classList.add('d-none'));
                setRequired(naturalFields, true);
                setRequired(juridicaFields, false);
                if (razonSocial) razonSocial.value = '';
                selectDocumentoPorCodigo('DNI');
            } else if (tipo === 'juridica') {
                naturalFields.forEach((el) => el.classList.add('d-none'));
                juridicaFields.forEach((el) => el.classList.remove('d-none'));
                setRequired(naturalFields, false);
                setRequired(juridicaFields, true);
                if (nombres) nombres.value = '';
                if (apellidos) apellidos.value = '';
                selectDocumentoPorCodigo('RUC');
            } else {
                naturalFields.forEach((el) => el.classList.add('d-none'));
                juridicaFields.forEach((el) => el.classList.add('d-none'));
                setRequired(naturalFields, false);
                setRequired(juridicaFields, false);
            }
        }

        if (tipoPersona) {
            tipoPersona.addEventListener('change', toggleFields);
        }

        toggleFields();
    });
</script>
@endpush