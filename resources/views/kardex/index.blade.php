@extends('layouts.app')
@section('title', 'Kardex de Inventario')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .main-card { border: 0; border-radius: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); overflow: hidden; background: #fff; }
    .card-gradient-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #edf2f7; }
    .table-custom th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    .table-custom td { vertical-align: middle; color: #334155; border-bottom: 1px solid #f1f5f9; }
    .tabular-nums { font-variant-numeric: tabular-nums; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; letter-spacing: -0.025em; }
    .cell-entrada { color: #059669; font-weight: 700; background-color: rgba(16, 185, 129, 0.05); border-left: 1px solid rgba(16, 185, 129, 0.1); }
    .cell-salida { color: #dc2626; font-weight: 700; background-color: rgba(239, 68, 68, 0.05); border-left: 1px solid rgba(239, 68, 68, 0.1); }
    .cell-saldo { font-weight: 800; color: #0f172a; background-color: #f8fafc; border-left: 2px solid #e2e8f0; border-right: 2px solid #e2e8f0; }
    .bootstrap-select > .dropdown-toggle { background-color: #fff !important; border: 1px solid #dee2e6 !important; border-radius: 0.375rem !important; padding: 0.375rem 0.75rem !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; color: #475569 !important; }
    .bootstrap-select > .dropdown-toggle:focus { outline: none !important; border-color: #86b7fe !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important; }
    .filter-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: 0.4rem; }
    .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 8px; transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-2px); }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Libro de Kardex</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Movimientos de Inventario</li>
            </ol>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary shadow-sm fw-medium" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimir Reporte</button>
        </div>
    </div>

    <div class="card main-card">
        <div class="card-header card-gradient-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-inner" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-boxes-stacked fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Auditoría de Stock en Tiempo Real</h5>
                    <div class="text-muted small mt-1">Rastrea cada unidad que entra o sale del almacén con precisión contable.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="p-4 bg-white border-bottom">
                <form method="GET" action="{{ route('kardex.index') }}" id="kardex-filter-form" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label for="q" class="filter-label">Búsqueda Rápida</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Código, nombre, SKU o variante...">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label for="producto_id" class="filter-label">Filtrar por Artículo</label>
                        <select name="producto_id" id="producto_id" class="selectpicker" data-width="100%" data-live-search="true" data-size="6" title="Todos los artículos">
                            <option value="">-- Catálogo Completo --</option>
                            @foreach($productos as $item)
                                <option value="{{ $item->id }}" @selected((string) request('producto_id') === (string) $item->id)>
                                    [{{ $item->codigo }}] {{ Str::limit($item->nombre, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="tipo_transaccion" class="filter-label">Tipo Transacción</label>
                        <select name="tipo_transaccion" id="tipo_transaccion" class="selectpicker" data-width="100%" title="Todas las op.">
                            <option value="">-- Cualquiera --</option>
                            <optgroup label="Ingresos">
                                @foreach(['COMPRA','APERTURA','DEVOLUCION','TRANSFERENCIA'] as $tipo)
                                    <option value="{{ $tipo }}" @selected(request('tipo_transaccion') === $tipo)>{{ $tipo }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Salidas / Ajustes">
                                @foreach(['VENTA','AJUSTE','ANULACION','MERMA','VENCIDO'] as $tipo)
                                    <option value="{{ $tipo }}" @selected(request('tipo_transaccion') === $tipo)>{{ $tipo }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="fecha" class="filter-label">Fecha de Op.</label>
                        <input type="date" name="fecha" id="fecha" class="form-control shadow-sm" value="{{ request('fecha') }}">
                    </div>

                    <div class="col-lg-1 col-md-12">
                        <a href="{{ route('kardex.index') }}" class="btn btn-light border w-100 fw-medium shadow-sm" data-bs-toggle="tooltip" title="Restablecer todos los filtros">
                            <i class="fas fa-eraser me-2 d-inline d-lg-none"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 250px;">Artículo y Variante</th>
                            <th class="text-center">Operación</th>
                            <th style="min-width: 200px;">Detalle / Justificación</th>
                            <th class="text-center" title="Unidades Ingresadas">Ingreso</th>
                            <th class="text-center" title="Unidades Extraídas">Salida</th>
                            <th class="text-center cell-saldo" title="Stock Final">Stock Final</th>
                            <th class="text-end">Costo Ref.</th>
                            <th>Ejecutor</th>
                            <th class="text-end">Registro</th>
                            <th class="text-center pe-4" style="width: 70px;">Doc.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kardex as $item)
                            @php
                                $tipo = $item->tipo_transaccion;
                                $badge = match ($tipo) {
                                    'COMPRA', 'APERTURA', 'TRANSFERENCIA' => 'success',
                                    'VENTA', 'MERMA', 'VENCIDO' => 'danger',
                                    'AJUSTE', 'ANULACION' => 'warning',
                                    'DEVOLUCION' => 'info',
                                    default => 'secondary',
                                };
                                $variante = $item->productoVariante;
                                $producto = $variante?->producto;
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $producto?->nombre ?? 'Artículo Invalido/Eliminado' }}</div>
                                    <div class="small text-muted mt-1 d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-secondary border px-2 py-1"><i class="fas fa-barcode me-1"></i>{{ $producto?->codigo ?? 'N/A' }}</span>
                                        @if($variante)
                                            <span class="badge bg-light text-primary border px-2 py-1"><i class="fas fa-tag me-1"></i>{{ $variante->codigo_variante ?? 'S/V' }}</span>
                                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-ruler me-1"></i>{{ $variante->talla?->nombre ?? 'U' }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} border border-{{ $badge }} border-opacity-25 px-2 py-1 rounded">
                                        {{ $tipo }}
                                    </span>
                                </td>

                                <td>
                                    <div class="text-dark small lh-sm" title="{{ $item->descripcion }}">{{ Str::limit($item->descripcion, 45) }}</div>
                                </td>

                                <td class="text-center cell-entrada tabular-nums fs-6">
                                    {{ $item->entrada > 0 ? '+'.number_format((int) $item->entrada, 0) : '-' }}
                                </td>

                                <td class="text-center cell-salida tabular-nums fs-6">
                                    {{ $item->salida > 0 ? '-'.number_format((int) $item->salida, 0) : '-' }}
                                </td>

                                <td class="text-center cell-saldo tabular-nums fs-5">
                                    {{ number_format((int) $item->saldo_posterior, 0) }}
                                </td>

                                <td class="text-end small fw-medium text-secondary tabular-nums">
                                    S/ {{ number_format((float) $item->costo_unitario, 2) }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center text-secondary" style="width: 24px; height: 24px; font-size: .65rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="small fw-bold text-dark">{{ Str::limit(explode(' ', $item->user?->name ?? 'Sistema')[0], 10) }}</span>
                                    </div>
                                </td>

                                <td class="text-end">
                                    <div class="small text-dark fw-bold">{{ optional($item->created_at)->format('d/m/Y') }}</div>
                                    <div class="small text-muted" style="font-size: 0.70rem;">{{ optional($item->created_at)->format('H:i:s') }}</div>
                                </td>

                                <td class="text-center pe-4">
                                    <a href="{{ route('kardex.show', $item) }}" class="btn btn-action btn-light border text-primary" data-bs-toggle="tooltip" title="Inspeccionar">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-box-open text-muted fs-2 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Inventario estático</h5>
                                        <p class="text-muted mb-0">No se encontraron movimientos registrados con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-light">
                <div class="d-flex align-items-center gap-3">
                    <form method="GET" action="{{ route('kardex.index') }}" class="d-flex align-items-center gap-2">
                        @foreach(request()->except('per_page', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="per_page_bottom" class="form-label mb-0 small fw-bold text-muted text-uppercase tracking-wider text-nowrap">Líneas:</label>
                        <select name="per_page" id="per_page_bottom" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                            @foreach([10, 15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </form>
                    <div class="text-muted small fw-medium border-start ps-3">
                        Viendo <span class="fw-bold text-dark">{{ $kardex->firstItem() ?? 0 }}</span> - <span class="fw-bold text-dark">{{ $kardex->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $kardex->total() }}</span> movs.
                    </div>
                </div>
                <div>
                    {{ $kardex->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('kardex-filter-form');
        $('.selectpicker').selectpicker();
        $('#producto_id, #tipo_transaccion').on('changed.bs.select', function () {
            filterForm.submit();
        });
        
        document.getElementById('fecha').addEventListener('change', function() {
            filterForm.submit();
        });
        
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush