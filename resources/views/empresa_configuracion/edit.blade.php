@extends('layouts.app')
@section('title', 'Configuración de Empresa')

@push('css')
<style>
    #descripcion, #mensaje_ticket { resize: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Configuración de Empresa</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active">Configuración</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 1100px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-gear text-primary me-2"></i>Datos de la empresa
            </h5>
            <span class="badge bg-light text-secondary border">ID: {{ $empresaConfiguracion->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('empresa-configuracion.update', $empresaConfiguracion) }}" method="post" enctype="multipart/form-data">
                @method('PATCH')
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="razon_social" class="form-label fw-medium text-secondary">
                            Razón social <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="razon_social" id="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social', $empresaConfiguracion->razon_social) }}">
                        @error('razon_social')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nombre_comercial" class="form-label fw-medium text-secondary">
                            Nombre comercial <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control @error('nombre_comercial') is-invalid @enderror" value="{{ old('nombre_comercial', $empresaConfiguracion->nombre_comercial) }}">
                        @error('nombre_comercial')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="ruc" class="form-label fw-medium text-secondary">
                            RUC <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="ruc" id="ruc" class="form-control @error('ruc') is-invalid @enderror" value="{{ old('ruc', $empresaConfiguracion->ruc) }}">
                        @error('ruc')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="telefono" class="form-label fw-medium text-secondary">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $empresaConfiguracion->telefono) }}">
                        @error('telefono')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label fw-medium text-secondary">Correo electrónico</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $empresaConfiguracion->email) }}">
                        @error('email')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label for="direccion_fiscal" class="form-label fw-medium text-secondary">Dirección</label>
                        <input type="text" name="direccion_fiscal" id="direccion_fiscal" class="form-control @error('direccion_fiscal') is-invalid @enderror" value="{{ old('direccion_fiscal', $empresaConfiguracion->direccion_fiscal) }}">
                        @error('direccion_fiscal')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="igv_porcentaje" class="form-label fw-medium text-secondary">
                            IGV (%) <span class="text-danger">*</span>
                        </label>
                        <input type="number" min="0" step="0.01" name="igv_porcentaje" id="igv_porcentaje" class="form-control @error('igv_porcentaje') is-invalid @enderror" value="{{ old('igv_porcentaje', $empresaConfiguracion->igv_porcentaje ?? 18) }}">
                        @error('igv_porcentaje')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="moneda" class="form-label fw-medium text-secondary">Moneda</label>
                        <input type="text" name="moneda" id="moneda" class="form-control @error('moneda') is-invalid @enderror" value="{{ old('moneda', $empresaConfiguracion->moneda ?? 'PEN') }}">
                        @error('moneda')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="logo" class="form-label fw-medium text-secondary">Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                        @error('logo')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label for="mensaje_ticket" class="form-label fw-medium text-secondary">Mensaje para ticket</label>
                        <textarea name="mensaje_ticket" id="mensaje_ticket" rows="4" class="form-control @error('mensaje_ticket') is-invalid @enderror">{{ old('mensaje_ticket', $empresaConfiguracion->mensaje_ticket) }}</textarea>
                        @error('mensaje_ticket')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label for="estado" class="form-label fw-medium text-secondary">Estado</label>
                        <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $empresaConfiguracion->estado) == 1)>Activo</option>
                            <option value="0" @selected(old('estado', $empresaConfiguracion->estado) === 0 || old('estado', $empresaConfiguracion->estado) === '0')>Inactivo</option>
                        </select>
                        @error('estado')<div class="text-danger mt-1 small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 mt-5 d-flex justify-content-between align-items-center border-top pt-4">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none">Restablecer campos</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('empresa-configuracion.show', $empresaConfiguracion) }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-sync-alt me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            @if(!empty($empresaConfiguracion->logo_path))
                <div class="mt-4">
                    <h6 class="fw-semibold text-dark mb-3">Logo actual</h6>
                    <img src="{{ asset('storage/' . $empresaConfiguracion->logo_path) }}" alt="Logo actual" class="img-fluid rounded-3 border shadow-sm" style="max-height: 160px;">
                </div>
            @endif
        </div>
    </div>
</div>
@endsection