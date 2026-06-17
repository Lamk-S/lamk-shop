@extends('layouts.app')
@section('title', 'Abrir Sesión de Caja')

@push('css')
<style>
    .page-title {
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .glass-card {
        border: 0;
        border-radius: 1.25rem;
        box-shadow: 0 0.5rem 1.5rem rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .soft-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid rgba(148, 163, 184, .18);
    }

    .summary-box {
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
        border: 1px solid rgba(59, 130, 246, .15);
        border-radius: 1rem;
        padding: 1rem;
    }

    .summary-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        font-weight: 700;
        margin-bottom: .25rem;
    }

    .summary-value {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        word-break: break-word;
    }

    .section-title {
        font-size: .9rem;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .help-text {
        font-size: .85rem;
        color: #64748b;
    }

    .sticky-actions {
        position: sticky;
        bottom: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.7), #fff 40%);
        backdrop-filter: blur(8px);
        z-index: 2;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="page-title text-dark mb-0">Abrir Sesión de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item">
                    <a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none">Sesiones de Caja</a>
                </li>
                <li class="breadcrumb-item active">Abrir sesión</li>
            </ol>
        </div>
        <div class="mt-3 mt-md-0">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                <i class="fas fa-lock-open me-1"></i> Operación de apertura
            </span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card glass-card">
                <div class="card-header soft-header p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                            <i class="fa-solid fa-lock-open"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Datos de apertura</h5>
                            <div class="help-text">Selecciona una caja, revisa su fondo y confirma la apertura.</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('sesiones-caja.store') }}" method="post">
                        @csrf

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="caja_id" class="form-label fw-semibold text-secondary">
                                    Caja <span class="text-danger">*</span>
                                </label>
                                <select name="caja_id" id="caja_id"
                                        class="form-select form-select-lg @error('caja_id') is-invalid @enderror">
                                    <option value="">Seleccione una caja...</option>
                                    @foreach($cajas as $caja)
                                        <option
                                            value="{{ $caja->id }}"
                                            data-nombre="{{ $caja->nombre }}"
                                            data-fondo="{{ number_format((float) $caja->fondo_fijo, 2, '.', '') }}"
                                            @selected(old('caja_id') == $caja->id)
                                        >
                                            {{ $caja->nombre }} — Fondo S/ {{ number_format($caja->fondo_fijo, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('caja_id')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                                <div class="help-text mt-2">
                                    Solo se muestran cajas activas y solo puede existir una sesión abierta por caja y por usuario.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="saldo_inicial" class="form-label fw-semibold text-secondary">
                                    Fondo inicial / saldo de apertura
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">S/</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="saldo_inicial"
                                        id="saldo_inicial"
                                        class="form-control border-start-0 @error('saldo_inicial') is-invalid @enderror"
                                        value="{{ old('saldo_inicial') }}"
                                        placeholder="Se completa automáticamente"
                                    >
                                </div>
                                @error('saldo_inicial')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                                <div class="help-text mt-2">
                                    Se recomienda mantener el fondo fijo de la caja para facilitar el vuelto y el control diario.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="observacion_apertura" class="form-label fw-semibold text-secondary">
                                    Observación de apertura
                                </label>
                                <textarea
                                    name="observacion_apertura"
                                    id="observacion_apertura"
                                    rows="4"
                                    class="form-control @error('observacion_apertura') is-invalid @enderror"
                                    placeholder="Ej. Caja abierta para turno de mañana, revisión de billetes, cambio inicial, etc."
                                >{{ old('observacion_apertura') }}</textarea>
                                @error('observacion_apertura')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info border-0 shadow-sm mb-0">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-circle-info me-2 mt-1"></i>
                                        <div>
                                            La apertura de caja no representa una venta ni una ganancia.
                                            Es el monto inicial con el que la caja empieza a operar durante el turno.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sticky-actions mt-5 pt-4 border-top">
                            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                                <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light px-4">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="fas fa-save me-2"></i>Abrir Sesión
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card glass-card mb-4">
                <div class="card-body p-4">
                    <h6 class="section-title mb-3">Resumen de caja seleccionada</h6>

                    <div class="summary-box mb-3">
                        <div class="summary-label">Caja</div>
                        <div class="summary-value" id="resumen-caja">Ninguna seleccionada</div>
                    </div>

                    <div class="summary-box mb-3">
                        <div class="summary-label">Fondo fijo</div>
                        <div class="summary-value" id="resumen-fondo">S/ 0.00</div>
                    </div>

                    <div class="summary-box">
                        <div class="summary-label">Estado esperado</div>
                        <div class="summary-value" id="resumen-estado">Esperando selección</div>
                    </div>
                </div>
            </div>

            <div class="card glass-card">
                <div class="card-body p-4">
                    <h6 class="section-title mb-3">Recomendación operativa</h6>
                    <p class="mb-0 text-muted">
                        Abre la sesión con el fondo fijo exacto de la caja para evitar diferencias al cierre.
                        La caja debe comenzar limpia cada día y la utilidad real debe controlarse en tesorería.
                    </p>
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
            const option = selectCaja.options[selectCaja.selectedIndex];

            if (!option || !option.value) {
                resumenCaja.textContent = 'Ninguna seleccionada';
                resumenFondo.textContent = 'S/ 0.00';
                resumenEstado.textContent = 'Esperando selección';
                return;
            }

            const nombre = option.dataset.nombre || option.textContent.trim();
            const fondo = parseFloat(option.dataset.fondo || '0');

            resumenCaja.textContent = nombre;
            resumenFondo.textContent = 'S/ ' + fondo.toFixed(2);
            resumenEstado.textContent = 'Lista para abrir';
            inputSaldo.value = fondo.toFixed(2);
        }

        selectCaja.addEventListener('change', updateSummary);

        if (selectCaja.value) {
            updateSummary();
        }
    });
</script>
@endpush
@endsection