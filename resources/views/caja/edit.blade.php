@extends('layouts.app')

@section('title', 'Editar Caja')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Modificar Caja</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cajas.index') }}" class="text-decoration-none">Cajas</a></li>
            <li class="breadcrumb-item active">Editar registro</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 700px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-cash-register text-warning me-2"></i>Datos de la Caja
            </h5>
            <span class="badge bg-light text-secondary border">ID: {{ $caja->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('cajas.update', $caja->id) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label fw-medium text-secondary">Nombre de la Caja <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-cash-register"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control border-start-0 @error('nombre') is-invalid @enderror" value="{{ old('nombre', $caja->nombre) }}">
                        </div>
                        @error('nombre')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-medium text-secondary">Estado</label>
                        <div class="form-check form-switch p-3 border rounded-3 bg-light shadow-sm">
                            <input type="hidden" name="estado" value="0">
                            <input type="checkbox" name="estado" id="estado" class="form-check-input ms-0 me-3" style="width: 2.5em; height: 1.2em; cursor: pointer;" value="1" {{ old('estado', $caja->estado) ? 'checked' : '' }}>
                            <label for="estado" class="form-check-label mb-0 fw-medium text-dark" style="cursor: pointer;">
                                Activa
                            </label>
                        </div>
                        @error('estado')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-between align-items-center border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer valores</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('cajas.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar Caja
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection