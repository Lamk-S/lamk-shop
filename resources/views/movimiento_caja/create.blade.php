@extends('layouts.app')

@section('title', 'Crear Movimiento de Caja')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    @if(isset($sesionesAbiertas) && $sesionesAbiertas->isEmpty())
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No hay sesiones de caja abiertas disponibles para registrar movimientos.
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-money-bill text-primary me-2"></i>Datos del Movimiento
            </h5>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('movimientos-caja.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="sesion_caja_id" class="form-label fw-medium text-secondary">Sesión de Caja <span class="text-danger">*</span></label>
                        <select name="sesion_caja_id" id="sesion_caja_id" class="form-select @error('sesion_caja_id') is-invalid @enderror">
                            <option value="" selected disabled>Seleccione una sesión abierta...</option>
                            @foreach($sesionesAbiertas as $item)
                                <option value="{{ $item->id }}" @selected(old('sesion_caja_id') == $item->id)>
                                    {{ $item->caja?->nombre ?? 'Caja' }} - {{ $item->user?->name ?? 'Usuario' }}
                                </option>
                            @endforeach
                        </select>
                        @error('sesion_caja_id')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tipo" class="form-label fw-medium text-secondary">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror">
                            <option value="" selected disabled>Seleccione...</option>
                            <option value="INGRESO" @selected(old('tipo') == 'INGRESO')>INGRESO</option>
                            <option value="EGRESO" @selected(old('tipo') == 'EGRESO')>EGRESO</option>
                        </select>
                        @error('tipo')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="monto" class="form-label fw-medium text-secondary">Monto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                            <input type="number" name="monto" id="monto" class="form-control border-start-0 @error('monto') is-invalid @enderror" value="{{ old('monto') }}" min="0.01" step="0.01">
                        </div>
                        @error('monto')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="descripcion" class="form-label fw-medium text-secondary">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describa el motivo del movimiento...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush