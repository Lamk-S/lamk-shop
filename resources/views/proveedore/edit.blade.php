@extends('template')

@section('title', 'Editar proveedor')

@push('css')
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="container-fluid px-4">

    <h1 class="mt-4 text-center">Editar Proveedor</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            <a href="{{ route('panel') }}">Inicio</a>
        </li>

        <li class="breadcrumb-item">
            <a href="{{ route('proveedores.index') }}">Proveedores</a>
        </li>

        <li class="breadcrumb-item active">
            Editar proveedor
        </li>
    </ol>

    <div class="container w-100 border border-3 border-primary rounded p-4 mt-3">

        <form action="{{ route('proveedores.update', ['proveedore' => $proveedore]) }}"
            method="post">

            @method('PATCH')
            @csrf

            <div class="row g-3">

                <!-- Tipo de persona -->
                <div class="col-md-6">

                    <label class="form-label">
                        Tipo de persona:
                        <span class="fw-bold">
                            {{ strtoupper($proveedore->persona->tipo_persona) }}
                        </span>
                    </label>

                </div>

                <!-- Razón social -->
                <div class="col-md-12 mb-2">

                    <label class="form-label">
                        {{ $proveedore->persona->tipo_persona == 'natural'
                            ? 'Nombres y apellidos'
                            : 'Nombre de la empresa' }}
                    </label>

                    <input type="text"
                        name="razon_social"
                        class="form-control"
                        value="{{ old('razon_social', $proveedore->persona->razon_social) }}">

                    @error('razon_social')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror

                </div>

                <!-- Dirección -->
                <div class="col-md-12 mb-2">

                    <label class="form-label">
                        Dirección:
                    </label>

                    <input type="text"
                        name="direccion"
                        class="form-control"
                        value="{{ old('direccion', $proveedore->persona->direccion) }}">

                    @error('direccion')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror

                </div>

                <!-- Documento -->
                <div class="col-md-6">

                    <label class="form-label">
                        Tipo de documento:
                    </label>

                    <select class="form-select"
                        name="documento_id">

                        @foreach($documentos as $item)
                            <option value="{{ $item->id }}"
                                {{ old('documento_id', $proveedore->persona->documento_id) == $item->id ? 'selected' : '' }}>
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

                    <label class="form-label">
                        Número de documento:
                    </label>

                    <input type="text"
                        name="numero_documento"
                        class="form-control"
                        value="{{ old('numero_documento', $proveedore->persona->numero_documento) }}">

                    @error('numero_documento')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror

                </div>

                <!-- Botones -->
                <div class="col-12 text-center">

                    <button type="submit" class="btn btn-primary">
                        Actualizar
                    </button>

                    <button type="reset" class="btn btn-secondary">
                        Reiniciar
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush