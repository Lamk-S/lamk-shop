@extends('layouts.app')

@section('title', 'Editar Usuario')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Modificar Usuario</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Usuarios</a></li>
            <li class="breadcrumb-item active">Editar registro</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 700px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-user-pen text-warning me-2"></i>Credenciales y Rol</h5>
            <span class="badge bg-light text-secondary border">ID: {{ $user->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('users.update', ['user' => $user]) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-medium text-secondary">Nombre de Usuario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
                        </div>
                        @error('name') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="email" class="form-label fw-medium text-secondary">Correo Electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                        </div>
                        @error('email') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="role" class="form-label fw-medium text-secondary">Asignar Rol <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="" disabled>Seleccione el nivel de acceso...</option>
                            @php
                                $userRole = $user->roles->pluck('name')->first();
                            @endphp
                            @foreach($roles as $item)
                                <option value="{{ $item->name }}" @selected(old('role', $userRole) == $item->name)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label fw-medium text-secondary">Nueva Contraseña <span class="text-muted fw-normal">(Opcional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" placeholder="Dejar en blanco para no cambiar">
                        </div>
                        @error('password') <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-medium text-secondary">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 @error('password_confirmation') is-invalid @enderror" placeholder="Repita la nueva contraseña">
                        </div>
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-between align-items-center border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer valores</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-sync-alt me-2"></i>Actualizar Usuario</button>
                        </div>
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