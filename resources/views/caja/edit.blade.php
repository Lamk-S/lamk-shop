@extends('layouts.app')
@section('title', 'Editar Caja')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Modificar Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cajas.index') }}" class="text-decoration-none text-muted">Cajas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Actualización de datos</li>
            </ol>
        </div>
        <a href="{{ route('cajas.index') }}" class="btn btn-light border shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="card border-0 shadow-sm rounded-3 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-cash-register text-warning me-2"></i>Ficha Técnica del Terminal
            </h5>
            <span class="badge bg-light text-secondary border font-monospace fs-7">ID: {{ str_pad($caja->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('cajas.update', $caja) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label fw-bold text-muted small text-uppercase">
                            Código / Alias <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="codigo" id="codigo" class="form-control shadow-sm font-monospace text-uppercase @error('codigo') is-invalid @enderror" value="{{ old('codigo', $caja->codigo) }}" required>
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nombre" class="form-label fw-bold text-muted small text-uppercase">
                            Nombre descriptivo <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control shadow-sm @error('nombre') is-invalid @enderror" value="{{ old('nombre', $caja->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fondo_fijo" class="form-label fw-bold text-muted small text-uppercase">
                            Fondo fijo asignado <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input type="number" step="0.01" min="0" name="fondo_fijo" id="fondo_fijo" class="form-control border-start-0 font-monospace fw-bold @error('fondo_fijo') is-invalid @enderror" value="{{ old('fondo_fijo', $caja->fondo_fijo) }}" required>
                        </div>
                        @error('fondo_fijo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="estado" class="form-label fw-bold text-muted small text-uppercase">Estado Actual</label>
                        <select name="estado" id="estado" class="form-select shadow-sm @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $caja->estado) == 1)>Operativa (Activa)</option>
                            <option value="0" @selected(old('estado', $caja->estado) == 0)>Suspendida (Inactiva)</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-5 border-top pt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('cajas.index') }}" class="btn btn-light border px-4 shadow-sm">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i>Actualizar Caja
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection