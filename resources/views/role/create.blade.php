@extends('template')

@section('title', 'Crear rol')

@push('css')
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="container-fluid px-4">

    <h1 class="mt-4 text-center">Crear Rol</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            <a href="{{ route('panel') }}">Inicio</a>
        </li>

        <li class="breadcrumb-item">
            <a href="{{ route('roles.index') }}">Roles</a>
        </li>

        <li class="breadcrumb-item active">
            Crear rol
        </li>
    </ol>

    <div class="container w-100 border border-3 border-primary rounded p-4 mt-3">
        <form action="{{ route('roles.store') }}" method="post">
            @csrf
            <div class="row g-3">
                <!-- Nombre del rol -->
                <div class="col-md-6">
                    <label for="name" class="form-label">Nombre del rol:</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror
                </div>

                <!-- Permisos -->
                <div class="col-12 mb-4">
                    <label for="" class="form-label">Permisos para el rol:</label>
                    @foreach ($permisos as $item)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="permission[]" id="{{ $item->id }}" class="form-check-input" value="{{ $item->id }}">
                            <label for="{{ $item->id}}" class="form-check-label">{{ $item->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('permission')
                    <small class="text-danger">{{ '*'.$message }}</small>
                @enderror

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
@endpush