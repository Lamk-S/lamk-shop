@extends('layouts.app')

@section('title', 'Crear Categoría')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Nueva Categoría</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}" class="text-decoration-none">Categorías</a></li>
            <li class="breadcrumb-item active">Crear registro</li>
        </ol>
    </div>

    <!-- Tarjeta del Formulario -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Datos de la categoría</h5>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('categorias.store') }}" method="post">
                @csrf
                <div class="row g-4">
                    
                    <div class="col-md-12">
                        <label for="nombre" class="form-label fw-medium text-secondary">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-tag"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control border-start-0 @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej. Lácteos, Abarrotes, Bebidas..." autofocus>
                        </div>
                        @error('nombre')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="descripcion" class="form-label fw-medium text-secondary">Descripción <span class="text-muted fw-normal">(Opcional)</span></label>
                        <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Detalles adicionales sobre esta categoría..." style="resize: none;">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 mt-5 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('categorias.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save me-2"></i>Guardar Registro</button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</div>
@endsection