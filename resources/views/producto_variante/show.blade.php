@extends('layouts.app')
@section('title', 'Detalle de Variante')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .section-title { font-size: .82rem; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
    .data-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; font-weight: 800; margin-bottom: .25rem; display: block; }
    .data-value { font-weight: 600; color: #1e293b; font-size: 1rem; word-break: break-word; }
    .summary-box { background: #f8fafc; border: 1px solid rgba(148, 163, 184, .18); border-radius: 1rem; padding: 1rem; height: 100%; }
</style>
@endpush

@section('content')
@php
    $producto = $productoVariante->loadMissing(['producto.marca', 'talla'])->producto;
    $marca = $producto?->marca;
    $estadoActivo = !$productoVariante->trashed() && (int) $productoVariante->estado === 1;
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title text-dark mb-0">Detalle de Variante</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('producto-variantes.index') }}" class="text-decoration-none">Variantes</a></li>
                <li class="breadcrumb-item active">Ficha de registro</li>
            </ol>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('producto-variantes.edit', $productoVariante) }}" class="btn btn-warning shadow-sm rounded-3 px-4 fw-medium">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="{{ route('producto-variantes.index') }}" class="btn btn-light shadow-sm rounded-3 px-4 fw-medium">
                Volver
            </a>
        </div>
    </div>

    <div class="card soft-card mx-auto" style="max-width: 1100px;">
        <div class="card-header soft-header p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-barcode fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">{{ $productoVariante->codigo_variante }}</h4>
                        <div class="text-muted small">
                            {{ $producto?->nombre ?? 'Producto desvinculado' }} · {{ $marca?->nombre ?? 'Sin marca' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @if($estadoActivo)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                    @endif
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">ID: {{ $productoVariante->id }}</span>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="summary-box h-100">
                        <div class="section-title mb-3">Información de la variante</div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <span class="data-label">Producto</span>
                                <div class="data-value">{{ $producto?->nombre ?? 'Sin producto' }}</div>
                            </div>

                            <div class="col-md-6">
                                <span class="data-label">Marca</span>
                                <div class="data-value">{{ $marca?->nombre ?? 'Sin marca' }}</div>
                            </div>

                            <div class="col-md-6">
                                <span class="data-label">Tipo de producto</span>
                                <div class="data-value">
                                    {{ $producto ? ucfirst(strtolower($producto->tipo_producto)) : 'N/A' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <span class="data-label">Talla</span>
                                <div class="data-value">
                                    {{ optional($productoVariante->talla)->codigo ?? '-' }} - {{ optional($productoVariante->talla)->nombre ?? 'Sin talla' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <span class="data-label">Código de barra</span>
                                <div class="data-value">{{ $productoVariante->codigo_barra ?: 'No registrado' }}</div>
                            </div>

                            <div class="col-md-6">
                                <span class="data-label">Código variante</span>
                                <div class="data-value">{{ $productoVariante->codigo_variante }}</div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded-4 border">
                            <div class="section-title mb-3">Estado operativo</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <span class="data-label">Stock actual</span>
                                    <div class="data-value text-primary fs-4">{{ number_format((float) $productoVariante->stock_actual, 0) }} unid.</div>
                                </div>
                                <div class="col-md-4">
                                    <span class="data-label">Stock mínimo</span>
                                    <div class="data-value text-warning fs-4">{{ number_format((float) $productoVariante->stock_minimo, 0) }} unid.</div>
                                </div>
                                <div class="col-md-4">
                                    <span class="data-label">Diferencia</span>
                                    <div class="data-value fs-4 {{ $productoVariante->stock_actual <= $productoVariante->stock_minimo ? 'text-danger' : 'text-success' }}">
                                        {{ number_format((float) ($productoVariante->stock_actual - $productoVariante->stock_minimo), 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($productoVariante->stock_actual <= $productoVariante->stock_minimo)
                            <div class="alert alert-danger border-0 shadow-sm mt-4 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                El stock se encuentra en nivel crítico o por debajo del mínimo permitido.
                            </div>
                        @else
                            <div class="alert alert-success border-0 shadow-sm mt-4 mb-0">
                                <i class="fas fa-shield-check me-2"></i>
                                El stock se mantiene en un nivel saludable.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="summary-box h-100">
                        <div class="section-title mb-3">Trazabilidad</div>

                        <div class="row g-4">
                            <div class="col-12">
                                <span class="data-label">Creado</span>
                                <div class="data-value">{{ $productoVariante->created_at?->format('d/m/Y H:i') ?? 'N/D' }}</div>
                            </div>
                            <div class="col-12">
                                <span class="data-label">Última actualización</span>
                                <div class="data-value">{{ $productoVariante->updated_at?->format('d/m/Y H:i') ?? 'N/D' }}</div>
                            </div>
                            <div class="col-12">
                                <span class="data-label">Estado de registro</span>
                                <div>
                                    @if($estadoActivo)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Operativo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Bloqueado / Inactivo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <span class="data-label">Observación funcional</span>
                                <div class="text-muted">
                                    Esta variante representa una unidad de inventario específica y debe coincidir con el tipo de talla permitido por el producto maestro.
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 border-top pt-4 d-grid gap-2">
                            <a href="{{ route('producto-variantes.edit', $productoVariante) }}" class="btn btn-warning shadow-sm">
                                <i class="fas fa-edit me-2"></i>Modificar variante
                            </a>
                            <a href="{{ route('producto-variantes.index') }}" class="btn btn-light border">
                                Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection