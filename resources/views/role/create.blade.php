@extends('layouts.app')

@section('title', 'Crear Rol')

@push('css')
<style>
    .permissions-box { max-height: 400px; overflow-y: auto; overflow-x: hidden; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Nuevo Rol</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}" class="text-decoration-none">Roles</a></li>
            <li class="breadcrumb-item active">Crear registro</li>
        </ol>
    </div>

    <!-- Tarjeta del Formulario Centrada -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 900px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-user-shield text-primary me-2"></i>Configuración del Rol y Permisos</h5>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('roles.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <!-- Nombre del rol -->
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-medium text-secondary">Nombre del Rol <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-tag"></i></span>
                            <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej. Administrador, Vendedor, Supervisor...">
                        </div>
                        @error('name')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permisos (Grid de 3 columnas con switches) -->
                    <div class="col-12 mt-4">
                        <label class="form-label fw-medium text-secondary mb-3">Asignación de Permisos <span class="text-danger">*</span></label>
                        
                        <div class="permissions-box border rounded-3 p-4 bg-light">
                            <div class="row g-3">
                                @foreach ($permisos as $item)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check form-switch p-3 border rounded-3 bg-white shadow-sm h-100 d-flex align-items-center">
                                            <input type="checkbox" name="permission[]" id="perm_{{ $item->id }}" class="form-check-input ms-0 me-3" style="width: 2.5em; height: 1.2em; cursor: pointer;" value="{{ $item->id }}">
                                            <label for="perm_{{ $item->id }}" class="form-check-label mb-0 fw-medium text-dark flex-grow-1" style="cursor: pointer; font-size: 0.9rem;">
                                                {{ $item->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @error('permission')
                            <div class="text-danger mt-2 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 mt-5 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save me-2"></i>Guardar Rol</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection