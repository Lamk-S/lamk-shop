@extends('layouts.app')
@section('title', 'Editar Talla')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Modificar Talla</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tallas.index') }}" class="text-decoration-none text-muted">Tallas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">{{ $talla->codigo }}</li>
            </ol>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-3 py-2 fs-7 font-monospace shadow-sm">ID: {{ str_pad($talla->id, 4, '0', STR_PAD_LEFT) }}</span>
            <a href="{{ route('tallas.index') }}" class="btn btn-light border shadow-sm fw-medium rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-pen-to-square fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Actualización de Parámetros</h5>
                    <div class="text-muted small mt-1">Verifica el tipo antes de guardar para no desorganizar el catálogo activo.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5 bg-light bg-opacity-25">
            <form action="{{ route('tallas.update', $talla) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label text-secondary small fw-bold text-uppercase">
                            Código Corto <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                            <input type="text" name="codigo" id="codigo" class="form-control border-start-0 fw-bold @error('codigo') is-invalid @enderror" value="{{ old('codigo', $talla->codigo) }}" maxlength="20">
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
                                {{-- USO CORRECTO DE LA VARIABLE ENVIADA POR EL CONTROLADOR --}}
                                @foreach($optionsTipoTalla as $key => $label)
                                    <option value="{{ $key }}" @selected(old('tipo_talla', $talla->tipo_talla) == $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('tipo_talla') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="nombre" class="form-label text-secondary small fw-bold text-uppercase">
                            Descripción / Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control shadow-sm @error('nombre') is-invalid @enderror" value="{{ old('nombre', $talla->nombre) }}" maxlength="100">
                        @error('nombre') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="orden" class="form-label text-secondary small fw-bold text-uppercase">Prioridad de Visualización</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-sort-numeric-down"></i></span>
                            <input type="number" name="orden" id="orden" class="form-control border-start-0 @error('orden') is-invalid @enderror" value="{{ old('orden', $talla->orden) }}" min="0">
                        </div>
                        @error('orden') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="estado" class="form-label text-secondary small fw-bold text-uppercase">Estado del Sistema</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-toggle-on"></i></span>
                            <select name="estado" id="estado" class="form-select border-start-0 @error('estado') is-invalid @enderror">
                                <option value="1" @selected(old('estado', $talla->estado) == 1)>Activo y Visible</option>
                                <option value="0" @selected(old('estado', $talla->estado) == 0)>Inactivo (Oculto)</option>
                            </select>
                        </div>
                        @error('estado') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-4 border-top d-flex flex-column flex-sm-row justify-content-end align-items-center gap-3">
                        <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                            <a href="{{ route('tallas.index') }}" class="btn btn-light px-4 fw-bold rounded-pill border shadow-sm">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold rounded-pill">
                                <i class="fas fa-sync-alt me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection