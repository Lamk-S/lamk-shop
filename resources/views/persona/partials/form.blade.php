@php
    $tipoPersona = old('tipo_persona', optional($persona)->tipo_persona);
    $documentoId = old('documento_id', optional($persona)->documento_id);
    $numeroDocumento = old('numero_documento', optional($persona)->numero_documento);
    $nombres = old('nombres', optional($persona)->nombres);
    $apellidos = old('apellidos', optional($persona)->apellidos);
    $razonSocial = old('razon_social', optional($persona)->razon_social);
    $direccion = old('direccion', optional($persona)->direccion);
    $telefono = old('telefono', optional($persona)->telefono);
    $email = old('email', optional($persona)->email);
    $estado = old('estado', optional($persona)->estado ?? 1);
    $showEstado = $showEstado ?? false;
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="tipo_persona" class="form-label fw-medium text-secondary">
            Tipo de persona <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('tipo_persona') is-invalid @enderror" name="tipo_persona" id="tipo_persona">
            <option value="" selected disabled>Seleccione una opción...</option>
            <option value="natural" @selected($tipoPersona === 'natural')>Natural</option>
            <option value="juridica" @selected($tipoPersona === 'juridica')>Jurídica</option>
        </select>
        @error('tipo_persona')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="documento_id" class="form-label fw-medium text-secondary">
            Tipo de documento <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('documento_id') is-invalid @enderror" name="documento_id" id="documento_id">
            <option value="" selected disabled>Seleccione una opción...</option>
            @foreach($documentos as $item)
                <option value="{{ $item->id }}" @selected((string) $documentoId === (string) $item->id)>
                    {{ $item->codigo }} - {{ $item->tipo_documento }}
                </option>
            @endforeach
        </select>
        @error('documento_id')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="numero_documento" class="form-label fw-medium text-secondary">
            Número de documento <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-id-badge"></i></span>
            <input type="text"
                   name="numero_documento"
                   id="numero_documento"
                   class="form-control border-start-0 @error('numero_documento') is-invalid @enderror"
                   value="{{ $numeroDocumento }}"
                   placeholder="Ej. 74839201 / 20123456789">
        </div>
        @error('numero_documento')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="telefono" class="form-label fw-medium text-secondary">Teléfono</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-phone"></i></span>
            <input type="text"
                   name="telefono"
                   id="telefono"
                   class="form-control border-start-0 @error('telefono') is-invalid @enderror"
                   value="{{ $telefono }}"
                   placeholder="Ej. 987654321">
        </div>
        @error('telefono')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label for="email" class="form-label fw-medium text-secondary">Correo electrónico</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
            <input type="email"
                   name="email"
                   id="email"
                   class="form-control border-start-0 @error('email') is-invalid @enderror"
                   value="{{ $email }}"
                   placeholder="Ej. cliente@correo.com">
        </div>
        @error('email')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 persona-natural-field">
        <label for="nombres" class="form-label fw-medium text-secondary">
            Nombres <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="nombres"
               id="nombres"
               class="form-control @error('nombres') is-invalid @enderror"
               value="{{ $nombres }}"
               placeholder="Ej. Juan">
        @error('nombres')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 persona-natural-field">
        <label for="apellidos" class="form-label fw-medium text-secondary">
            Apellidos <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="apellidos"
               id="apellidos"
               class="form-control @error('apellidos') is-invalid @enderror"
               value="{{ $apellidos }}"
               placeholder="Ej. Pérez">
        @error('apellidos')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 persona-juridica-field">
        <label for="razon_social" class="form-label fw-medium text-secondary">
            Razón social <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="razon_social"
               id="razon_social"
               class="form-control @error('razon_social') is-invalid @enderror"
               value="{{ $razonSocial }}"
               placeholder="Ej. Lamk Sports S.A.C.">
        @error('razon_social')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label for="direccion" class="form-label fw-medium text-secondary">Dirección</label>
        <input type="text"
               name="direccion"
               id="direccion"
               class="form-control @error('direccion') is-invalid @enderror"
               value="{{ $direccion }}"
               placeholder="Ej. Av. Principal 123, Ciudad">
        @error('direccion')
            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    @if($showEstado)
        <div class="col-md-12">
            <label for="estado" class="form-label fw-medium text-secondary">Estado</label>
            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                <option value="1" @selected((string) $estado === '1' || $estado === 1)>Activo</option>
                <option value="0" @selected((string) $estado === '0' || $estado === 0)>Inactivo</option>
            </select>
            @error('estado')
                <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
            @enderror
        </div>
    @endif

    <div class="col-12 mt-5 d-flex justify-content-end gap-2 border-top pt-4">
        <a href="{{ $cancelRoute }}" class="btn btn-light px-4">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4 shadow-sm">
            <i class="{{ $submitIcon ?? 'fas fa-save' }} me-2"></i>{{ $submitLabel ?? 'Guardar Registro' }}
        </button>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoPersona = document.getElementById('tipo_persona');
        const naturalFields = document.querySelectorAll('.persona-natural-field');
        const juridicaFields = document.querySelectorAll('.persona-juridica-field');
        const nombres = document.getElementById('nombres');
        const apellidos = document.getElementById('apellidos');
        const razonSocial = document.getElementById('razon_social');
        const documento = document.getElementById('documento_id');

        function toggleFields() {
            const value = tipoPersona ? tipoPersona.value : '';

            if (value === 'natural') {
                naturalFields.forEach(el => el.style.display = 'block');
                juridicaFields.forEach(el => el.style.display = 'none');

                if (nombres) nombres.required = true;
                if (apellidos) apellidos.required = true;
                if (razonSocial) razonSocial.required = false;

                if (razonSocial) razonSocial.value = razonSocial.value || '';
            } else if (value === 'juridica') {
                naturalFields.forEach(el => el.style.display = 'none');
                juridicaFields.forEach(el => el.style.display = 'block');

                if (nombres) nombres.required = false;
                if (apellidos) apellidos.required = false;
                if (razonSocial) razonSocial.required = true;
            } else {
                naturalFields.forEach(el => el.style.display = 'none');
                juridicaFields.forEach(el => el.style.display = 'none');
            }

            if (documento && value === 'juridica') {
                const options = Array.from(documento.options);
                const rucOption = options.find(opt => (opt.textContent || '').includes('RUC'));
                if (rucOption) documento.value = rucOption.value;
            }
        }

        toggleFields();

        if (tipoPersona) {
            tipoPersona.addEventListener('change', toggleFields);
        }
    });
</script>
@endpush