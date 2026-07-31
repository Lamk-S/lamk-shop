@extends('layouts.app')
@section('title', 'Editar Usuario')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Modificar Colaborador</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Usuarios</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Editar Registro</li>
            </ol>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-3 py-2 fs-7 font-monospace">ID USUARIO: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
            <a href="{{ route('users.index') }}" class="btn btn-light border shadow-sm fw-medium">
                <i class="fas fa-arrow-left me-2"></i>Regresar
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 1000px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-user-gear fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Actualizar Cuenta de Usuario</h5>
                    <div class="text-muted small mt-1">Modifica los niveles de acceso y los datos operativos del personal.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('users.update', $user) }}" method="post">
                @csrf
                @method('PATCH')
                
                <div class="row g-4">
                    <!-- Identidad y Contacto -->
                    <div class="col-lg-6">
                        <div class="border rounded-4 p-4 bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold text-dark text-uppercase small mb-4 pb-2 border-bottom">
                                <i class="fas fa-id-card text-primary me-2"></i> Identidad y Contacto
                            </h6>
                            
                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold text-secondary small text-uppercase">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Ej. Juan Pérez">
                                </div>
                                @error('name') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold text-secondary small text-uppercase">Correo Electrónico <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="usuario@empresa.com">
                                </div>
                                @error('email') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label fw-bold text-secondary small text-uppercase">Estado de Cuenta</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-toggle-on"></i></span>
                                    <select name="estado" id="estado" class="form-select border-start-0 @error('estado') is-invalid @enderror">
                                        <option value="1" @selected(old('estado', $user->estado) == 1)>Permitir Acceso (Activo)</option>
                                        <option value="0" @selected(old('estado', $user->estado) === 0 || old('estado', $user->estado) === '0')>Bloquear Acceso (Inactivo)</option>
                                    </select>
                                </div>
                                @error('estado') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Seguridad y Permisos -->
                    <div class="col-lg-6">
                        <div class="border rounded-4 p-4 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark text-uppercase small mb-4 pb-2 border-bottom">
                                    <i class="fas fa-lock text-primary me-2"></i> Seguridad y Permisos
                                </h6>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold text-secondary small text-uppercase">Nueva Contraseña <span class="text-muted fw-normal text-lowercase">(Opcional)</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-key"></i></span>
                                        <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="Dejar vacío para conservar actual">
                                        <span class="input-group-text bg-white text-muted" style="cursor: pointer;" onclick="togglePassword('password', this)">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    @error('password') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="role" class="form-label fw-bold text-secondary small text-uppercase">Rol / Nivel de Autorización <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-shield-alt"></i></span>
                                        <select name="role" id="role" class="form-select border-start-0 @error('role') is-invalid @enderror" {{ (strtolower($user->name) === 'administrador' && Auth::id() === $user->id) ? 'disabled' : '' }}>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()?->name) === $role->name)>{{ Str::headline($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="alert alert-warning bg-warning bg-opacity-10 border-warning border-opacity-25 text-dark small fw-medium mb-0 p-3 rounded-3">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                Si dejas el campo de contraseña vacío, el usuario continuará ingresando con su clave actual.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <button type="reset" class="btn btn-link text-muted text-decoration-none fw-medium p-0 w-100 w-sm-auto">Restablecer campos</button>
                    <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                        <a href="{{ route('users.index') }}" class="btn btn-light px-4 fw-bold rounded-pill border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-pill">
                            <i class="fas fa-sync-alt me-2"></i>Actualizar
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