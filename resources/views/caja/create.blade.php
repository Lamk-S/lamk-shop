@extends('layouts.app')

@section('title', 'Crear Caja')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Nueva Caja</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cajas.index') }}" class="text-decoration-none">Cajas</a></li>
            <li class="breadcrumb-item active">Crear registro</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 700px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-cash-register text-primary me-2"></i>Datos de la Caja
            </h5>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('cajas.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label fw-medium text-secondary">Nombre de la Caja <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-cash-register"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control border-start-0 @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej. Caja Principal">
                        </div>
                        @error('nombre')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="fondo_fijo" class="form-label fw-medium text-secondary">Fondo fijo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input type="number" step="0.01" min="0" name="fondo_fijo" id="fondo_fijo" class="form-control border-start-0 @error('fondo_fijo') is-invalid @enderror" value="{{ old('fondo_fijo', 100) }}">
                        </div>
                        @error('fondo_fijo')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-medium text-secondary">Estado</label>
                        <div class="form-check form-switch p-3 border rounded-3 bg-light shadow-sm">
                            <input type="hidden" name="estado" value="0">
                            <input type="checkbox" name="estado" id="estado" class="form-check-input ms-0 me-3" style="width: 2.5em; height: 1.2em; cursor: pointer;" value="1" {{ old('estado', 1) ? 'checked' : '' }}>
                            <label for="estado" class="form-check-label mb-0 fw-medium text-dark" style="cursor: pointer;">
                                Activa
                            </label>
                        </div>
                        @error('estado')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('cajas.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-2"></i>Guardar Caja
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection