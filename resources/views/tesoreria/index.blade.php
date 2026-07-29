@extends('layouts.app')
@section('title', 'Control de Tesorería')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0 fs-3">Gestión de Tesorería</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item active fw-medium text-dark">Cuentas y Flujos Macro</li>
        </ol>
    </div>

    <!-- Panel de Indicadores (KPIs ERP Style) -->
    <div class="row g-3 mb-4">
        <!-- Caja Fuerte / Efectivo -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <i class="fa-solid fa-money-bill-wave position-absolute top-0 end-0 p-3 fs-1 text-success opacity-25"></i>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Caja Fuerte</div>
                    <h3 class="fw-bold text-success mb-1 font-monospace">
                        S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small mb-3 text-truncate pe-5">{{ $tesoreriaEfectivo?->nombre ?? 'No registrado' }}</div>
                    <span class="badge {{ ($tesoreriaEfectivo?->estado ?? false) ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2 py-1">
                        {{ ($tesoreriaEfectivo?->estado ?? false) ? 'Cuenta Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Banco -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <i class="fa-solid fa-building-columns position-absolute top-0 end-0 p-3 fs-1 text-primary opacity-25"></i>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Cuenta Bancaria</div>
                    <h3 class="fw-bold text-primary mb-1 font-monospace">
                        S/ {{ number_format((float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small mb-3 text-truncate pe-5">{{ $tesoreriaBanco?->nombre ?? 'No registrado' }}</div>
                    <span class="badge {{ ($tesoreriaBanco?->estado ?? false) ? 'bg-primary bg-opacity-10 text-primary border border-primary' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2 py-1">
                        {{ ($tesoreriaBanco?->estado ?? false) ? 'Cuenta Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Capital Total -->
        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-dark text-white overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <i class="fa-solid fa-vault position-absolute top-0 end-0 p-3 fs-1 text-light opacity-25"></i>
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">Capital Consolidado</div>
                    <h3 class="fw-bold text-white mb-2 font-monospace">
                        S/ {{ number_format((float) (($tesoreriaEfectivo?->saldo_actual ?? 0) + ($tesoreriaBanco?->saldo_actual ?? 0)), 2) }}
                    </h3>
                    <div class="text-white-50 small mt-4 border-top border-secondary pt-2">
                        <i class="fas fa-clock me-1"></i> Última sincronización: 
                        {{ collect([$tesoreriaEfectivo?->updated_at, $tesoreriaBanco?->updated_at])->filter()->max()?->format('d/m/Y H:i') ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Operativa: Filtros y Tabla -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Filtros -->
        <div class="card-body p-4 bg-light border-bottom">
            <form method="GET" action="{{ route('tesorerias.index') }}" id="filtro-form" class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Cuenta / Origen</label>
                    <select name="tesoreria_id" class="form-select shadow-sm">
                        <option value="">Todas las cuentas</option>
                        @foreach($tesorerias as $t)
                            <option value="{{ $t->id }}" @selected(request('tesoreria_id') == $t->id)>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Dirección</label>
                    <select name="tipo" class="form-select shadow-sm">
                        <option value="">Cualquiera</option>
                        <option value="INGRESO" @selected(request('tipo') === 'INGRESO')>Ingresos (+)</option>
                        <option value="EGRESO" @selected(request('tipo') === 'EGRESO')>Egresos (-)</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Naturaleza Comercial</label>
                    <select name="origen" class="form-select shadow-sm">
                        <option value="">Todos los orígenes</option>
                        @foreach(['CIERRE_CAJA','VENTA_EFECTIVO','VENTA_TARJETA','VENTA_TRANSFERENCIA','COMPRA_PRODUCTO','DEPOSITO','RETIRO','AJUSTE','ANULACION'] as $origen)
                            <option value="{{ $origen }}" @selected(request('origen') === $origen)>{{ str_replace('_', ' ', $origen) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-end justify-content-end gap-2">
                    <a href="{{ route('tesorerias.index') }}" class="btn btn-outline-secondary shadow-sm" title="Limpiar">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header bg-white p-3 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-list-check fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Libro Mayor de Transacciones</h5>
                <div class="text-muted small">Liquidaciones y flujos empresariales.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-secondary small text-uppercase">Fecha Op.</th>
                            <th class="text-secondary small text-uppercase">Cuenta Asignada</th>
                            <th class="text-secondary small text-uppercase">Responsable</th>
                            <th class="text-center text-secondary small text-uppercase">Flujo / Medio</th>
                            <th class="text-center text-secondary small text-uppercase">Origen</th>
                            <th class="text-secondary small text-uppercase">Glosa</th>
                            <th class="text-end text-secondary small text-uppercase">Importe</th>
                            <th class="text-end text-secondary small text-uppercase pe-4 bg-light border-start">Saldo Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td class="ps-4 text-muted">
                                    <div class="small fw-bold text-dark">{{ $item->created_at?->format('d/m/Y') }}</div>
                                    <div class="small" style="font-size: 0.75rem;">{{ $item->created_at?->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->tesoreria?->nombre ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><i class="fas fa-user-circle text-muted me-1"></i>{{ explode(' ', $item->user?->name ?? 'Sistema')[0] }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="mb-1">
                                        @if($item->tipo->value === 'INGRESO')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Ingreso</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">Egreso</span>
                                        @endif
                                    </div>
                                    <div class="small font-monospace text-muted fw-bold">{{ $item->medio }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1 text-uppercase">
                                        {{ str_replace('_', ' ', $item->origen->value) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate text-dark small" style="max-width: 250px;" title="{{ $item->descripcion }}">
                                        {{ $item->descripcion }}
                                    </div>
                                </td>
                                <td class="text-end fw-bold font-monospace fs-6 {{ $item->tipo->value === 'INGRESO' ? 'text-success' : 'text-danger' }}">
                                    {{ $item->tipo->value === 'INGRESO' ? '+' : '-' }} S/ {{ number_format((float) $item->monto, 2) }}
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark font-monospace fs-6 bg-light bg-opacity-50 border-start">
                                    S/ {{ number_format((float) $item->saldo_posterior, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-center text-muted">
                                    <i class="fas fa-file-invoice-dollar fs-1 text-light mb-3"></i>
                                    <h5 class="fw-semibold text-dark">Sin extractos</h5>
                                    <p class="mb-0">No se encontraron liquidaciones bajo estos filtros.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Mostrar:</label>
                    <select name="per_page" form="filtro-form" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                        @foreach([5, 10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                    <span class="text-muted small fw-medium ms-2">
                        Viendo <strong>{{ $movimientos->firstItem() ?? 0 }}</strong> a <strong>{{ $movimientos->lastItem() ?? 0 }}</strong> de <strong>{{ $movimientos->total() }}</strong>
                    </span>
                </div>
                <div>
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
            if(element.name !== 'per_page') {
                element.addEventListener('change', () => form.submit());
            }
        });
    });
</script>
@endpush