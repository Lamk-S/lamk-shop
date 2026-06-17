@extends('layouts.app')
@section('title', 'Nuevo Movimiento de Caja')

@push('css')
<style>
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .14); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Nuevo Movimiento de Caja</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('movimientos-caja.index') }}" class="text-decoration-none">Movimientos de Caja</a></li>
            <li class="breadcrumb-item active">Crear registro</li>
        </ol>
    </div>

    <div class="card card-soft w-100 mx-auto" style="max-width: 900px;">
        <div class="card-header soft-header p-4">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-money-bill-wave text-primary me-2"></i>Datos del movimiento
            </h5>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('movimientos-caja.store') }}" method="post">
                @csrf

                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="sesion_caja_id" class="form-label fw-medium text-secondary">
                            Sesión de caja <span class="text-danger">*</span>
                        </label>
                        <select name="sesion_caja_id" id="sesion_caja_id" class="form-select @error('sesion_caja_id') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($sesionesAbiertas as $sesion)
                                <option value="{{ $sesion->id }}" @selected(old('sesion_caja_id') == $sesion->id)>
                                    #{{ $sesion->id }} - {{ $sesion->caja?->nombre }} / {{ $sesion->user?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sesion_caja_id')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tipo" class="form-label fw-medium text-secondary">
                            Tipo <span class="text-danger">*</span>
                        </label>
                        <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror">
                            <option value="INGRESO" @selected(old('tipo', 'INGRESO') === 'INGRESO')>Ingreso</option>
                            <option value="EGRESO" @selected(old('tipo') === 'EGRESO')>Egreso</option>
                        </select>
                        @error('tipo')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="origen" class="form-label fw-medium text-secondary">
                            Origen <span class="text-danger">*</span>
                        </label>
                        <select name="origen" id="origen" class="form-select @error('origen') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="INGRESO_MANUAL" @selected(old('origen') === 'INGRESO_MANUAL')>Ingreso manual</option>
                            <option value="EGRESO_MANUAL" @selected(old('origen') === 'EGRESO_MANUAL')>Egreso manual</option>
                            <option value="AJUSTE" @selected(old('origen') === 'AJUSTE')>Ajuste</option>
                            <option value="ANULACION" @selected(old('origen') === 'ANULACION')>Anulación</option>
                        </select>
                        @error('origen')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            El origen ayuda a clasificar el movimiento y mejora los reportes.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="monto" class="form-label fw-medium text-secondary">
                            Monto <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">S/</span>
                            <input type="number" step="0.01" min="0.01" name="monto" id="monto" class="form-control border-start-0 @error('monto') is-invalid @enderror" value="{{ old('monto') }}">
                        </div>
                        @error('monto')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="descripcion" class="form-label fw-medium text-secondary">
                            Descripción <span class="text-danger">*</span>
                        </label>
                        <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info border-0 shadow-sm mb-0">
                            Usa movimientos de caja solo para operaciones de efectivo dentro de la sesión activa.
                        </div>
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('movimientos-caja.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-2"></i>Guardar Movimiento
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection