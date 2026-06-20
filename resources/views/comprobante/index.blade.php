@extends('layouts.app')
@section('title', 'Comprobantes')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .8rem; white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .filters-row .form-label { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
    .table-actions .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .empty-state { padding: 3rem 1rem; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title text-dark mb-0">Comprobantes</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Comprobantes</li>
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
                    <h5 class="mb-0 fw-bold text-dark">Series registradas</h5>
                    <div class="text-muted small">Administra series, usos y correlativos para ventas y compras.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('comprobantes.index') }}" class="row g-3 filters-row mb-4 bg-light rounded-3 p-3 border">
                <div class="col-lg-4 col-md-6">
                    <label for="q" class="form-label">Buscar</label>
                    <input type="search" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Tipo, serie, uso o ambiente...">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="tipo_comprobante" class="form-label">Tipo</label>
                    <select name="tipo_comprobante" id="tipo_comprobante" class="form-select">
                        <option value="">Todos</option>
                        @foreach(['TICKET','BOLETA','FACTURA','NOTA_CREDITO','NOTA_DEBITO'] as $tipo)
                            <option value="{{ $tipo }}" @selected(request('tipo_comprobante') === $tipo)>{{ str_replace('_', ' ', $tipo) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="uso_comprobante" class="form-label">Uso</label>
                    <select name="uso_comprobante" id="uso_comprobante" class="form-select">
                        <option value="">Todos</option>
                        <option value="VENTA" @selected(request('uso_comprobante') === 'VENTA')>Venta</option>
                        <option value="COMPRA" @selected(request('uso_comprobante') === 'COMPRA')>Compra</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                        <option value="eliminado" @selected(request('estado') === 'eliminado')>Eliminados</option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-6">
                    <label for="per_page" class="form-label">Ver</label>
                    <select name="per_page" id="per_page" class="form-select">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 align-items-end">
                    <a href="{{ route('comprobantes.index') }}" class="btn btn-light fw-medium border">Limpiar</a>
                    <button type="submit" class="btn btn-primary fw-medium">
                        <i class="fas fa-filter me-2"></i>Aplicar filtros
                    </button>
                </div>
            </form>

            <div class="table-responsive rounded-3 border">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Tipo</th>
                            <th style="min-width: 130px;">Serie</th>
                            <th style="min-width: 120px;">Uso</th>
                            <th class="text-center" style="width: 140px;">Correlativo</th>
                            <th class="text-center" style="width: 130px;">Ambiente</th>
                            <th class="text-center" style="width: 130px;">Estado</th>
                            @can('gestionar_comprobantes')
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comprobantes as $item)
                            @php
                                $tipo = strtoupper((string) $item->tipo_comprobante);
                                $tipoBadge = match ($tipo) {
                                    'TICKET' => 'secondary',
                                    'BOLETA' => 'info',
                                    'FACTURA' => 'primary',
                                    'NOTA_CREDITO' => 'warning',
                                    'NOTA_DEBITO' => 'dark',
                                    default => 'light',
                                };
                                $usoBadge = $item->uso_comprobante === 'VENTA' ? 'success' : 'primary';
                                $ambienteBadge = $item->ambiente === 'PRODUCCION' ? 'dark' : 'secondary';
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ str_replace('_', ' ', $item->tipo_comprobante) }}</div>
                                    <div class="small text-muted">ID: {{ $item->id }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-3 py-2">{{ $item->serie }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $usoBadge }} bg-opacity-10 text-{{ $usoBadge }} border border-{{ $usoBadge }} border-opacity-25 px-3 py-2 rounded-pill">
                                        {{ $item->uso_comprobante === 'VENTA' ? 'Venta' : 'Compra' }}
                                    </span>
                                </td>
                                <td class="text-center fw-semibold">
                                    {{ number_format((int) $item->correlativo_actual, 0) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $ambienteBadge }} bg-opacity-10 text-{{ $ambienteBadge }} border border-{{ $ambienteBadge }} border-opacity-25 px-3 py-2 rounded-pill">
                                        {{ $item->ambiente === 'PRODUCCION' ? 'Producción' : 'Simulado' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if(!$item->trashed() && (int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                                    @endif
                                </td>
                                @can('gestionar_comprobantes')
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm table-actions" role="group">
                                            <a href="{{ route('comprobantes.show', $item) }}" class="btn btn-outline-secondary text-info border-light" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('comprobantes.edit', $item) }}" class="btn btn-outline-secondary text-primary border-light" title="Editar">
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
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-receipt text-secondary fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No se encontraron comprobantes</h5>
                                        <p class="text-muted mb-0">Prueba otro filtro o registra una nueva serie de comprobante.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <div class="text-muted small fw-medium">
                    Mostrando <span class="fw-bold text-dark">{{ $comprobantes->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $comprobantes->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $comprobantes->total() }}</span> registros
                </div>
                <div class="pagination-custom">
                    {{ $comprobantes->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection