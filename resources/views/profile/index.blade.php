@extends('layouts.app')
@section('title', 'Mi Perfil')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .main-card { border: 0; border-radius: 1.25rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; background: #fff; }
    .card-gradient-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #edf2f7; }
    .section-box { border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; background: #fcfcfd; height: 100%; transition: all 0.2s; }
    .section-box:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    .section-title { font-size: 0.9rem; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
    .form-label-custom { font-size: .80rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 0.4rem; }
    .input-group-text.password-toggle { cursor: pointer; background-color: #fff; border-left: none; color: #94a3b8; transition: color 0.2s; }
    .input-group-text.password-toggle:hover { color: #3b82f6; }
    .form-control.password-input { border-right: none; }
    .form-control:focus + .password-toggle { border-color: #86b7fe; }
    .profile-meta-wrap { display: flex; align-items: center; gap: 1.25rem; padding: 0.5rem 0; }
    .profile-avatar-big { width: 64px; height: 64px; border-radius: 16px; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
    .role-badge-profile { font-size: 0.75rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 700; text-transform: uppercase; display: inline-block; margin-top: 0.35rem; }
    .helper-box { background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 0.75rem; padding: 0.75rem 1rem; color: #0369a1; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Mi Perfil Profesional</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Mi Cuenta</li>
            </ol>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-2 fs-7 fw-mono">ID OPERADOR: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
    </div>

    <div class="card main-card mx-auto" style="max-width: 1000px;">
        <div class="card-header card-gradient-header p-4">
            <div class="profile-meta-wrap">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0d6efd&color=fff&bold=true&size=128" alt="Avatar" class="profile-avatar-big">
                <div>
                    <h4 class="mb-0 fw-black text-dark">{{ $user->name }}</h4>
                    <span class="role-badge-profile">
                        <i class="fas fa-shield-alt me-1"></i> {{ $user->roles->first()?->name ?? 'Sin Rol Asignado' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('profile.update') }}" method="post">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="section-box">
                            <h6 class="section-title"><i class="fas fa-user-circle text-primary opacity-75"></i> Información Personal</h6>
                            
                            <div class="mb-4">
                                <label for="name" class="form-label form-label-custom">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Ej. Tu Nombre">
                                </div>
                                @error('name') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label form-label-custom">Correo Electrónico Corporativo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="tu_correo@empresa.com">
                                </div>
                                @error('email') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="section-box d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="section-title"><i class="fas fa-key text-primary opacity-75"></i> Cambiar Contraseña</h6>

                                <div class="mb-4">
                                    <label for="password" class="form-label form-label-custom">Nueva Contraseña <span class="text-muted fw-normal">(Opcional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control password-input @error('password') is-invalid @enderror" placeholder="Dejar en blanco para mantener la actual">
                                        <span class="input-group-text password-toggle" onclick="togglePassword('password', this)">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    @error('password') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label form-label-custom">Confirmar Nueva Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control password-input" placeholder="Repite tu nueva contraseña">
                                        <span class="input-group-text password-toggle" onclick="togglePassword('password_confirmation', this)">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="helper-box small fw-medium mt-2">
                                <i class="fas fa-info-circle me-2"></i>
                                Para actualizar la clave de acceso, es obligatorio completar ambos campos de contraseña y que coincidan con exactitud.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <span class="text-muted small"><i class="fas fa-shield-halved text-success me-1"></i> Sesión protegida por cifrado atómico SSL.</span>
                    <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                        <a href="{{ route('panel') }}" class="btn btn-light px-4 fw-bold rounded-3">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-3">
                            <i class="fas fa-save me-2"></i>Guardar Cambios de Perfil
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
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush