@extends('layouts.app')
@section('title', 'Editar Comprobante')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .form-label-custom { font-size: .82rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .06em; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title text-dark mb-0">Editar Comprobante</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('comprobantes.index') }}" class="text-decoration-none">Comprobantes</a></li>
                <li class="breadcrumb-item active">Registro #{{ $comprobante->id }}</li>
            </ol>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-2">ID: {{ $comprobante->id }}</span>
    </div>

    <div class="card soft-card mx-auto" style="max-width: 900px;">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
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
                        <label for="tipo_comprobante" class="form-label form-label-custom">
                            Tipo de comprobante <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_comprobante" id="tipo_comprobante" class="form-select @error('tipo_comprobante') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($optionsTipoComprobante as $value => $label)
                                <option value="{{ $value }}" @selected(old('tipo_comprobante', $comprobante->tipo_comprobante) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_comprobante')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="serie" class="form-label form-label-custom">
                            Serie <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="serie"
                               id="serie"
                               class="form-control @error('serie') is-invalid @enderror"
                               value="{{ old('serie', $comprobante->serie) }}"
                               maxlength="20"
                               placeholder="Ej. F001, B001, T001">
                        @error('serie')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="uso_comprobante" class="form-label form-label-custom">
                            Uso <span class="text-danger">*</span>
                        </label>
                        <select name="uso_comprobante" id="uso_comprobante" class="form-select @error('uso_comprobante') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($optionsUsoComprobante as $value => $label)
                                <option value="{{ $value }}" @selected(old('uso_comprobante', $comprobante->uso_comprobante) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('uso_comprobante')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ambiente" class="form-label form-label-custom">
                            Ambiente <span class="text-danger">*</span>
                        </label>
                        <select name="ambiente" id="ambiente" class="form-select @error('ambiente') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($optionsAmbiente as $value => $label)
                                <option value="{{ $value }}" @selected(old('ambiente', $comprobante->ambiente) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('ambiente')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="correlativo_actual" class="form-label form-label-custom">
                            Correlativo actual <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               min="0"
                               name="correlativo_actual"
                               id="correlativo_actual"
                               class="form-control @error('correlativo_actual') is-invalid @enderror"
                               value="{{ old('correlativo_actual', $comprobante->correlativo_actual) }}">
                        @error('correlativo_actual')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="estado" class="form-label form-label-custom">Estado</label>
                        <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $comprobante->estado) == 1)>Activo</option>
                            <option value="0" @selected(old('estado', $comprobante->estado) === 0 || old('estado', $comprobante->estado) === '0')>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer campos</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('comprobantes.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                                <i class="fas fa-sync-alt me-2"></i>Actualizar comprobante
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection