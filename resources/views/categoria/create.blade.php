@extends('layouts.app')
@section('title', 'Crear Categoría')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .form-label-custom { font-size: .82rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .06em; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Nueva Categoría</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}" class="text-decoration-none text-muted">Categorías</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Crear registro</li>
            </ol>
        </div>
    </div>

    <div class="card soft-card mx-auto" style="max-width: 820px;">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Datos de la categoría</h5>
                    <div class="text-muted small">Usa nombres claros para facilitar búsquedas y reportes.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('categorias.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label for="nombre" class="form-label form-label-custom">
                            Nombre de la categoría <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-tag"></i></span>
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
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="descripcion" class="form-label form-label-custom">
                            Descripción <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  rows="4"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  placeholder="Describe el uso o alcance de esta categoría..."
                                  maxlength="500"
                                  style="resize: none;">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                        <div class="text-muted small mt-2">Mantén la descripción breve para que sea útil en el día a día.</div>
                    </div>

                    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
                        <a href="{{ route('categorias.index') }}" class="btn btn-light px-4">Cancelar</a>
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