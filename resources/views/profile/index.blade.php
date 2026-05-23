@extends('layouts.app')

@section('title', 'Mi Perfil')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <!-- Encabezado y Breadcrumb -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Configuración de Perfil</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active">Mi Perfil</li>
        </ol>
    </div>

    <!-- Tarjeta del Formulario Centrada -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 600px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Datos de la Cuenta</h5>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('profile.update', ['profile' => $user]) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">
                    <!-- Nombres -->
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-medium text-secondary">Nombres Completos <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
                        </div>
                        @error('name')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Correo electrónico -->
                    <div class="col-md-12">
                        <label for="email" class="form-label fw-medium text-secondary">Correo Electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                        </div>
                        @error('email')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="col-md-12">
                        <label for="password" class="form-label fw-medium text-secondary">Nueva Contraseña <span class="text-muted fw-normal">(Opcional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" placeholder="Dejar en blanco para conservar la actual">
                        </div>
                        @error('password')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botón de Acción -->
                    <div class="col-12 mt-5 border-top pt-4">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 shadow-sm fw-bold">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush