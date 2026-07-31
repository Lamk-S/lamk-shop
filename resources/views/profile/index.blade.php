@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Mi Perfil Profesional</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Mi Cuenta</li>
            </ol>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-2 fs-7 font-monospace shadow-sm">
            ID OPERADOR: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
        </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 1000px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0d6efd&color=fff&bold=true&size=128" 
                     alt="Avatar" class="rounded-4 shadow-sm" style="width: 72px; height: 72px; object-fit: cover;">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">{{ $user->name }}</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                        <i class="fas fa-shield-alt me-1"></i> {{ Str::headline($user->roles->first()?->name ?? 'Sin Rol Asignado') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('profile.update') }}" method="post">
                @csrf
                @method('PATCH')
                
                <div class="row g-4">
                    <!-- Información Personal -->
                    <div class="col-lg-6">
                        <div class="border rounded-4 p-4 bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold text-dark text-uppercase small mb-4 pb-2 border-bottom">
                                <i class="fas fa-user-circle text-primary me-2"></i> Información Personal
                            </h6>
                            
                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold text-secondary small text-uppercase">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Ej. Tu Nombre">
                                </div>
                                @error('name') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold text-secondary small text-uppercase">Correo Electrónico <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="tu_correo@empresa.com">
                                </div>
                                @error('email') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="col-lg-6">
                        <div class="border rounded-4 p-4 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark text-uppercase small mb-4 pb-2 border-bottom">
                                    <i class="fas fa-key text-primary me-2"></i> Cambiar Contraseña
                                </h6>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold text-secondary small text-uppercase">Nueva Contraseña <span class="text-muted fw-normal text-lowercase">(Opcional)</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="En blanco para mantener actual">
                                        <span class="input-group-text bg-white text-muted" style="cursor: pointer;" onclick="togglePassword('password', this)">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    @error('password') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label fw-bold text-secondary small text-uppercase">Confirmar Contraseña</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0" placeholder="Repite tu nueva contraseña">
                                        <span class="input-group-text bg-white text-muted" style="cursor: pointer;" onclick="togglePassword('password_confirmation', this)">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-dark small fw-medium mb-0 p-3 rounded-3">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                Si ingresas una contraseña nueva, ambos campos deben coincidir.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <span class="text-muted small"><i class="fas fa-shield-halved text-success me-1"></i> Sesión segura protegida.</span>
                    <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                        <a href="{{ route('panel') }}" class="btn btn-light px-4 fw-bold rounded-pill border shadow-sm">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-pill">
                            <i class="fas fa-save me-2"></i>Actualizar Perfil
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