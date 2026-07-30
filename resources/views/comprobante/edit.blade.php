@extends('layouts.app')
@section('title', 'Editar Comprobante')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Editar Comprobante</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('comprobantes.index') }}" class="text-decoration-none text-muted">Comprobantes</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Registro #{{ $comprobante->id }}</li>
            </ol>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-2 shadow-sm font-monospace">ID: {{ $comprobante->id }}</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 900px;">
        <div class="card-header bg-light bg-opacity-50 border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Actualizar comprobante</h5>
                    <div class="text-muted small">Mantén la serie y el correlativo bajo control antes de emitir nuevos documentos.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('comprobantes.update', $comprobante) }}" method="post">
                @method('PATCH')
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="tipo_comprobante" class="form-label text-muted small fw-bold text-uppercase">
                            Tipo de comprobante <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_comprobante" id="tipo_comprobante" class="form-select shadow-sm @error('tipo_comprobante') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($optionsTipoComprobante as $value => $label)
                                <option value="{{ $value }}" @selected(old('tipo_comprobante', $comprobante->tipo_comprobante->value ?? $comprobante->tipo_comprobante) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_comprobante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="serie" class="form-label text-muted small fw-bold text-uppercase">
                            Serie <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="serie"
                               id="serie"
                               class="form-control shadow-sm font-monospace fw-bold @error('serie') is-invalid @enderror"
                               value="{{ old('serie', $comprobante->serie) }}"
                               maxlength="20"
                               placeholder="Ej. F001, B001, T001">
                        @error('serie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="uso_comprobante" class="form-label text-muted small fw-bold text-uppercase">
                            Uso <span class="text-danger">*</span>
                        </label>
                        <select name="uso_comprobante" id="uso_comprobante" class="form-select shadow-sm @error('uso_comprobante') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($optionsUsoComprobante as $value => $label)
                                <option value="{{ $value }}" @selected(old('uso_comprobante', $comprobante->uso_comprobante->value ?? $comprobante->uso_comprobante) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('uso_comprobante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ambiente" class="form-label text-muted small fw-bold text-uppercase">
                            Ambiente <span class="text-danger">*</span>
                        </label>
                        <select name="ambiente" id="ambiente" class="form-select shadow-sm @error('ambiente') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($optionsAmbiente as $value => $label)
                                <option value="{{ $value }}" @selected(old('ambiente', $comprobante->ambiente->value ?? $comprobante->ambiente) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('ambiente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="correlativo_actual" class="form-label text-muted small fw-bold text-uppercase">
                            Correlativo actual <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               min="0"
                               name="correlativo_actual"
                               id="correlativo_actual"
                               class="form-control shadow-sm font-monospace text-primary fw-bold @error('correlativo_actual') is-invalid @enderror"
                               value="{{ old('correlativo_actual', $comprobante->correlativo_actual) }}">
                        @error('correlativo_actual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="estado" class="form-label text-muted small fw-bold text-uppercase">Estado</label>
                        <select name="estado" id="estado" class="form-select shadow-sm @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $comprobante->estado) == 1)>Activo</option>
                            <option value="0" @selected(old('estado', $comprobante->estado) === 0 || old('estado', $comprobante->estado) === '0')>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none px-0">Restablecer campos</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('comprobantes.index') }}" class="btn btn-light border px-4 shadow-sm fw-medium">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
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