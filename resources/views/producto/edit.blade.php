@extends('layouts.app')

@section('title', 'Editar Producto')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    #descripcion { resize: none; }
    .section-title { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; color: #495057; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #e9ecef; padding-bottom: 0.5rem; }
    .img-preview { max-height: 150px; object-fit: contain; border-radius: 8px; border: 1px solid #dee2e6; padding: 0.25rem; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Modificar Producto</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none">Productos</a></li>
            <li class="breadcrumb-item active">Editar registro</li>
        </ol>
    </div>

    <form action="{{ route('productos.update', $producto) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <div class="row g-4">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="fas fa-info-circle text-warning me-2"></i>Información General</h5>
                        <span class="badge bg-light text-secondary border">ID: {{ $producto->id }}</span>
                    </div>
                    <div class="card-body p-4 p-md-5 pt-4">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <label for="codigo" class="form-label fw-medium text-secondary">Código (SKU/Barras) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                                    <input type="text" name="codigo" id="codigo" class="form-control border-start-0 @error('codigo') is-invalid @enderror" value="{{ old('codigo', $producto->codigo) }}">
                                </div>
                                @error('codigo') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-7">
                                <label for="nombre" class="form-label fw-medium text-secondary">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto->nombre) }}">
                                @error('nombre') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="descripcion" class="form-label fw-medium text-secondary">Descripción Detallada <span class="text-muted fw-normal">(Opcional)</span></label>
                                <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                @error('descripcion') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="precio_compra" class="form-label fw-medium text-secondary">Precio de Compra <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                                    <input type="number" step="0.01" min="0" name="precio_compra" id="precio_compra" class="form-control border-start-0 @error('precio_compra') is-invalid @enderror" value="{{ old('precio_compra', $producto->precio_compra) }}">
                                </div>
                                @error('precio_compra') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="precio_venta" class="form-label fw-medium text-secondary">Precio de Venta <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">S/</span>
                                    <input type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" class="form-control border-start-0 @error('precio_venta') is-invalid @enderror" value="{{ old('precio_venta', $producto->precio_venta) }}">
                                </div>
                                @error('precio_venta') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="stock" class="form-label fw-medium text-secondary">Stock Actual</label>
                                <input type="number" min="0" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $producto->stock) }}">
                                @error('stock') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="stock_minimo" class="form-label fw-medium text-secondary">Stock Mínimo</label>
                                <input type="number" min="0" name="stock_minimo" id="stock_minimo" class="form-control @error('stock_minimo') is-invalid @enderror" value="{{ old('stock_minimo', $producto->stock_minimo) }}">
                                @error('stock_minimo') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12 mt-4 border-top pt-4">
                                <label for="img_path" class="form-label fw-medium text-secondary">Fotografía del Producto</label>
                                <div class="d-flex align-items-center gap-4">
                                    @if($producto->img_path)
                                        <div>
                                            <span class="d-block small text-muted mb-1 text-center">Imagen actual</span>
                                            <img src="{{ Storage::url($producto->img_path) }}" alt="Producto" class="img-preview shadow-sm">
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <span class="d-block small text-muted mb-1">Subir nueva imagen (Reemplazará la actual)</span>
                                        <input type="file" name="img_path" id="img_path" class="form-control" accept="image/*">
                                        @error('img_path') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="fas fa-tags text-success me-2"></i>Clasificación y Logística</h5>
                    </div>
                    <div class="card-body p-4 p-md-5 pt-4">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <label for="marca_id" class="form-label fw-medium text-secondary">Marca <span class="text-danger">*</span></label>
                                <select data-size="4" title="Seleccione..." data-live-search="true" name="marca_id" id="marca_id" class="form-control selectpicker show-tick">
                                    @foreach($marcas as $item)
                                        <option value="{{ $item->id }}" {{ (old('marca_id', $producto->marca_id) == $item->id) ? 'selected' : '' }}>{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('marca_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="presentacion_id" class="form-label fw-medium text-secondary">Presentación <span class="text-danger">*</span></label>
                                <select data-size="4" title="Seleccione..." data-live-search="true" name="presentacion_id" id="presentacion_id" class="form-control selectpicker show-tick">
                                    @foreach($presentaciones as $item)
                                        <option value="{{ $item->id }}" {{ (old('presentacion_id', $producto->presentacion_id) == $item->id) ? 'selected' : '' }}>{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('presentacion_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="categoria_id" class="form-label fw-medium text-secondary">Categorías <span class="text-danger">*</span></label>
                                <select data-size="4" title="Busque y seleccione categorías..." data-live-search="true" name="categoria_id[]" id="categoria_id" class="form-control selectpicker show-tick" multiple data-selected-text-format="count > 3">
                                    @php
                                        $selectedCategories = old('categoria_id', $producto->categorias->pluck('id')->toArray());
                                    @endphp
                                    @foreach($categorias as $item)
                                        <option value="{{ $item->id }}" {{ in_array($item->id, $selectedCategories) ? 'selected' : '' }}>{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('categoria_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="fecha_vencimiento" class="form-label fw-medium text-secondary">Fecha de Vencimiento <span class="text-muted fw-normal">(Si aplica)</span></label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento', $producto->fecha_vencimiento) }}">
                                @error('fecha_vencimiento') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer valores</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('productos.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold"><i class="fas fa-sync-alt me-2"></i>Actualizar Producto</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush