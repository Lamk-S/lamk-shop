@extends('layouts.app')
@section('title', 'Editar Talla')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; }
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
            <h2 class="page-title mb-0">Editar Talla</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tallas.index') }}" class="text-decoration-none text-muted">Tallas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Registro #{{ $talla->id }}</li>
            </ol>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-2">ID: {{ $talla->id }}</span>
    </div>

    <div class="card soft-card mx-auto" style="max-width: 800px;">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Actualizar talla</h5>
                    <div class="text-muted small">Verifica el tipo antes de guardar para no romper las variantes asociadas.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('tallas.update', $talla) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="codigo" class="form-label form-label-custom">
                            Código <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="codigo"
                               id="codigo"
                               class="form-control @error('codigo') is-invalid @enderror"
                               value="{{ old('codigo', $talla->codigo) }}"
                               maxlength="20">
                        @error('codigo')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tipo_talla" class="form-label form-label-custom">
                            Tipo de talla <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_talla" id="tipo_talla" class="form-select @error('tipo_talla') is-invalid @enderror">
                            <option value="CALZADO" @selected(old('tipo_talla', $talla->tipo_talla) === 'CALZADO')>Calzado</option>
                            <option value="ROPA" @selected(old('tipo_talla', $talla->tipo_talla) === 'ROPA')>Ropa</option>
                            <option value="UNICA" @selected(old('tipo_talla', $talla->tipo_talla) === 'UNICA')>Única</option>
                        </select>
                        @error('tipo_talla')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="nombre" class="form-label form-label-custom">
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="nombre"
                               id="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $talla->nombre) }}"
                               maxlength="100">
                        @error('nombre')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="orden" class="form-label form-label-custom">Orden</label>
                        <input type="number"
                               name="orden"
                               id="orden"
                               class="form-control @error('orden') is-invalid @enderror"
                               value="{{ old('orden', $talla->orden) }}"
                               min="0">
                        @error('orden')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="estado" class="form-label form-label-custom">Estado</label>
                        <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $talla->estado) == 1)>Activo</option>
                            <option value="0" @selected(old('estado', $talla->estado) === 0 || old('estado', $talla->estado) === '0')>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer campos</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tallas.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar talla
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection