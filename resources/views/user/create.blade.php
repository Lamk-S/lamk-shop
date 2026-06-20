@extends('layouts.app')
@section('title', 'Nuevo Usuario')

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
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Alta de Personal</h2>
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

    <div class="card main-card mx-auto" style="max-width: 1000px;">
        <div class="card-header card-gradient-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-inner" style="width: 48px; height: 48px;">
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
                    <div class="col-lg-6">
                        <div class="section-box">
                            <h6 class="section-title"><i class="fas fa-id-card text-primary opacity-75"></i> Datos de Identidad</h6>
                            
                            <div class="mb-4">
                                <label for="name" class="form-label form-label-custom">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej. Juan Pérez">
                                </div>
                                @error('name') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label form-label-custom">Correo Electrónico (Login) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="usuario@empresa.com">
                                </div>
                                @error('email') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="section-box">
                            <h6 class="section-title"><i class="fas fa-lock text-primary opacity-75"></i> Seguridad y Permisos</h6>

                            <div class="mb-4">
                                <label for="password" class="form-label form-label-custom">Contraseña de Acceso <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fas fa-key"></i></span>
                                    <input type="password" name="password" id="password" class="form-control password-input @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
                                    <span class="input-group-text password-toggle" onclick="togglePassword('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                @error('password') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label form-label-custom">Rol Funcional <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                                    <option value="">Seleccione un nivel de acceso...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ Str::headline($role->name) }}</option>
                                    @endforeach
                                </select>
                                @error('role') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-top pt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small fw-medium">
                        <i class="fas fa-info-circle text-primary me-1"></i> La cuenta se activará automáticamente y estará lista para usar.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-light px-4 fw-bold rounded-3">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-3">
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