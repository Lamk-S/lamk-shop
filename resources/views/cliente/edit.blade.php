@extends('layouts.app')

@section('title', 'Editar Cliente')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Modificar Cliente</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}" class="text-decoration-none">Clientes</a></li>
            <li class="breadcrumb-item active">Editar registro</li>
        </ol>
    </div>

    <!-- Tarjeta del Formulario -->
    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-address-card text-warning me-2"></i>Datos del Cliente</h5>
            <span class="badge bg-light text-secondary border">ID: {{ $cliente->persona->id }}</span>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('clientes.update', ['cliente' => $cliente]) }}" method="post">
                @method('PATCH')
                @csrf
                <div class="row g-4">
                    
                    <!-- Tipo de persona (Informativo - No editable) -->
                    <div class="col-md-12 mb-2">
                        <span class="text-secondary fw-medium me-2">Tipo de persona registrada:</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fs-7">
                            <i class="fas {{ strtolower($cliente->persona->tipo_persona) == 'natural' ? 'fa-user' : 'fa-building' }} me-1"></i>
                            {{ strtoupper($cliente->persona->tipo_persona) }}
                        </span>
                    </div>
                    
                    <!-- Documento -->
                    <div class="col-md-6">
                        <label for="documento_id" class="form-label fw-medium text-secondary">Tipo de documento <span class="text-danger">*</span></label>
                        <select class="form-select @error('documento_id') is-invalid @enderror" name="documento_id" id="documento_id">
                            @foreach($documentos as $item)
                                <option value="{{ $item->id }}" {{ (old('documento_id') ?? $cliente->persona->documento_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->tipo_documento }}
                                </option>
                            @endforeach
                        </select>
                        @error('documento_id')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="numero_documento" class="form-label fw-medium text-secondary">Número de documento <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-id-badge"></i></span>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control border-start-0 @error('numero_documento') is-invalid @enderror" value="{{ old('numero_documento', $cliente->persona->numero_documento) }}">
                        </div>
                        @error('numero_documento')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Razón Social / Nombres -->
                    <div class="col-md-12">
                        <label for="razon_social" class="form-label fw-medium text-secondary">
                            {{ strtolower($cliente->persona->tipo_persona) == 'natural' ? 'Nombres y apellidos' : 'Razón Social o Nombre de la empresa' }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user-tie"></i></span>
                            <input type="text" name="razon_social" id="razon_social" class="form-control border-start-0 @error('razon_social') is-invalid @enderror" value="{{ old('razon_social', $cliente->persona->razon_social) }}">
                        </div>
                        @error('razon_social')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dirección (Ahora Obligatoria) -->
                    <div class="col-md-12">
                        <label for="direccion" class="form-label fw-medium text-secondary">Dirección <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="direccion" id="direccion" class="form-control border-start-0 @error('direccion') is-invalid @enderror" value="{{ old('direccion', $cliente->persona->direccion) }}">
                        </div>
                        @error('direccion')
                            <div class="text-danger mt-1 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 mt-5 d-flex justify-content-between align-items-center border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer campos</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('clientes.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-sync-alt me-2"></i>Actualizar Registro</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush