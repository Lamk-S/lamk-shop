@extends('layouts.app')
@section('title', 'Configuración de Comprobantes')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .filters-row .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 0.3rem; }
    .table-actions .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .empty-state { padding: 3rem 1rem; }
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Series de Comprobantes</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Configuración SUNAT / Interna</li>
            </ol>
        </div>
    </div>

    <div class="card soft-card">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Control de Correlativos</h5>
                    <div class="text-muted small">Administra los folios de boletas, facturas y tickets del negocio.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('comprobantes.index') }}" id="filtro-comprobantes-form" class="row g-3 filters-row mb-4">
                <div class="col-lg-4 col-md-6">
                    <label for="q" class="form-label">Búsqueda rápida</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Serie (ej. F001), tipo o uso...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="tipo_comprobante" class="form-label">Tipo Doc.</label>
                    <select name="tipo_comprobante" id="tipo_comprobante" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        @foreach(['TICKET','BOLETA','FACTURA','NOTA_CREDITO','NOTA_DEBITO'] as $tipo)
                            <option value="{{ $tipo }}" @selected(request('tipo_comprobante') === $tipo)>{{ str_replace('_', ' ', $tipo) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label for="uso_comprobante" class="form-label">Operación</label>
                    <select name="uso_comprobante" id="uso_comprobante" class="form-select shadow-sm">
                        <option value="">Todas</option>
                        <option value="VENTA" @selected(request('uso_comprobante') === 'VENTA')>Ventas (Salidas)</option>
                        <option value="COMPRA" @selected(request('uso_comprobante') === 'COMPRA')>Compras (Ingresos)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                        <option value="eliminado" @selected(request('estado') === 'eliminado')>Papelera</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 d-flex justify-content-end align-items-end">
                    <a href="{{ route('comprobantes.index') }}" class="btn btn-outline-secondary w-100 fw-medium bg-white" title="Limpiar todos los filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>

            <div class="table-responsive rounded-3 border bg-white">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 180px;">Tipo de Documento</th>
                            <th style="min-width: 130px;">N° Serie</th>
                            <th style="min-width: 120px;">Uso</th>
                            <th class="text-center" style="width: 140px;">Correlativo Actual</th>
                            <th class="text-center" style="width: 130px;">Entorno</th>
                            <th class="text-center" style="width: 130px;">Estado</th>
                            @can('gestionar_comprobantes')
                                <th class="text-center pe-4" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comprobantes as $item)
                            @php
                                $tipo = strtoupper((string) $item->tipo_comprobante);
                                $tipoBadge = match ($tipo) {
                                    'TICKET' => 'secondary', 'BOLETA' => 'info', 'FACTURA' => 'primary',
                                    'NOTA_CREDITO' => 'warning', 'NOTA_DEBITO' => 'dark', default => 'light',
                                };
                                $usoBadge = $item->uso_comprobante === 'VENTA' ? 'success' : 'primary';
                                $ambienteBadge = $item->ambiente === 'PRODUCCION' ? 'danger' : 'secondary';
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ str_replace('_', ' ', $item->tipo_comprobante) }}</div>
                                    <div class="small text-muted font-monospace">ID SYS: {{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 font-monospace shadow-sm">{{ $item->serie }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $usoBadge }} bg-opacity-10 text-{{ $usoBadge }} border border-{{ $usoBadge }} border-opacity-25 px-3 py-1 rounded-pill">
                                        <i class="fas {{ $item->uso_comprobante === 'VENTA' ? 'fa-cart-arrow-down' : 'fa-truck-loading' }} me-1"></i>
                                        {{ $item->uso_comprobante === 'VENTA' ? 'Ventas' : 'Compras' }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold fs-6 font-monospace text-primary">
                                    {{ str_pad((int) $item->correlativo_actual, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $ambienteBadge }} bg-opacity-10 text-{{ $ambienteBadge }} border border-{{ $ambienteBadge }} border-opacity-25 px-2 py-1 rounded">
                                        {{ $item->ambiente === 'PRODUCCION' ? 'Producción (SUNAT)' : 'Pruebas' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if(!$item->trashed() && (int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill">Inactivo</span>
                                    @endif
                                </td>
                                @can('gestionar_comprobantes')
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm table-actions" role="group">
                                            <a href="{{ route('comprobantes.show', $item) }}" class="btn btn-light border text-info" title="Auditar serie">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comprobantes.edit', $item) }}" class="btn btn-light border text-primary" title="Editar configuración">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_comprobantes') ? 7 : 6 }}" class="py-5">
                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-file-invoice text-secondary fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">Sin configuración de comprobantes</h5>
                                        <p class="text-muted mb-0">No se encontraron series con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ route('comprobantes.index') }}" id="pagination-form" class="d-flex align-items-center gap-2">
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
                        Viendo {{ $comprobantes->firstItem() ?? 0 }} a {{ $comprobantes->lastItem() ?? 0 }} de {{ $comprobantes->total() }}
                    </span>
                </form>
                <div class="pagination-custom">
                    {{ $comprobantes->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filtro-comprobantes-form');
        const searchInput = document.getElementById('q');
        
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', () => filterForm.submit());
        });

        let typingTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => filterForm.submit(), 500);
        });

        document.getElementById('per_page').addEventListener('change', () => {
            document.getElementById('pagination-form').submit();
        });
    });
</script>
@endpush