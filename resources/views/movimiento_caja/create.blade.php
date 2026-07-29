@extends('layouts.app')
@section('title', 'Nuevo Movimiento de Caja')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Registrar Movimiento</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('movimientos-caja.index') }}" class="text-decoration-none text-muted">Movimientos</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Nuevo</li>
            </ol>
        </div>
        <a href="{{ route('movimientos-caja.index') }}" class="btn btn-light border shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="card border-0 shadow-sm rounded-3 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom p-4">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-money-bill-wave text-primary me-2"></i>Detalle de la Operación
            </h5>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('movimientos-caja.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="sesion_caja_id" class="form-label fw-bold text-muted small text-uppercase">
                            Sesión de Caja Operativa <span class="text-danger">*</span>
                        </label>
                        @if($sesionesAbiertas->isEmpty())
                            <div class="alert alert-warning border-0 shadow-sm mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>No hay sesiones de caja abiertas actualmente.
                            </div>
                        @else
                            <select name="sesion_caja_id" id="sesion_caja_id" class="form-select shadow-sm @error('sesion_caja_id') is-invalid @enderror" required>
                                <option value="">Seleccione el terminal...</option>
                                @foreach($sesionesAbiertas as $sesion)
                                    <option value="{{ $sesion->id }}" @selected(old('sesion_caja_id') == $sesion->id)>
                                        Turno #{{ $sesion->id }} - {{ $sesion->caja?->nombre }} (Operador: {{ $sesion->user?->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('sesion_caja_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="tipo" class="form-label fw-bold text-muted small text-uppercase">Tipo de Flujo <span class="text-danger">*</span></label>
                        <select name="tipo" id="tipo" class="form-select shadow-sm @error('tipo') is-invalid @enderror" required>
                            <option value="INGRESO" @selected(old('tipo', 'INGRESO') === 'INGRESO')>Ingreso (+)</option>
                            <option value="EGRESO" @selected(old('tipo') === 'EGRESO')>Egreso (-)</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="origen" class="form-label fw-bold text-muted small text-uppercase">Categoría <span class="text-danger">*</span></label>
                        <select name="origen" id="origen" class="form-select shadow-sm @error('origen') is-invalid @enderror" required>
                            <option value="">Seleccione origen...</option>
                            <option value="INGRESO_MANUAL" @selected(old('origen') === 'INGRESO_MANUAL')>Ingreso extra manual</option>
                            <option value="EGRESO_MANUAL" @selected(old('origen') === 'EGRESO_MANUAL')>Egreso / Gasto manual</option>
                            <option value="AJUSTE" @selected(old('origen') === 'AJUSTE')>Ajuste de sistema</option>
                            <option value="ANULACION" @selected(old('origen') === 'ANULACION')>Anulación</option>
                        </select>
                        @error('origen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="monto" class="form-label fw-bold text-muted small text-uppercase">Importe <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-light text-muted">S/</span>
                            <input type="number" step="0.01" min="0.01" name="monto" id="monto" class="form-control font-monospace fw-bold @error('monto') is-invalid @enderror" value="{{ old('monto') }}" placeholder="0.00" required>
                        </div>
                        @error('monto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="descripcion" class="form-label fw-bold text-muted small text-uppercase">Justificación <span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control shadow-sm @error('descripcion') is-invalid @enderror" placeholder="Motivo o detalle de la operación..." required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('movimientos-caja.index') }}" class="btn btn-light px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" {{ $sesionesAbiertas->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-save me-2"></i>Procesar Movimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection