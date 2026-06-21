@extends('layouts.app')
@section('title', 'Movimientos de Caja')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .table-soft thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .14); }
    .empty-state { padding: 3rem 1rem; }
    .filters-row .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 0.3rem; }
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-0">Movimientos de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Registro de Flujo</li>
            </ol>
        </div>

        @can('movimientos_caja')
            <div>
                <a href="{{ route('movimientos-caja.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-hand-holding-dollar me-2"></i>Registrar Movimiento
                </a>
            </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('movimientos-caja.index') }}" id="filtro-form" class="row g-3 filters-row">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Tipo de Flujo</label>
                    <select name="tipo" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="INGRESO" @selected(request('tipo') === 'INGRESO')>Ingresos (Entradas)</option>
                        <option value="EGRESO" @selected(request('tipo') === 'EGRESO')>Egresos (Salidas)</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Clasificación / Origen</label>
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
                    <div class="d-flex gap-2">
                        <div class="w-50">
                            <label class="form-label">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control shadow-sm p-2 fs-7" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="w-50">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control shadow-sm p-2 fs-7" value="{{ request('fecha_hasta') }}">
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 d-flex justify-content-end align-items-end">
                    <a href="{{ route('movimientos-caja.index') }}" class="btn btn-outline-secondary w-100 fw-medium bg-white" title="Limpiar filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-soft">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Transacciones de Cajón</h5>
                <div class="text-muted small">Trazabilidad del efectivo físico por terminal.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Sesión / Terminal</th>
                            <th>Operador</th>
                            <th class="text-center">Naturaleza</th>
                            <th>Categoría</th>
                            <th>Concepto</th>
                            <th class="text-end">Importe</th>
                            <th class="text-end pe-4">Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark font-monospace fs-7">#{{ str_pad($item->sesion_caja_id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="small text-muted">{{ $item->sesionCaja?->caja?->nombre ?? 'Terminal Desconocido' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ explode(' ', $item->sesionCaja?->user?->name ?? 'N/A')[0] }}</div>
                                </td>
                                <td class="text-center">
                                    @if($item->tipo === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded">Egreso</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1 rounded shadow-sm text-uppercase" style="font-size: 0.7rem;">
                                        {{ str_replace('_', ' ', $item->origen) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $item->descripcion }}">
                                        {{ $item->descripcion }}
                                    </div>
                                </td>
                                <td class="text-end font-monospace fs-6 {{ $item->tipo === 'INGRESO' ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                    {{ $item->tipo === 'INGRESO' ? '+' : '-' }} S/ {{ number_format((float) $item->monto, 2) }}
                                </td>
                                <td class="text-end pe-4 font-monospace fs-7 text-muted">
                                    <div>{{ $item->created_at?->format('d/m/Y') }}</div>
                                    <div>{{ $item->created_at?->format('H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-filter-circle-xmark text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Cajón sin registros</h5>
                                        <p class="text-muted mb-0">No se encontraron movimientos financieros con los filtros aplicados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <form method="GET" action="{{ route('movimientos-caja.index') }}" id="pagination-form" class="d-flex align-items-center gap-2">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm shadow-sm" style="width: auto;">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small fw-medium ms-2">
                        Viendo {{ $movimientos->firstItem() ?? 0 }} a {{ $movimientos->lastItem() ?? 0 }} de {{ $movimientos->total() }}
                    </span>
                </form>
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
        const pagForm = document.getElementById('pagination-form');
        
        form.querySelectorAll('select, input[type="date"]').forEach(element => {
            element.addEventListener('change', () => form.submit());
        });

        document.getElementById('per_page').addEventListener('change', () => {
            pagForm.submit();
        });
    });
</script>
@endpush