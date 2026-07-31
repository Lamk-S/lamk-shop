@extends('layouts.app')
@section('title', 'Editar Marca')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Editar Marca</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('marcas.index') }}" class="text-decoration-none text-muted">Marcas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Registro #{{ $marca->id }}</li>
            </ol>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-2 shadow-sm">ID: {{ $marca->id }}</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 760px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-pen-to-square fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Actualizar marca</h5>
                    <div class="text-muted small mt-1">Revisa el nombre y el estado antes de guardar cambios.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('marcas.update', $marca) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label for="nombre" class="form-label text-muted small fw-bold text-uppercase">
                            Nombre de la marca <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-tag"></i></span>
                            <input type="text"
                                   name="nombre"
                                   id="nombre"
                                   class="form-control border-start-0 @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $marca->nombre) }}"
                                   maxlength="120">
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
                                  maxlength="500"
                                  style="resize: none;">{{ old('descripcion', $marca->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="estado" class="form-label text-muted small fw-bold text-uppercase">Estado</label>
                        <select name="estado" id="estado" class="form-select shadow-sm @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $marca->estado) == 1)>Activo</option>
                            <option value="0" @selected(old('estado', $marca->estado) === 0 || old('estado', $marca->estado) === '0')>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer campos</button>
                        <div class="d-flex gap-2 w-100 w-sm-auto">
                            <a href="{{ route('marcas.index') }}" class="btn btn-light px-4 border shadow-sm fw-medium flex-grow-1 flex-sm-grow-0">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold flex-grow-1 flex-sm-grow-0">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection