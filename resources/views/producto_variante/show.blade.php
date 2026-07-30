@extends('layouts.app')
@section('title', 'Detalle de Variante')

@section('content')
@php
    $producto = $productoVariante->loadMissing(['producto.marca', 'talla'])->producto;
    $marca = $producto?->marca;
    $estadoActivo = !$productoVariante->trashed() && (int) $productoVariante->estado === 1;
@endphp

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 mx-auto" style="max-width: 1000px;">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Detalle de Variante</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('producto-variantes.index') }}" class="text-decoration-none text-muted">Variantes</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Ficha de registro</li>
            </ol>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('producto-variantes.edit', $productoVariante) }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-medium">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="{{ route('producto-variantes.index') }}" class="btn btn-light shadow-sm rounded-3 px-4 fw-medium border">
                Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 1000px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light text-secondary rounded-3 d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                        <i class="fas fa-barcode fs-4 opacity-75"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark font-monospace">{{ $productoVariante->codigo_variante }}</h4>
                        <span class="text-muted small">
                            {{ $producto?->nombre ?? 'Producto desvinculado' }}
                        </span>
                    </div>
                </div>
                <div>
                    @if($estadoActivo)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Operativo</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Inactivo / Bloqueado</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row g-5">
                <div class="col-lg-7">
                    <h6 class="text-uppercase text-secondary fw-bold mb-4 small"><i class="fas fa-box me-2"></i>Información del Artículo</h6>
                    <div class="row g-4 mb-5">
                        <div class="col-sm-6">
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Marca</span>
                            <div class="fw-medium text-dark"><span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-tag me-1 text-muted"></i>{{ $marca?->nombre ?? 'Genérica' }}</span></div>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Tipo de Producto</span>
                            <div class="fw-medium text-dark">
                                {{ $producto ? ucfirst(strtolower($producto->tipo_producto->value)) : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Talla Asignada</span>
                            <div class="fw-medium text-dark">
                                {{ optional($productoVariante->talla)->codigo ?? '-' }} - {{ optional($productoVariante->talla)->nombre ?? 'Sin talla' }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">ID de Sistema</span>
                            <div class="fw-medium text-dark font-monospace">#{{ str_pad($productoVariante->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                    
                    <h6 class="text-uppercase text-secondary fw-bold mb-4 small"><i class="fas fa-cubes me-2"></i>Estado de Inventario</h6>
                    <div class="d-flex flex-wrap gap-4 p-4 bg-light bg-opacity-50 rounded-4 border">
                        <div>
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Stock Actual</span>
                            <div class="fw-bold fs-3 {{ $productoVariante->stock_actual <= $productoVariante->stock_minimo ? 'text-danger' : 'text-primary' }}">
                                {{ number_format((float) $productoVariante->stock_actual, 0) }}
                            </div>
                        </div>
                        <div class="border-start ps-4">
                            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Mínimo Alerta</span>
                            <div class="fw-bold fs-3 text-secondary opacity-75">
                                {{ number_format((float) $productoVariante->stock_minimo, 0) }}
                            </div>
                        </div>
                    </div>

                    @if($productoVariante->stock_actual <= $productoVariante->stock_minimo)
                        <div class="alert alert-danger bg-danger bg-opacity-10 border-0 mt-3 d-flex align-items-center shadow-sm rounded-3">
                            <i class="fas fa-exclamation-triangle fs-4 text-danger me-3"></i>
                            <div>
                                <strong class="d-block text-danger">Stock Crítico</strong>
                                <span class="small text-danger opacity-75">El inventario actual está por debajo del límite sugerido para operar.</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-5">
                    <div class="p-4 bg-light rounded-4 border h-100">
                        <h6 class="text-uppercase text-secondary fw-bold mb-4 small"><i class="fas fa-history me-2"></i>Trazabilidad</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 border-bottom pb-2">
                                <span class="d-block text-muted small fw-bold">Fecha de creación</span>
                                <span class="fw-medium text-dark">{{ $productoVariante->created_at?->format('d/m/Y - H:i') ?? 'N/D' }}</span>
                            </li>
                            <li class="mb-3 border-bottom pb-2">
                                <span class="d-block text-muted small fw-bold">Última actualización</span>
                                <span class="fw-medium text-dark">{{ $productoVariante->updated_at?->format('d/m/Y - H:i') ?? 'N/D' }}</span>
                            </li>
                            <li class="mb-4">
                                <span class="d-block text-muted small fw-bold mb-1">Notas del sistema</span>
                                <p class="small text-muted mb-0 lh-sm">
                                    El SKU de esta variante es generado de manera automática basándose en el modelo base y la talla asignada. Todos los movimientos afectan directamente al Kardex.
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection