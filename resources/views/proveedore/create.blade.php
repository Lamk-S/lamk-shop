@extends('template')

@section('title', 'Crear proveedor')

@push('css')
<style>
    #direccion {
        resize: none;
    }

    #box-razon-social {
        display: none;
    }
</style>

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="container-fluid px-4">

    <h1 class="mt-4 text-center">Crear Proveedor</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            <a href="{{ route('panel') }}">Inicio</a>
        </li>

        <li class="breadcrumb-item">
            <a href="{{ route('proveedores.index') }}">Proveedores</a>
        </li>

        <li class="breadcrumb-item active">
            Crear proveedor
        </li>
    </ol>

    <div class="container w-100 border border-3 border-primary rounded p-4 mt-3">

        <form action="{{ route('proveedores.store') }}" method="post">
            @csrf

            <div class="row g-3">

                <!-- Tipo de persona -->
                <div class="col-md-6">
                    <label for="tipo_persona" class="form-label">
                        Tipo de persona:
                    </label>

                    <select class="form-select"
                        name="tipo_persona"
                        id="tipo_persona">

                        <option value="" selected disabled>
                            Seleccione una opción
                        </option>

                        @foreach ($optionsTipoPersona as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('tipo_persona') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>

                    @error('tipo_persona')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror
                </div>

                <!-- Razón social -->
                <div class="col-md-12 mb-2" id="box-razon-social">

                    <label id="label-natural" for="razon_social">
                        Nombres y apellidos
                    </label>

                    <label id="label-juridica" for="razon_social">
                        Nombre de la empresa
                    </label>

                    <input type="text"
                        name="razon_social"
                        id="razon_social"
                        class="form-control"
                        value="{{ old('razon_social') }}">

                    @error('razon_social')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror

                </div>

                <!-- Dirección -->
                <div class="col-md-12 mb-2">

                    <label for="direccion" class="form-label">
                        Dirección:
                    </label>

                    <input type="text"
                        name="direccion"
                        id="direccion"
                        class="form-control"
                        value="{{ old('direccion') }}">

                    @error('direccion')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror

                </div>

                <!-- Documento -->
                <div class="col-md-6">

                    <label for="documento_id" class="form-label">
                        Tipo de documento:
                    </label>

                    <select class="form-select"
                        name="documento_id"
                        id="documento_id">

                        <option value="" selected disabled>
                            Seleccione una opción
                        </option>

                        @foreach($documentos as $item)
                            <option value="{{ $item->id }}"
                                {{ old('documento_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->tipo_documento }}
                            </option>
                        @endforeach

                    </select>

                    @error('documento_id')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror

                </div>

                <!-- Número documento -->
                <div class="col-md-6">

                    <label for="numero_documento" class="form-label">
                        Número de documento:
                    </label>

                    <input type="text"
                        name="numero_documento"
                        id="numero_documento"
                        class="form-control"
                        value="{{ old('numero_documento') }}">

                    @error('numero_documento')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror

                </div>

                <!-- Botón -->
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>

            </div>

        </form>

    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
$(document).ready(function () {

    $('#tipo_persona').on('change', function () {

        let value = $(this).val();

        if (value == 'natural') {
            $('#label-juridica').hide();
            $('#label-natural').show();
        } else {
            $('#label-natural').hide();
            $('#label-juridica').show();
        }

        $('#box-razon-social').show();
    });

});
</script>
@endpush