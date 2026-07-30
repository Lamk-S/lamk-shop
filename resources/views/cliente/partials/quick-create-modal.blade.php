@php
    $shouldOpenQuickClienteModal = session('quickClienteError') || $errors->hasAny([
        'tipo_persona', 'documento_id', 'numero_documento', 'nombres',
        'apellidos', 'razon_social', 'direccion', 'telefono', 'email',
    ]);
@endphp

<div class="modal fade" id="quickClienteModal" tabindex="-1" aria-labelledby="quickClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('clientes.quick-store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-semibold" id="quickClienteModalLabel">Crear cliente rápido</h5>
                        <small class="text-muted">Registro mínimo para ventas y facturación.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    @if (session('quickClienteError'))
                        <div class="alert alert-danger mb-3">
                            {{ session('quickClienteError') }}
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_tipo_persona" class="form-label fw-medium text-secondary">
                                Tipo de persona <span class="text-danger">*</span>
                            </label>
                            <select name="tipo_persona" id="modal_tipo_persona" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="natural" @selected(old('tipo_persona') === 'natural')>Natural</option>
                                <option value="juridica" @selected(old('tipo_persona') === 'juridica')>Jurídica</option>
                            </select>
                            @error('tipo_persona')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_documento_id" class="form-label fw-medium text-secondary">
                                Tipo de documento <span class="text-danger">*</span>
                            </label>
                            <select name="documento_id" id="modal_documento_id" class="form-select" required>
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
                            <label for="modal_numero_documento" class="form-label fw-medium text-secondary">
                                Número de documento <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="numero_documento"
                                    id="modal_numero_documento"
                                    class="form-control"
                                    value="{{ old('numero_documento') }}"
                                    placeholder="Ej. 87689765"
                                    autocomplete="off"
                                    maxlength="11"
                                    required
                                >
                                <button type="button" class="btn btn-info text-white" id="btnBuscarDoc">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            @error('numero_documento')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_telefono" class="form-label fw-medium text-secondary">
                                Teléfono
                            </label>
                            <input
                                type="text"
                                name="telefono"
                                id="modal_telefono"
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
                            <label for="modal_email" class="form-label fw-medium text-secondary">
                                Correo electrónico
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="modal_email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="Ej. cliente@correo.com"
                                autocomplete="off"
                            >
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 quick-cliente-natural-field d-none">
                            <label for="modal_nombres" class="form-label fw-medium text-secondary">
                                Nombres <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="nombres"
                                id="modal_nombres"
                                class="form-control"
                                value="{{ old('nombres') }}"
                                placeholder="Ej. Juan"
                                autocomplete="off"
                            >
                            @error('nombres')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 quick-cliente-natural-field d-none">
                            <label for="modal_apellidos" class="form-label fw-medium text-secondary">
                                Apellidos <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="apellidos"
                                id="modal_apellidos"
                                class="form-control"
                                value="{{ old('apellidos') }}"
                                placeholder="Ej. Pérez"
                                autocomplete="off"
                            >
                            @error('apellidos')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 quick-cliente-juridica-field d-none">
                            <label for="modal_razon_social" class="form-label fw-medium text-secondary">
                                Razón social <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="razon_social"
                                id="modal_razon_social"
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
                            <label for="modal_direccion" class="form-label fw-medium text-secondary">
                                Dirección
                            </label>
                            <input
                                type="text"
                                name="direccion"
                                id="modal_direccion"
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
        @if ($shouldOpenQuickClienteModal)
            const modalEl = document.getElementById('quickClienteModal');
            if (modalEl && window.bootstrap) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        @endif
    });
</script>
@endpush