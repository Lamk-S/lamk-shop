@extends('layouts.app')

@section('title', 'Nuevo Producto')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    #descripcion { resize: none; }
    .section-title { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; color: #495057; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #e9ecef; padding-bottom: 0.5rem; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Nuevo Producto</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none">Productos</a></li>
            <li class="breadcrumb-item active">Crear registro</li>
        </ol>
    </div>

    <form action="{{ route('productos.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Panel 1: Información Básica -->
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h6 class="section-title"><i class="fas fa-info-circle text-primary me-2"></i>Información General</h6>
                        <div class="row g-4">
                            <!-- Código -->
                            <div class="col-md-5">
                                <label for="codigo" class="form-label fw-medium text-secondary">Código (SKU/Barras) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                                    <input type="text" name="codigo" id="codigo" class="form-control border-start-0 @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}" placeholder="Ej. PROD-001">
                                </div>
                                @error('codigo') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Nombre -->
                            <div class="col-md-7">
                                <label for="nombre" class="form-label fw-medium text-secondary">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej. Zapatillas Running Nike...">
                                @error('nombre') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Descripcion -->
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label fw-medium text-secondary">Descripción Detallada <span class="text-muted fw-normal">(Opcional)</span></label>
                                <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Añada características, colores, dimensiones...">{{ old('descripcion') }}</textarea>
                                @error('descripcion') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel 2: Clasificación y Detalles Extras -->
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h6 class="section-title"><i class="fas fa-tags text-success me-2"></i>Clasificación y Logística</h6>
                        <div class="row g-4">
                            
                            <!-- Marca -->
                            <div class="col-md-6">
                                <label for="marca_id" class="form-label fw-medium text-secondary">Marca <span class="text-danger">*</span></label>
                                <select data-size="4" title="Seleccione..." data-live-search="true" name="marca_id" id="marca_id" class="form-control selectpicker show-tick">
                                    @foreach($marcas as $item)
                                        <option value="{{ $item->id }}" {{ old('marca_id') == $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('marca_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Presentación -->
                            <div class="col-md-6">
                                <label for="presentacione_id" class="form-label fw-medium text-secondary">Presentación <span class="text-danger">*</span></label>
                                <select data-size="4" title="Seleccione..." data-live-search="true" name="presentacione_id" id="presentacione_id" class="form-control selectpicker show-tick">
                                    @foreach($presentaciones as $item)
                                        <option value="{{ $item->id }}" {{ old('presentacione_id') == $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('presentacione_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Categoría (Múltiple) -->
                            <div class="col-md-12">
                                <label for="categoria_id" class="form-label fw-medium text-secondary">Categorías (Puede elegir varias) <span class="text-danger">*</span></label>
                                <select data-size="4" title="Busque y seleccione categorías..." data-live-search="true" name="categoria_id[]" id="categoria_id" class="form-control selectpicker show-tick" multiple data-selected-text-format="count > 3">
                                    @foreach($categorias as $item)
                                        <option value="{{ $item->id }}" {{ in_array($item->id, old('categoria_id', [])) ? 'selected' : '' }}>{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('categoria_id') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Fecha de vencimiento -->
                            <div class="col-md-12">
                                <label for="fecha_vencimiento" class="form-label fw-medium text-secondary">Fecha de Vencimiento <span class="text-muted fw-normal">(Si aplica)</span></label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
                                @error('fecha_vencimiento') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Imagen -->
                            <div class="col-md-12">
                                <label for="img_path" class="form-label fw-medium text-secondary">Fotografía del Producto <span class="text-muted fw-normal">(JPG/PNG)</span></label>
                                <input type="file" name="img_path" id="img_path" class="form-control" accept="image/*">
                                @error('img_path') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones Generales -->
            <div class="col-12 mt-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold"><i class="fas fa-save me-2"></i>Guardar Producto</button>
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