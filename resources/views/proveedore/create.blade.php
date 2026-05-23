@extends('layouts.app')

@section('title', 'Crear Proveedor')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Nuevo Proveedor</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}" class="text-decoration-none">Proveedores</a></li>
            <li class="breadcrumb-item active">Crear registro</li>
        </ol>
    </div>

    <!-- Tarjeta del Formulario Centrada -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-truck-moving text-primary me-2"></i>Datos del Proveedor</h5>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('proveedores.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <!-- Tipo de persona -->
                    <div class="col-md-12">
                        <label for="tipo_persona" class="form-label fw-medium text-secondary">Tipo de persona <span class="text-danger">*</span></label>
                        <select class="form-select border-start-0 @error('tipo_persona') is-invalid @enderror" name="tipo_persona" id="tipo_persona">
                            <option value="" selected disabled>Seleccione el tipo de entidad...</option>
                            @foreach ($optionsTipoPersona as $value => $label)
                                <option value="{{ $value }}" {{ old('tipo_persona') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_persona')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Razón social / Nombres (Inicia oculto con d-none) -->
                    <div class="col-md-12 d-none" id="box-razon-social">
                        <label id="label-natural" for="razon_social" class="form-label fw-medium text-secondary">Nombres y Apellidos <span class="text-danger">*</span></label>
                        <label id="label-juridica" for="razon_social" class="form-label fw-medium text-secondary d-none">Razón Social de la Empresa <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-building"></i></span>
                            <input type="text" name="razon_social" id="razon_social" class="form-control border-start-0 @error('razon_social') is-invalid @enderror" value="{{ old('razon_social') }}" placeholder="Escriba aquí...">
                        </div>
                        @error('razon_social')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div class="col-md-12">
                        <label for="direccion" class="form-label fw-medium text-secondary">Dirección Física <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="direccion" id="direccion" class="form-control border-start-0 @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}" placeholder="Dirección completa">
                        </div>
                        @error('direccion')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo de Documento -->
                    <div class="col-md-6">
                        <label for="documento_id" class="form-label fw-medium text-secondary">Tipo de documento <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-id-card"></i></span>
                            <select class="form-select border-start-0 @error('documento_id') is-invalid @enderror" name="documento_id" id="documento_id">
                                <option value="" selected disabled>Seleccione...</option>
                                @foreach($documentos as $item)
                                    <option value="{{ $item->id }}" {{ old('documento_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->tipo_documento }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('documento_id')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Número documento -->
                    <div class="col-md-6">
                        <label for="numero_documento" class="form-label fw-medium text-secondary">Número de documento <span class="text-danger">*</span></label>
                        <input type="text" name="numero_documento" id="numero_documento" class="form-control @error('numero_documento') is-invalid @enderror" value="{{ old('numero_documento') }}" placeholder="Ej. 1045... o 2056...">
                        @error('numero_documento')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 mt-5 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save me-2"></i>Guardar Proveedor</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function () {
    function togglePersonaType() {
        let value = $('#tipo_persona').val();
        
        if(value) {
            $('#box-razon-social').removeClass('d-none');
            
            if (value.toLowerCase() === 'natural') {
                $('#label-juridica').addClass('d-none');
                $('#label-natural').removeClass('d-none');
            } else {
                $('#label-natural').addClass('d-none');
                $('#label-juridica').removeClass('d-none');
            }
        } else {
            $('#box-razon-social').addClass('d-none');
        }
    }

    $('#tipo_persona').on('change', togglePersonaType);

    togglePersonaType();
});
</script>
@endpush