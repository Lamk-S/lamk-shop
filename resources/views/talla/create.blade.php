@extends('layouts.app')
@section('title', 'Crear Talla')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Nueva Talla / Medida</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tallas.index') }}" class="text-decoration-none text-muted">Tallas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Crear registro</li>
            </ol>
        </div>
        <a href="{{ route('tallas.index') }}" class="btn btn-light border shadow-sm fw-medium rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-ruler-combined fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Datos de la Talla</h5>
                    <div class="text-muted small mt-1">Registra una referencia clara y estandarizada para el inventario de la tienda.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5 bg-light bg-opacity-25">
            <form action="{{ route('tallas.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label text-secondary small fw-bold text-uppercase">
                            Código Corto <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                            <input type="text" name="codigo" id="codigo" class="form-control border-start-0 fw-bold @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}" placeholder="Ej. 40, M, XL, UNICA" maxlength="20" autofocus>
                        </div>
                        @error('codigo') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tipo_talla" class="form-label text-secondary small fw-bold text-uppercase">
                            Familia / Categoría <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-layer-group"></i></span>
                            <select name="tipo_talla" id="tipo_talla" class="form-select border-start-0 @error('tipo_talla') is-invalid @enderror">
                                <option value="">Seleccione familia...</option>
                                {{-- USO CORRECTO DE LA VARIABLE ENVIADA POR EL CONTROLADOR --}}
                                @foreach($optionsTipoTalla as $key => $label)
                                    <option value="{{ $key }}" @selected(old('tipo_talla') == $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('tipo_talla') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="nombre" class="form-label text-secondary small fw-bold text-uppercase">
                            Descripción / Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control shadow-sm @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej. Talla 40 Zapatillas, Talla M Deportivo" maxlength="100">
                        @error('nombre') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="orden" class="form-label text-secondary small fw-bold text-uppercase">Prioridad de Visualización</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-sort-numeric-down"></i></span>
                            <input type="number" name="orden" id="orden" class="form-control border-start-0 @error('orden') is-invalid @enderror" value="{{ old('orden', 0) }}" min="0">
                        </div>
                        <div class="form-text small">Menor número aparece primero en selectores.</div>
                        @error('orden') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-4 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                        <span class="text-muted small">
                            <i class="fas fa-shield-alt text-primary me-2"></i>Asegúrate de usar códigos consistentes.
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tallas.index') }}" class="btn btn-light px-4 fw-bold rounded-pill border shadow-sm">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-pill">
                                <i class="fas fa-save me-2"></i>Registrar Talla
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection