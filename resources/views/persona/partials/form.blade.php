@php
    $rawTipo = optional($persona)->tipo_persona;
    $tipoPersona = old('tipo_persona', $rawTipo->value ?? $rawTipo);
    $rawEstado = optional($persona)->estado;
    $estado = old('estado', $rawEstado->value ?? $rawEstado ?? 1);
    $documentoId = old('documento_id', optional($persona)->documento_id);
    $numeroDocumento = old('numero_documento', optional($persona)->numero_documento);
    $nombres = old('nombres', optional($persona)->nombres);
    $apellidos = old('apellidos', optional($persona)->apellidos);
    $razonSocial = old('razon_social', optional($persona)->razon_social);
    $direccion = old('direccion', optional($persona)->direccion);
    $telefono = old('telefono', optional($persona)->telefono);
    $email = old('email', optional($persona)->email);
    $showEstado = $showEstado ?? false;
    
    // Fallback de seguridad por si otro controlador olvida inyectar la variable
    $optionsTipoPersona = $optionsTipoPersona ?? \App\Enums\TipoPersona::opciones();
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="tipo_persona" class="form-label text-secondary small fw-bold text-uppercase">
            Tipo de perfil <span class="text-danger">*</span>
        </label>
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-users"></i></span>
            <select class="form-select border-start-0 fw-medium @error('tipo_persona') is-invalid @enderror" name="tipo_persona" id="tipo_persona">
                <option value="" selected disabled>Seleccione una opción...</option>
                {{-- AHORA RESPETA EL ENUM DEL BACKEND --}}
                @foreach($optionsTipoPersona as $value => $label)
                    <option value="{{ $value }}" @selected($tipoPersona === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @error('tipo_persona') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="documento_id" class="form-label text-secondary small fw-bold text-uppercase">
            Tipo de documento <span class="text-danger">*</span>
        </label>
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-passport"></i></span>
            <select class="form-select border-start-0 fw-medium @error('documento_id') is-invalid @enderror" name="documento_id" id="documento_id">
                <option value="" selected disabled>Seleccione una opción...</option>
                @foreach($documentos as $item)
                    <option value="{{ $item->id }}" data-codigo="{{ strtoupper($item->codigo) }}" @selected((string) $documentoId === (string) $item->id)>
                        {{ $item->codigo }} - {{ $item->tipo_documento }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('documento_id') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="numero_documento" class="form-label text-secondary small fw-bold text-uppercase">
            Número de documento <span class="text-danger">*</span>
        </label>
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-id-card"></i></span>
            <input type="text" name="numero_documento" id="numero_documento" class="form-control border-start-0 fw-bold @error('numero_documento') is-invalid @enderror" value="{{ $numeroDocumento }}" placeholder="Ej. 74839201">
        </div>
        @error('numero_documento') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="telefono" class="form-label text-secondary small fw-bold text-uppercase">Teléfono</label>
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-phone-alt"></i></span>
            <input type="text" name="telefono" id="telefono" class="form-control border-start-0 @error('telefono') is-invalid @enderror" value="{{ $telefono }}" placeholder="Ej. 987654321">
        </div>
        @error('telefono') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label for="email" class="form-label text-secondary small fw-bold text-uppercase">Correo electrónico</label>
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
            <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ $email }}" placeholder="Ej. cliente@correo.com">
        </div>
        @error('email') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 persona-natural-field">
        <label for="nombres" class="form-label text-secondary small fw-bold text-uppercase">Nombres <span class="text-danger">*</span></label>
        <input type="text" name="nombres" id="nombres" class="form-control shadow-sm @error('nombres') is-invalid @enderror" value="{{ $nombres }}" placeholder="Ej. Juan">
        @error('nombres') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 persona-natural-field">
        <label for="apellidos" class="form-label text-secondary small fw-bold text-uppercase">Apellidos <span class="text-danger">*</span></label>
        <input type="text" name="apellidos" id="apellidos" class="form-control shadow-sm @error('apellidos') is-invalid @enderror" value="{{ $apellidos }}" placeholder="Ej. Pérez">
        @error('apellidos') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12 persona-juridica-field">
        <label for="razon_social" class="form-label text-secondary small fw-bold text-uppercase">Razón social <span class="text-danger">*</span></label>
        <input type="text" name="razon_social" id="razon_social" class="form-control shadow-sm fw-bold @error('razon_social') is-invalid @enderror" value="{{ $razonSocial }}" placeholder="Ej. Lamk Sports S.A.C.">
        @error('razon_social') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label for="direccion" class="form-label text-secondary small fw-bold text-uppercase">Dirección</label>
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-map-marker-alt"></i></span>
            <input type="text" name="direccion" id="direccion" class="form-control border-start-0 @error('direccion') is-invalid @enderror" value="{{ $direccion }}" placeholder="Ej. Av. Principal 123, Ciudad">
        </div>
        @error('direccion') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
    </div>

    @if($showEstado)
        <div class="col-md-12">
            <label for="estado" class="form-label text-secondary small fw-bold text-uppercase">Estado de Cuenta</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-toggle-on"></i></span>
                <select name="estado" id="estado" class="form-select border-start-0 @error('estado') is-invalid @enderror">
                    <option value="1" @selected((string) $estado === '1' || $estado === 1)>Activo y Visible</option>
                    <option value="0" @selected((string) $estado === '0' || $estado === 0)>Inactivo / Bloqueado</option>
                </select>
            </div>
            @error('estado') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
        </div>
    @endif

    <div class="col-12 mt-5 pt-4 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
        <span class="text-muted small"><i class="fas fa-shield-alt text-primary me-2"></i>Verifica los datos de facturación.</span>
        <div class="d-flex gap-2">
            <a href="{{ $cancelRoute }}" class="btn btn-light px-4 fw-bold rounded-pill border shadow-sm">Cancelar</a>
            <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm">
                <i class="{{ $submitIcon ?? 'fas fa-save' }} me-2"></i>{{ $submitLabel ?? 'Guardar Registro' }}
            </button>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoPersonaSelects = document.querySelectorAll('select[name="tipo_persona"]');

        tipoPersonaSelects.forEach(select => {
            const form = select.closest('form');
            if (!form) return;

            const inputsNatural = form.querySelectorAll('input[name="nombres"], input[name="apellidos"]');
            const inputJuridica = form.querySelector('input[name="razon_social"]');
            const docSelect = form.querySelector('select[name="documento_id"]');

            function toggleFields() {
                const val = select.value.toLowerCase();
                const isNatural = val === 'natural';
                const isJuridica = val === 'juridica';

                inputsNatural.forEach(el => {
                    const wrapper = el.closest('[class*="col-"]');
                    wrapper.style.display = isNatural ? 'block' : 'none';
                    el.required = isNatural;
                    if(!isNatural) el.value = '';
                });

                if (inputJuridica) {
                    const wrapper = inputJuridica.closest('[class*="col-"]');
                    wrapper.style.display = isJuridica ? 'block' : 'none';
                    inputJuridica.required = isJuridica;
                    if(!isJuridica) inputJuridica.value = '';
                }

                if (docSelect) {
                    const options = Array.from(docSelect.options);
                    if (isJuridica) {
                        const rucOption = options.find(o => o.text.includes('RUC') || (o.dataset.codigo && o.dataset.codigo.toUpperCase() === 'RUC'));
                        if (rucOption) docSelect.value = rucOption.value;
                    } else if (isNatural) {
                        const dniOption = options.find(o => o.text.includes('DNI') || (o.dataset.codigo && o.dataset.codigo.toUpperCase() === 'DNI'));
                        if (dniOption) docSelect.value = dniOption.value;
                    }
                }
            }

            toggleFields();
            select.addEventListener('change', toggleFields);
        });
    });
</script>
@endpush