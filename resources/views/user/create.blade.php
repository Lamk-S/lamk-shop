@extends('template')

@section('title', 'Crear usuario')

@push('css')
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="container-fluid px-4">

    <h1 class="mt-4 text-center">Crear Usuario</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            <a href="{{ route('panel') }}">Inicio</a>
        </li>

        <li class="breadcrumb-item">
            <a href="{{ route('users.index') }}">Usuarios</a>
        </li>

        <li class="breadcrumb-item active">
            Crear usuario
        </li>
    </ol>

    <div class="container w-100 border border-3 border-primary rounded p-4 mt-3">
        <form action="{{ route('users.store') }}" method="post">
            @csrf
            <div class="row g-3">
                <!-- Nombre del usuario -->
                <div class="row mb-4 mt-4">
                    <label for="name" class="col-sm-2 col-form-label">Nombres:</label>
                    <div class="col-sm-4">
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                    </div>
                    <div class="col-sm-4">
                        <div class="form-text">Escriba un solo nombre</div>
                    </div>
                    <div class="col-sm-2">
                        @error('name')
                            <small class="text-danger">{{ '*'.$message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Correo electrónico -->
                <div class="row mb-4 mt-4">
                    <label for="email" class="col-sm-2 col-form-label">Correo electrónico:</label>
                    <div class="col-sm-4">
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-sm-4">
                        <div class="form-text">Dirección de correo electrónico</div>
                    </div>
                    <div class="col-sm-2">
                        @error('email')
                            <small class="text-danger">{{ '*'.$message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="row mb-4 mt-4">
                    <label for="password" class="col-sm-2 col-form-label">Contraseña:</label>
                    <div class="col-sm-4">
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="col-sm-4">
                        <div class="form-text">Escriba una contraseña segura. Debe incluir: números, letras y caracteres especiales.</div>
                    </div>
                    <div class="col-sm-2">
                        @error('password')
                            <small class="text-danger">{{ '*'.$message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Confirmar contraseña -->
                <div class="row mb-4 mt-4"> 
                    <label for="password_confirm" class="col-sm-2 col-form-label">Confirmar contraseña:</label>
                    <div class="col-sm-4">
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control">
                    </div>
                    <div class="col-sm-4">
                        <div class="form-text">Vuelva a escribir la contraseña para confirmarla.</div>
                    </div>
                    <div class="col-sm-2">
                        @error('password_confirm')
                            <small class="text-danger">{{ '*'.$message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Roles -->
                <div class="row mb-4">
                    <label for="role" class="col-sm-2 col-form-label">Roles:</label>
                    <div class="col-sm-4">
                        <select name="role" id="role" class="form-select">
                            <option value="" selected disabled>Seleccione un rol</option>
                            @foreach($roles as $item)
                                <option value="{{ $item->name }}" @selected(old('role') == $item->name)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
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
@endpush