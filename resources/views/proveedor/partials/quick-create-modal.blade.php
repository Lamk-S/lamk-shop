<div class="modal fade" id="quickProveedorModal" tabindex="-1" aria-labelledby="quickProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('proveedores.quick-store') }}" method="POST" id="formQuickProveedor" novalidate>
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="quickProveedorModalLabel">Crear proveedor rápido</h5>
                        <small class="text-muted">Registro mínimo para compras y cuentas por pagar.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_tipo_persona" class="form-label fw-bold text-secondary small text-uppercase">
                                Tipo de persona <span class="text-danger">*</span>
                            </label>
                            <select name="tipo_persona" id="modal_tipo_persona" class="form-select shadow-sm" required>
                                <option value="">Seleccione...</option>
                                @foreach(\App\Enums\TipoPersona::opciones() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('tipo_persona') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tipo_persona') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_documento_id" class="form-label fw-bold text-secondary small text-uppercase">
                                Tipo documento <span class="text-danger">*</span>
                            </label>
                            <select name="documento_id" id="modal_documento_id" class="form-select shadow-sm" required>
                                <option value="">Seleccione...</option>
                                @isset($documentos)
                                    @foreach ($documentos as $documento)
                                        <option value="{{ $documento->id }}" data-codigo="{{ strtoupper($documento->codigo) }}" @selected((string) old('documento_id') === (string) $documento->id)>
                                            {{ $documento->tipo_documento }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('documento_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_numero_documento" class="form-label fw-bold text-secondary small text-uppercase">
                                Número documento <span class="text-danger">*</span>
                            </label>
                            <div class="input-group shadow-sm">
                                <input type="text" name="numero_documento" id="modal_numero_documento" class="form-control" value="{{ old('numero_documento') }}" placeholder="Ej. 20123456789" autocomplete="off" maxlength="11" required>
                                <button type="button" class="btn btn-info text-white fw-bold" id="btnBuscarDocProv">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            @error('numero_documento') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_telefono" class="form-label fw-bold text-secondary small text-uppercase">Teléfono</label>
                            <input type="text" name="telefono" id="modal_telefono" class="form-control shadow-sm" value="{{ old('telefono') }}" placeholder="Ej. 987654321" autocomplete="off">
                        </div>

                        <div class="col-md-12">
                            <label for="modal_email" class="form-label fw-bold text-secondary small text-uppercase">Correo electrónico</label>
                            <input type="email" name="email" id="modal_email" class="form-control shadow-sm" value="{{ old('email') }}" placeholder="Ej. ventas@empresa.com" autocomplete="off">
                        </div>

                        <div class="col-md-6 quick-proveedor-natural-field d-none">
                            <label for="modal_nombres" class="form-label fw-bold text-secondary small text-uppercase">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" id="modal_nombres" class="form-control shadow-sm" value="{{ old('nombres') }}" autocomplete="off">
                        </div>

                        <div class="col-md-6 quick-proveedor-natural-field d-none">
                            <label for="modal_apellidos" class="form-label fw-bold text-secondary small text-uppercase">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" id="modal_apellidos" class="form-control shadow-sm" value="{{ old('apellidos') }}" autocomplete="off">
                        </div>

                        <div class="col-md-12 quick-proveedor-juridica-field d-none">
                            <label for="modal_razon_social" class="form-label fw-bold text-secondary small text-uppercase">Razón social <span class="text-danger">*</span></label>
                            <input type="text" name="razon_social" id="modal_razon_social" class="form-control shadow-sm fw-bold" value="{{ old('razon_social') }}" autocomplete="off">
                        </div>

                        <div class="col-md-12">
                            <label for="modal_direccion" class="form-label fw-bold text-secondary small text-uppercase">Dirección</label>
                            <input type="text" name="direccion" id="modal_direccion" class="form-control shadow-sm" value="{{ old('direccion') }}" placeholder="Av. Principal 123" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold rounded-pill px-4 border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i>Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalTipoSelect = document.getElementById('modal_tipo_persona');
        const modalForm = document.getElementById('formQuickProveedor');
        
        if (modalTipoSelect && modalForm) {
            const inputsNatural = modalForm.querySelectorAll('.quick-proveedor-natural-field');
            const inputsJuridica = modalForm.querySelectorAll('.quick-proveedor-juridica-field');
            const docSelect = document.getElementById('modal_documento_id');

            function toggleModalFields() {
                const isNatural = modalTipoSelect.value === 'natural';
                const isJuridica = modalTipoSelect.value === 'juridica';

                inputsNatural.forEach(el => {
                    el.classList.toggle('d-none', !isNatural);
                    const input = el.querySelector('input');
                    if (input) input.required = isNatural;
                });

                inputsJuridica.forEach(el => {
                    el.classList.toggle('d-none', !isJuridica);
                    const input = el.querySelector('input');
                    if (input) input.required = isJuridica;
                });

                if (docSelect) {
                    const options = Array.from(docSelect.options);
                    if (isJuridica) {
                        const rucOption = options.find(o => o.dataset.codigo && o.dataset.codigo.toUpperCase() === 'RUC');
                        if (rucOption) docSelect.value = rucOption.value;
                    } else if (isNatural) {
                        const dniOption = options.find(o => o.dataset.codigo && o.dataset.codigo.toUpperCase() === 'DNI');
                        if (dniOption) docSelect.value = dniOption.value;
                    }
                }
            }

            modalTipoSelect.addEventListener('change', toggleModalFields);
            
            if (modalTipoSelect.value) toggleModalFields(); 
        }
    });
</script>