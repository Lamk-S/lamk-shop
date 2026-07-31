@extends('layouts.app')
@section('title', 'Movimientos de Caja')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Movimientos de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Registro de Flujo</li>
            </ol>
        </div>

        @can('movimientos_caja')
            <div>
                <a href="{{ route('movimientos-caja.create') }}" class="btn btn-primary shadow-sm fw-medium">
                    <i class="fas fa-hand-holding-dollar me-2"></i>Registrar Movimiento
                </a>
            </div>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 bg-light">
            <form method="GET" action="{{ route('movimientos-caja.index') }}" id="filtro-form" class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Tipo de Flujo</label>
                    <select name="tipo" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="INGRESO" @selected(request('tipo') === 'INGRESO')>Ingresos (Entradas)</option>
                        <option value="EGRESO" @selected(request('tipo') === 'EGRESO')>Egresos (Salidas)</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Clasificación / Origen</label>
                    <select name="origen" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="APERTURA" @selected(request('origen') === 'APERTURA')>Fondo de Apertura</option>
                        <option value="VENTA" @selected(request('origen') === 'VENTA')>Ventas de Mostrador</option>
                        <option value="PAGO_PROVEEDOR" @selected(request('origen') === 'PAGO_PROVEEDOR')>Pago a Proveedores</option>
                        <option value="RETIRO" @selected(request('origen') === 'RETIRO')>Retiro/Traslado a Tesorería</option>
                        <option value="AJUSTE" @selected(request('origen') === 'AJUSTE')>Ajustes de Cuadre</option>
                    </select>
                </div>

                <div class="col-lg-4 col-md-8">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control shadow-sm" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold text-uppercase">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control shadow-sm" value="{{ request('fecha_hasta') }}">
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 d-flex align-items-end">
                    <a href="{{ route('movimientos-caja.index') }}" class="btn btn-outline-secondary w-100 shadow-sm" title="Limpiar filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Resultados -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-money-bill-transfer fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Transacciones de Cajón</h5>
                <div class="text-muted small">Trazabilidad del efectivo físico por terminal.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-secondary small text-uppercase">Sesión / Terminal</th>
                            <th class="text-secondary small text-uppercase">Operador</th>
                            <th class="text-center text-secondary small text-uppercase">Naturaleza</th>
                            <th class="text-secondary small text-uppercase">Categoría</th>
                            <th class="text-secondary small text-uppercase">Concepto</th>
                            <th class="text-end text-secondary small text-uppercase">Importe</th>
                            <th class="text-end text-secondary small text-uppercase pe-4">Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark font-monospace">#{{ str_pad($item->sesion_caja_id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="small text-muted"><i class="fas fa-desktop me-1"></i>{{ $item->sesionCaja?->caja?->nombre ?? 'Desconocido' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><i class="fas fa-user-circle text-muted me-1"></i>{{ explode(' ', $item->sesionCaja?->user?->name ?? 'N/A')[0] }}</div>
                                </td>
                                <td class="text-center">
                                    @if($item->tipo->value === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">Egreso</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1 text-uppercase">
                                        {{ str_replace('_', ' ', $item->origen->value ?? $item->origen) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-wrap small text-dark" style="max-width: 250px;" title="{{ $item->descripcion }}">
                                        {{ Str::limit($item->descripcion, 45) }}
                                    </div>
                                </td>
                                <td class="text-end font-monospace fs-6 {{ $item->tipo->value === 'INGRESO' ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                    {{ $item->tipo->value === 'INGRESO' ? '+' : '-' }} S/ {{ number_format((float) $item->monto, 2) }}
                                </td>
                                <td class="text-end pe-4 text-muted">
                                    <div class="small fw-bold text-dark">{{ $item->created_at?->format('d/m/Y') }}</div>
                                    <div class="small" style="font-size: 0.75rem;">{{ $item->created_at?->format('H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">
                                    <i class="fas fa-filter-circle-xmark fs-1 text-light mb-3"></i>
                                    <h5 class="fw-semibold text-dark">Sin registros</h5>
                                    <p class="mb-0">No se encontraron movimientos financieros con los filtros aplicados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label for="per_page" class="form-label mb-0 small fw-bold text-muted text-uppercase">Mostrar:</label>
                    <select name="per_page" id="per_page" form="filtro-form" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small ms-2">
                        Viendo <strong>{{ $movimientos->firstItem() ?? 0 }}</strong> a <strong>{{ $movimientos->lastItem() ?? 0 }}</strong> de <strong>{{ $movimientos->total() }}</strong>
                    </span>
                </div>
                <div class="pagination-custom">
                    {{ $movimientos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filtro-form');
        form.querySelectorAll('select, input[type="date"]').forEach(element => {
            if(element.id !== 'per_page') {
                element.addEventListener('change', () => form.submit());
            }
        });
    });
</script>
@endpush