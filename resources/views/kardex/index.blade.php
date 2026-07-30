@extends('layouts.app')
@section('title', 'Kardex de Inventario')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Libro de Kardex</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Movimientos de Inventario</li>
            </ol>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir Reporte
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="fa-solid fa-boxes-stacked fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-semibold text-dark">Auditoría de Stock</h5>
                    <div class="text-muted small">Rastreo contable de ingresos y salidas del almacén.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Formulario de Filtros -->
            <div class="p-4 bg-light border-bottom">
                <form method="GET" action="{{ route('kardex.index') }}" id="kardex-filter-form" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label for="q" class="form-label fw-bold text-muted small text-uppercase">Búsqueda Rápida</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Código, nombre o SKU...">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label for="producto_id" class="form-label fw-bold text-muted small text-uppercase">Artículo</label>
                        <select name="producto_id" id="producto_id" class="selectpicker form-control shadow-sm" data-live-search="true" title="Todos" data-size="5">
                            <option value="">-- Catálogo Completo --</option>
                            @foreach($productos as $item)
                                <option value="{{ $item->id }}" @selected((string) request('producto_id') === (string) $item->id)>
                                    [{{ $item->codigo }}] {{ Str::limit($item->nombre, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="tipo_transaccion" class="form-label fw-bold text-muted small text-uppercase">Transacción</label>
                        <select name="tipo_transaccion" id="tipo_transaccion" class="selectpicker form-control shadow-sm" title="Todas" data-size="5">
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
                        <label for="fecha" class="form-label fw-bold text-muted small text-uppercase">Fecha de Op.</label>
                        <input type="date" name="fecha" id="fecha" class="form-control shadow-sm" value="{{ request('fecha') }}">
                    </div>

                    <div class="col-lg-1 col-md-12">
                        <a href="{{ route('kardex.index') }}" class="btn btn-outline-secondary w-100 shadow-sm" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-secondary small text-uppercase">Artículo y Variante</th>
                            <th class="text-center text-secondary small text-uppercase">Operación</th>
                            <th class="text-secondary small text-uppercase">Detalle</th>
                            <th class="text-center text-secondary small text-uppercase">Ingreso</th>
                            <th class="text-center text-secondary small text-uppercase">Salida</th>
                            <th class="text-center bg-secondary bg-opacity-10 text-dark small text-uppercase border-start border-end">Stock Final</th>
                            <th class="text-end text-secondary small text-uppercase">Costo Ref.</th>
                            <th class="text-secondary small text-uppercase">Ejecutor</th>
                            <th class="text-end text-secondary small text-uppercase pe-4">Registro</th>
                            <th class="text-center pe-4" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kardex as $item)
                            @php
                                $tipo = $item->tipo_transaccion->value ?? $item->tipo_transaccion;
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
                                    <div class="fw-semibold text-dark">{{ $producto?->nombre ?? 'Artículo Invalido/Eliminado' }}</div>
                                    <div class="small text-muted mt-1">
                                        <span class="border rounded px-1 me-1"><i class="fas fa-barcode me-1"></i>{{ $producto?->codigo ?? 'N/A' }}</span>
                                        @if($variante)
                                            <span class="border rounded px-1 text-primary me-1">{{ $variante->codigo_variante ?? 'S/V' }}</span>
                                            <span class="border rounded px-1">{{ $variante->talla?->nombre ?? 'U' }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} border border-{{ $badge }} px-2 py-1">
                                        {{ $tipo }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-dark text-wrap" style="max-width: 200px;" title="{{ $item->descripcion }}">{{ Str::limit($item->descripcion, 45) }}</div>
                                </td>
                                <td class="text-center font-monospace text-success fw-bold">
                                    {{ $item->entrada > 0 ? '+'.number_format((int) $item->entrada, 0) : '-' }}
                                </td>
                                <td class="text-center font-monospace text-danger fw-bold">
                                    {{ $item->salida > 0 ? '-'.number_format((int) $item->salida, 0) : '-' }}
                                </td>
                                <td class="text-center font-monospace fw-bold fs-6 bg-light border-start border-end">
                                    {{ number_format((int) $item->saldo_posterior, 0) }}
                                </td>
                                <td class="text-end font-monospace small text-muted">
                                    S/ {{ number_format((float) $item->costo_unitario, 2) }}
                                </td>
                                <td>
                                    <div class="small fw-medium text-dark"><i class="fas fa-user-circle text-muted me-1"></i>{{ Str::limit(explode(' ', $item->user?->name ?? 'Sistema')[0], 10) }}</div>
                                </td>
                                <td class="text-end">
                                    <div class="small text-dark fw-bold">{{ optional($item->created_at)->format('d/m/Y') }}</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">{{ optional($item->created_at)->format('H:i:s') }}</div>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('kardex.show', $item) }}" class="btn btn-sm btn-light border text-primary shadow-sm" title="Inspeccionar">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-5 text-center text-muted">
                                    <i class="fas fa-box-open fs-1 text-light mb-3"></i>
                                    <h5 class="fw-semibold text-dark">Sin movimientos</h5>
                                    <p class="mb-0">No se encontraron registros con los filtros actuales.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="p-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <!-- Se eliminó el form redundante, se usa el atributo form HTML5 -->
                    <div class="d-flex align-items-center gap-2">
                        <label for="per_page_bottom" class="form-label mb-0 small fw-bold text-muted">MOSTRAR:</label>
                        <select name="per_page" id="per_page_bottom" class="form-select form-select-sm shadow-sm" form="kardex-filter-form" onchange="this.form.submit()">
                            @foreach([10, 15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-muted small border-start ps-3">
                        Viendo <strong>{{ $kardex->firstItem() ?? 0 }}</strong> al <strong>{{ $kardex->lastItem() ?? 0 }}</strong> de <strong>{{ $kardex->total() }}</strong>
                    </div>
                </div>
                <div class="pagination-custom">
                    {{ $kardex->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
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
    });
</script>
@endpush