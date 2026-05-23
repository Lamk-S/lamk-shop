@extends('layouts.app')

@section('title', 'Crear Usuario')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Nuevo Usuario</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Usuarios</a></li>
            <li class="breadcrumb-item active">Crear registro</li>
        </ol>
    </div>

    <!-- Tarjeta del Formulario Centrada -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 700px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i>Credenciales y Rol</h5>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('users.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <!-- Nombre -->
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-medium text-secondary">Nombre de Usuario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej. Juan Pérez">
                        </div>
                        @error('name') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <!-- Correo electrónico -->
                    <div class="col-md-12">
                        <label for="email" class="form-label fw-medium text-secondary">Correo Electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="ejemplo@empresa.com">
                        </div>
                        @error('email') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <!-- Rol -->
                    <div class="col-md-12">
                        <label for="role" class="form-label fw-medium text-secondary">Asignar Rol <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="" selected disabled>Seleccione el nivel de acceso...</option>
                            @foreach($roles as $item)
                                <option value="{{ $item->name }}" @selected(old('role') == $item->name)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-medium text-secondary">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
                        </div>
                        @error('password') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label fw-medium text-secondary">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control border-start-0 @error('password_confirm') is-invalid @enderror" placeholder="Repita la contraseña">
                        </div>
                        @error('password_confirm') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 mt-5 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save me-2"></i>Guardar Usuario</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush