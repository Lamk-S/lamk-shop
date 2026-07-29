@extends('layouts.app')
@section('title', 'Editar Configuración de Empresa')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Configuración de Empresa</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Actualizar Datos</li>
            </ol>
        </div>
        <a href="{{ route('empresa-configuracion.show', $empresaConfiguracion) }}" class="btn btn-light border shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="card border-0 shadow-sm rounded-3 mx-auto" style="max-width: 1000px;">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-gear text-primary me-2"></i>Datos Institucionales y Fiscales
            </h5>
            <span class="badge bg-light text-secondary border font-monospace">ID: {{ $empresaConfiguracion->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('empresa-configuracion.update', $empresaConfiguracion) }}" method="post" enctype="multipart/form-data">
                @method('PATCH')
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="razon_social" class="form-label fw-bold text-muted small text-uppercase">
                            Razón social <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="razon_social" id="razon_social" class="form-control shadow-sm @error('razon_social') is-invalid @enderror" value="{{ old('razon_social', $empresaConfiguracion->razon_social) }}" required>
                        @error('razon_social')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nombre_comercial" class="form-label fw-bold text-muted small text-uppercase">
                            Nombre comercial <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control shadow-sm @error('nombre_comercial') is-invalid @enderror" value="{{ old('nombre_comercial', $empresaConfiguracion->nombre_comercial) }}" required>
                        @error('nombre_comercial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="ruc" class="form-label fw-bold text-muted small text-uppercase">
                            RUC <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="ruc" id="ruc" class="form-control shadow-sm font-monospace fw-bold @error('ruc') is-invalid @enderror" value="{{ old('ruc', $empresaConfiguracion->ruc) }}" required>
                        @error('ruc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="telefono" class="form-label fw-bold text-muted small text-uppercase">Teléfono de Contacto</label>
                        <input type="text" name="telefono" id="telefono" class="form-control shadow-sm @error('telefono') is-invalid @enderror" value="{{ old('telefono', $empresaConfiguracion->telefono) }}">
                        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label fw-bold text-muted small text-uppercase">Correo electrónico</label>
                        <input type="email" name="email" id="email" class="form-control shadow-sm @error('email') is-invalid @enderror" value="{{ old('email', $empresaConfiguracion->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label for="direccion_fiscal" class="form-label fw-bold text-muted small text-uppercase">Dirección Fiscal Completa</label>
                        <input type="text" name="direccion_fiscal" id="direccion_fiscal" class="form-control shadow-sm @error('direccion_fiscal') is-invalid @enderror" value="{{ old('direccion_fiscal', $empresaConfiguracion->direccion_fiscal) }}">
                        @error('direccion_fiscal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="igv_porcentaje" class="form-label fw-bold text-muted small text-uppercase">
                            Impuesto (IGV %) <span class="text-danger">*</span>
                        </label>
                        <input type="number" min="0" step="0.01" name="igv_porcentaje" id="igv_porcentaje" class="form-control shadow-sm font-monospace fw-bold text-end @error('igv_porcentaje') is-invalid @enderror" value="{{ old('igv_porcentaje', $empresaConfiguracion->igv_porcentaje ?? 18) }}" required>
                        @error('igv_porcentaje')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="moneda" class="form-label fw-bold text-muted small text-uppercase">ISO Moneda</label>
                        <input type="text" name="moneda" id="moneda" class="form-control shadow-sm font-monospace @error('moneda') is-invalid @enderror" value="{{ old('moneda', $empresaConfiguracion->moneda ?? 'PEN') }}" placeholder="PEN, USD...">
                        @error('moneda')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="estado" class="form-label fw-bold text-muted small text-uppercase">Estado Sistema</label>
                        <select name="estado" id="estado" class="form-select shadow-sm @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $empresaConfiguracion->estado) == 1)>Operativo / Activo</option>
                            <option value="0" @selected(old('estado', $empresaConfiguracion->estado) == 0)>Suspendido</option>
                        </select>
                        @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="logo" class="form-label fw-bold text-muted small text-uppercase">Logotipo (PNG/JPG)</label>
                        <input type="file" name="logo" id="logo" class="form-control shadow-sm @error('logo') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Dejar en blanco para mantener el logotipo actual.</div>
                        @error('logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        
                        @if(!empty($empresaConfiguracion->logo_path))
                            <div class="mt-3 p-2 bg-light border rounded-3 d-inline-block">
                                <img src="{{ asset('storage/' . $empresaConfiguracion->logo_path) }}" alt="Logo actual" class="img-fluid rounded" style="max-height: 80px;">
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="mensaje_ticket" class="form-label fw-bold text-muted small text-uppercase">Mensaje de pie de Ticket POS</label>
                        <textarea name="mensaje_ticket" id="mensaje_ticket" rows="4" class="form-control shadow-sm @error('mensaje_ticket') is-invalid @enderror" placeholder="Ej. ¡Gracias por su compra! Vuelva pronto.">{{ old('mensaje_ticket', $empresaConfiguracion->mensaje_ticket) }}</textarea>
                        @error('mensaje_ticket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 mt-5 pt-4 border-top d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-link text-muted text-decoration-none px-0">Restablecer formulario</button>
                        <div class="d-flex gap-2">
                            <a href="{{ route('empresa-configuracion.show', $empresaConfiguracion) }}" class="btn btn-light px-4 border">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection