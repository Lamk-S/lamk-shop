@extends('layouts.app')
@section('title', 'Crear Categoría')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Nueva Categoría</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}" class="text-decoration-none text-muted">Categorías</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Crear registro</li>
            </ol>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 820px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-tags fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Datos de la categoría</h5>
                    <div class="text-muted small mt-1">Usa nombres claros para facilitar búsquedas y reportes.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('categorias.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label for="nombre" class="form-label text-muted small fw-bold text-uppercase">
                            Nombre de la categoría <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-tag"></i></span>
                            <input type="text"
                                   name="nombre"
                                   id="nombre"
                                   class="form-control border-start-0 @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}"
                                   placeholder="Ej. Zapatillas, Ropa Deportiva, Accesorios"
                                   maxlength="120"
                                   autofocus>
                        </div>
                        @error('nombre')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="descripcion" class="form-label text-muted small fw-bold text-uppercase">
                            Descripción <span class="text-muted fw-normal text-lowercase">(opcional)</span>
                        </label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  rows="4"
                                  class="form-control shadow-sm @error('descripcion') is-invalid @enderror"
                                  placeholder="Describe el uso o alcance de esta categoría..."
                                  maxlength="500"
                                  style="resize: none;">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                        <div class="text-muted small mt-2">Mantén la descripción breve para que sea útil en el día a día.</div>
                    </div>

                    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
                        <a href="{{ route('categorias.index') }}" class="btn btn-light px-4 border shadow-sm fw-medium">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i>Guardar categoría
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection