@extends('layouts.app')
@section('title', 'Abrir Sesión de Caja')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Abrir Sesión de Caja</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none text-muted">Auditoría de Cajas</a></li>
                    <li class="breadcrumb-item active fw-medium text-dark">Abrir turno</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light shadow-sm border px-3">
                <i class="fas fa-arrow-left me-2"></i>Volver al listado
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Formulario Principal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-lock-open"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-semibold text-dark fs-5">Datos de apertura</h5>
                        <small class="text-muted">Seleccione una caja disponible para iniciar el turno de ventas.</small>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('sesiones-caja.store') }}" method="post" id="form-apertura">
                        @csrf
                        <div class="row g-4">
                            <!-- Selección de Caja -->
                            <div class="col-12">
                                <label for="caja_id" class="form-label fw-medium text-secondary small">
                                    Terminal / Caja <span class="text-danger">*</span>
                                </label>
                                @if($cajas->isEmpty())
                                    <div class="alert alert-warning mb-0 border-0 shadow-sm">
                                        <i class="fas fa-exclamation-triangle me-2"></i>No hay cajas disponibles en este momento. Todas están ocupadas o inactivas.
                                    </div>
                                @else
                                    <select name="caja_id" id="caja_id" class="form-select form-select-lg shadow-sm @error('caja_id') is-invalid @enderror" required>
                                        <option value="">Seleccione una caja disponible...</option>
                                        @foreach($cajas as $caja)
                                            <option 
                                                value="{{ $caja->id }}" 
                                                data-nombre="{{ $caja->nombre }}" 
                                                data-fondo="{{ number_format((float) $caja->fondo_fijo, 2, '.', '') }}"
                                                @selected(old('caja_id') == $caja->id)
                                            >
                                                {{ $caja->nombre }} (Fondo base: S/ {{ number_format($caja->fondo_fijo, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('caja_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <!-- Saldo Inicial -->
                            <div class="col-md-6">
                                <label for="saldo_inicial" class="form-label fw-medium text-secondary small">
                                    Fondo inicial / Sencillo (S/)
                                </label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-light text-muted">S/</span>
                                    <input 
                                        type="number" 
                                        step="0.01" min="0" 
                                        name="saldo_inicial" id="saldo_inicial" 
                                        class="form-control font-monospace fw-bold @error('saldo_inicial') is-invalid @enderror" 
                                        value="{{ old('saldo_inicial') }}" 
                                        placeholder="0.00"
                                        required
                                    >
                                </div>
                                <div class="form-text mt-1"><i class="fas fa-info-circle me-1"></i>Monto físico exacto con el que empieza el turno.</div>
                                @error('saldo_inicial')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Observaciones -->
                            <div class="col-md-6">
                                <label for="observacion_apertura" class="form-label fw-medium text-secondary small">
                                    Observación (Opcional)
                                </label>
                                <textarea 
                                    name="observacion_apertura" id="observacion_apertura" rows="2" 
                                    class="form-control shadow-sm @error('observacion_apertura') is-invalid @enderror" 
                                    placeholder="Detalles sobre billetes, requerimientos de cambio, etc."
                                >{{ old('observacion_apertura') }}</textarea>
                                @error('observacion_apertura')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" {{ $cajas->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-play me-2"></i>Iniciar Turno Operativo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Lateral Informativo -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-light border-bottom p-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-cash-register text-muted me-2"></i>Resumen de Apertura</h6>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted small fw-medium text-uppercase">Caja seleccionada</span>
                        <span class="fw-bold text-dark text-end" id="resumen-caja">Ninguna</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted small fw-medium text-uppercase">Fondo de sistema</span>
                        <span class="fw-bold text-primary font-monospace" id="resumen-fondo">S/ 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small fw-medium text-uppercase">Estado</span>
                        <span class="badge bg-secondary" id="resumen-estado">Esperando selección</span>
                    </div>
                </div>
            </div>

            <div class="alert alert-primary bg-primary bg-opacity-10 border-0 shadow-sm d-flex align-items-start gap-3">
                <i class="fas fa-shield-alt text-primary fs-4 mt-1"></i>
                <div>
                    <strong class="d-block text-primary mb-1">Responsabilidad de Caja</strong>
                    <span class="small text-dark text-opacity-75">
                        Al abrir la caja, usted se hace responsable del efectivo y las transacciones emitidas en este terminal hasta el cierre de su turno.
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectCaja = document.getElementById('caja_id');
        const inputSaldo = document.getElementById('saldo_inicial');
        const resumenCaja = document.getElementById('resumen-caja');
        const resumenFondo = document.getElementById('resumen-fondo');
        const resumenEstado = document.getElementById('resumen-estado');

        function updateSummary() {
            if (!selectCaja) return;
            
            const option = selectCaja.options[selectCaja.selectedIndex];
            if (!option || !option.value) {
                resumenCaja.textContent = 'Ninguna';
                resumenFondo.textContent = 'S/ 0.00';
                resumenEstado.className = 'badge bg-secondary';
                resumenEstado.textContent = 'Esperando selección';
                return;
            }

            const nombre = option.dataset.nombre;
            const fondo = parseFloat(option.dataset.fondo || '0');

            resumenCaja.textContent = nombre;
            resumenFondo.textContent = 'S/ ' + fondo.toFixed(2);
            resumenEstado.className = 'badge bg-success';
            resumenEstado.textContent = 'Lista para operar';
            
            inputSaldo.value = fondo.toFixed(2);
        }

        if(selectCaja) {
            selectCaja.addEventListener('change', updateSummary);
            if (selectCaja.value) updateSummary();
        }
    });
</script>
@endpush
@endsection