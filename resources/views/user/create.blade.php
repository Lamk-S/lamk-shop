@extends('layouts.app')
@section('title', 'Nuevo Usuario')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Alta de Personal</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Usuarios</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Crear Registro</li>
            </ol>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-light border shadow-sm fw-medium">
            <i class="fas fa-arrow-left me-2"></i>Regresar
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 1000px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-user-shield fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Ficha de Nuevo Usuario</h5>
                    <div class="text-muted small mt-1">Configura credenciales y roles para la nueva cuenta.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('users.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <!-- Datos de Identidad -->
                    <div class="col-lg-6">
                        <div class="border rounded-4 p-4 bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold text-dark text-uppercase small mb-4 pb-2 border-bottom">
                                <i class="fas fa-id-card text-primary me-2"></i> Datos de Identidad
                            </h6>
                            
                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold text-secondary small text-uppercase">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej. Juan Pérez">
                                </div>
                                @error('name') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold text-secondary small text-uppercase">Correo Electrónico (Login) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="usuario@empresa.com">
                                </div>
                                @error('email') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Seguridad y Permisos -->
                    <div class="col-lg-6">
                        <div class="border rounded-4 p-4 bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold text-dark text-uppercase small mb-4 pb-2 border-bottom">
                                <i class="fas fa-lock text-primary me-2"></i> Seguridad y Permisos
                            </h6>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold text-secondary small text-uppercase">Contraseña de Acceso <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-key"></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
                                    <span class="input-group-text bg-white text-muted" style="cursor: pointer;" onclick="togglePassword('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                @error('password') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label fw-bold text-secondary small text-uppercase">Rol Funcional <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-shield-alt"></i></span>
                                    <select name="role" id="role" class="form-select border-start-0 @error('role') is-invalid @enderror">
                                        <option value="">Seleccione un nivel de acceso...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ Str::headline($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small fw-medium">
                        <i class="fas fa-info-circle text-primary me-1"></i> La cuenta se activará automáticamente.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-light px-4 fw-bold rounded-pill border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-pill">
                            <i class="fas fa-save me-2"></i>Confirmar Alta
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function togglePassword(inputId, iconSpan) {
        const input = document.getElementById(inputId);
        const icon = iconSpan.querySelector('i');
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endpush