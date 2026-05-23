@extends('layouts.app')

@section('title','Editar Presentación')

@push('css')
<style>
    #descripcion { resize: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Modificar Presentación</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('presentaciones.index') }}" class="text-decoration-none">Presentaciones</a></li>
            <li class="breadcrumb-item active">Editar registro</li>
        </ol>
    </div>

    <!-- Tarjeta del Formulario Centrada -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 700px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-box text-warning me-2"></i>Detalles de la Presentación</h5>
            <span class="badge bg-light text-secondary border">ID: {{ $presentacione->id }}</span>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('presentaciones.update', ['presentacione' => $presentacione]) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">

                    <!-- Nombre -->
                    <div class="col-md-8">
                        <label for="nombre" class="form-label fw-medium text-secondary">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-tag"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control border-start-0 @error('nombre') is-invalid @enderror" value="{{ old('nombre', $presentacione->caracteristica->nombre) }}">
                        </div>
                        @error('nombre')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sigla -->
                    <div class="col-md-4">
                        <label for="sigla" class="form-label fw-medium text-secondary">Sigla <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-text-width"></i></span>
                            <input type="text" name="sigla" id="sigla" class="form-control border-start-0 @error('sigla') is-invalid @enderror" value="{{ old('sigla', $presentacione->sigla) }}" required>
                        </div>
                        @error('sigla')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label fw-medium text-secondary">Descripción <span class="text-muted fw-normal">(Opcional)</span></label>
                        <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $presentacione->caracteristica->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 mt-5 d-flex justify-content-between align-items-center border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer campos</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('presentaciones.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-sync-alt me-2"></i>Actualizar Presentación</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection