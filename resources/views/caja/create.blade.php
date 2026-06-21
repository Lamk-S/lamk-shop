@extends('layouts.app')
@section('title', 'Nueva Caja')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="page-title mb-0">Nueva Caja</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cajas.index') }}" class="text-decoration-none text-muted">Cajas</a></li>
            <li class="breadcrumb-item active fw-medium text-dark">Crear registro</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-cash-register text-primary me-2"></i>Datos de la caja
            </h5>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('cajas.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label fw-medium text-secondary">
                            Nombre de la caja <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej. Caja Principal">
                        @error('nombre')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fondo_fijo" class="form-label fw-medium text-secondary">
                            Fondo fijo <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">S/</span>
                            <input type="number" step="0.01" min="0" name="fondo_fijo" id="fondo_fijo" class="form-control border-start-0 @error('fondo_fijo') is-invalid @enderror" value="{{ old('fondo_fijo', 100) }}">
                        </div>
                        @error('fondo_fijo')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
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