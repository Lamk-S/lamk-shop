@extends('layouts.app')
@section('title', 'Control de Tesorería')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .table-soft th { background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
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
            <h2 class="page-title mb-0">Gestión de Tesorería</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Cuentas y Flujos</li>
            </ol>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-4 opacity-10">
                        <i class="fa-solid fa-money-bill-wave fa-4x text-success"></i>
                    </div>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Caja Fuerte / Efectivo</div>
                    <h3 class="fw-bold text-success mb-1 font-monospace">
                        S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small mb-3">{{ $tesoreriaEfectivo?->nombre ?? 'No registrado' }}</div>
                    <span class="badge {{ ($tesoreriaEfectivo?->estado ?? false) ? 'bg-success bg-opacity-10 text-success border-success border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25' }} border px-3 py-1 rounded-pill">
                        {{ ($tesoreriaEfectivo?->estado ?? false) ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-4 opacity-10">
                        <i class="fa-solid fa-building-columns fa-4x text-primary"></i>
                    </div>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Cuenta Bancaria</div>
                    <h3 class="fw-bold text-primary mb-1 font-monospace">
                        S/ {{ number_format((float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small mb-3">{{ $tesoreriaBanco?->nombre ?? 'No registrado' }}</div>
                    <span class="badge {{ ($tesoreriaBanco?->estado ?? false) ? 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25' }} border px-3 py-1 rounded-pill">
                        {{ ($tesoreriaBanco?->estado ?? false) ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-dark text-white">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-4 opacity-10">
                        <i class="fa-solid fa-vault fa-4x text-light"></i>
                    </div>
                    <div class="text-light text-opacity-75 small text-uppercase fw-bold mb-1">Capital Total</div>
                    <h3 class="fw-bold text-white mb-2 font-monospace">
                        S/ {{ number_format((float) (($tesoreriaEfectivo?->saldo_actual ?? 0) + ($tesoreriaBanco?->saldo_actual ?? 0)), 2) }}
                    </h3>
                    <div class="text-light text-opacity-50 small mt-3">
                        <i class="fas fa-clock me-1"></i> Sincronizado:<br>
                        {{ collect([$tesoreriaEfectivo?->updated_at, $tesoreriaBanco?->updated_at])->filter()->max()?->format('d/m/Y H:i') ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('tesorerias.index') }}" id="filtro-form" class="row g-3 filters-row">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Cuenta Bancaria/Caja</label>
                    <select name="tesoreria_id" class="form-select shadow-sm">
                        <option value="">Todas las cuentas</option>
                        @foreach($tesorerias as $t)
                            <option value="{{ $t->id }}" @selected(request('tesoreria_id') == $t->id)>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Movimiento</label>
                    <select name="tipo" class="form-select shadow-sm">
                        <option value="">Ingresos y Egresos</option>
                        <option value="INGRESO" @selected(request('tipo') === 'INGRESO')>Ingresos (+)</option>
                        <option value="EGRESO" @selected(request('tipo') === 'EGRESO')>Egresos (-)</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Origen Comercial</label>
                    <select name="origen" class="form-select shadow-sm">
                        <option value="">Todos los orígenes</option>
                        @foreach(['CIERRE_CAJA','VENTA_EFECTIVO','VENTA_TARJETA','VENTA_TRANSFERENCIA','COMPRA_PRODUCTO','DEPOSITO','RETIRO','AJUSTE','ANULACION'] as $origen)
                            <option value="{{ $origen }}" @selected(request('origen') === $origen)>{{ str_replace('_', ' ', $origen) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3">
                    <label class="form-label">Mostrar</label>
                    <select name="per_page" class="form-select shadow-sm">
                        @foreach([5, 10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 d-flex align-items-end">
                    <a href="{{ route('tesorerias.index') }}" class="btn btn-outline-secondary w-100 fw-bold bg-white">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Libro Mayor de Transacciones</h5>
                <div class="text-muted small">Liquidaciones y flujos aprobados.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Fecha Operación</th>
                            <th>Cuenta Asignada</th>
                            <th>Responsable</th>
                            <th class="text-center">Tipo / Medio</th>
                            <th class="text-center">Origen</th>
                            <th>Concepto</th>
                            <th class="text-end">Importe</th>
                            <th class="text-end pe-4">S. Resultante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td class="ps-4 text-muted font-monospace fs-7">
                                    <div>{{ $item->created_at?->format('d/m/Y') }}</div>
                                    <div>{{ $item->created_at?->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->tesoreria?->nombre ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ explode(' ', $item->user?->name ?? 'N/A')[0] }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="mb-1">
                                        @if($item->tipo === 'INGRESO')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded">Ingreso</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded">Egreso</span>
                                        @endif
                                    </div>
                                    <div class="small font-monospace text-muted">{{ $item->medio }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1 rounded shadow-sm text-uppercase" style="font-size: 0.65rem;">
                                        {{ str_replace('_', ' ', $item->origen) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate text-dark" style="max-width: 200px;" title="{{ $item->descripcion }}">
                                        {{ $item->descripcion }}
                                    </div>
                                </td>
                                <td class="text-end fw-bold font-monospace fs-6 {{ $item->tipo === 'INGRESO' ? 'text-success' : 'text-danger' }}">
                                    {{ $item->tipo === 'INGRESO' ? '+' : '-' }} S/ {{ number_format((float) $item->monto, 2) }}
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark font-monospace fs-6 bg-light bg-opacity-50">
                                    S/ {{ number_format((float) $item->saldo_posterior, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-center">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 70px; height: 70px;">
                                            <i class="fas fa-file-invoice-dollar text-muted fs-2 opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Sin extractos</h6>
                                        <p class="text-muted small mb-0">No se encontraron liquidaciones bajo estos filtros.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small fw-medium">
                    Viendo {{ $movimientos->firstItem() ?? 0 }} a {{ $movimientos->lastItem() ?? 0 }} de {{ $movimientos->total() }} registros
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
        
        form.querySelectorAll('select').forEach(element => {
            element.addEventListener('change', () => form.submit());
        });
    });
</script>
@endpush