@extends('layouts.app')

@section('title', 'Abrir Sesión de Caja')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Abrir Sesión de Caja</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none">Sesiones de Caja</a></li>
            <li class="breadcrumb-item active">Abrir sesión</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-lock-open text-primary me-2"></i>Nueva Sesión</h5>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('sesiones-caja.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label for="caja_id" class="form-label fw-medium text-secondary">Caja <span class="text-danger">*</span></label>
                        <select name="caja_id" id="caja_id" class="form-select @error('caja_id') is-invalid @enderror">
                            <option value="" selected disabled>Seleccione una caja...</option>
                            @foreach($cajas as $caja)
                                <option value="{{ $caja->id }}" {{ old('caja_id') == $caja->id ? 'selected' : '' }}>
                                    {{ $caja->nombre }} — Fondo S/ {{ number_format($caja->fondo_fijo, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('caja_id')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            El saldo inicial se tomará automáticamente desde el fondo fijo de la caja seleccionada.
                        </div>
                    </div>

                    <div class="col-12 mt-4 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-2"></i>Abrir Sesión
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection