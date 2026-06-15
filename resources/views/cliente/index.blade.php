@extends('layouts.app')

@section('title', 'Directorio de Clientes')

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.82rem; letter-spacing: .02em; white-space: nowrap; }
    .table-custom td { vertical-align: middle; color: #495057; }
    .fs-7 { font-size: 0.875rem; }
    .fs-8 { font-size: 0.8rem; }
    .table-wrap { border-radius: 1rem; overflow: hidden; }
    .pagination { margin-bottom: 0; }
</style>
@endpush

@section('content')
@php
    $qActual = request('q');
    $tipoActual = request('tipo_persona');
    $estadoActual = request('estado');
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Directorio de Clientes</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Clientes</li>
            </ol>
        </div>

        @can('gestionar_clientes')
            <div class="mt-3 mt-md-0">
                <a href="{{ route('clientes.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                    <i class="fas fa-plus me-2"></i>Nuevo Cliente
                </a>
            </div>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('clientes.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-6">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" class="form-control" value="{{ $qActual }}" placeholder="Documento, nombres, razón social, correo">
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Tipo</label>
                    <select name="tipo_persona" class="form-select">
                        <option value="">Todos</option>
                        <option value="natural" @selected($tipoActual === 'natural')>Natural</option>
                        <option value="juridica" @selected($tipoActual === 'juridica')>Jurídica</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" @selected((string) $estadoActual === '1')>Activo</option>
                        <option value="0" @selected((string) $estadoActual === '0')>Inactivo</option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-6">
                    <label class="form-label">Filas</label>
                    <select name="per_page" class="form-select">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-semibold text-dark">Registros Actuales</h5>
                <small class="text-muted">Personas naturales y jurídicas para ventas rápidas o registradas</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive table-wrap">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nombre / Razón social</th>
                            <th>Documento</th>
                            <th>Contacto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estado</th>
                            @can('gestionar_clientes')
                                <th class="text-center">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $item)
                            @php
                                $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
                                $persona = $item->persona;
                                $nombreMostrado = $persona?->tipo_persona === 'juridica'
                                    ? ($persona?->razon_social ?? 'Sin razón social')
                                    : trim(($persona?->nombres ?? '') . ' ' . ($persona?->apellidos ?? ''));
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $nombreMostrado ?: 'Sin nombre' }}</div>
                                    @if($persona?->email)
                                        <div class="small text-muted">
                                            <i class="fas fa-envelope me-1"></i>{{ $persona->email }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="small mb-1 text-muted">
                                        {{ optional($persona?->documento)->tipo_documento ?? 'Sin documento' }}
                                    </div>
                                    <div class="fw-medium text-dark">
                                        <i class="fas fa-id-card text-secondary me-1"></i>{{ $persona?->numero_documento ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small text-truncate" style="max-width: 240px;" title="{{ $persona?->direccion }}">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $persona?->direccion ?? 'No registrada' }}
                                    </div>
                                    @if($persona?->telefono)
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-phone-alt me-1"></i>{{ $persona->telefono }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1 fs-7">
                                        {{ ucfirst($persona?->tipo_persona ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($estaEliminado)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                                    @endif
                                </td>
                                @can('gestionar_clientes')
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm" role="group">
                                            <a href="{{ route('clientes.edit', $item) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary {{ $estaEliminado ? 'text-success' : 'text-danger' }} border-light"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal-{{ $item->id }}"
                                                    title="{{ $estaEliminado ? 'Restaurar' : 'Desactivar' }}">
                                                <i class="fas {{ $estaEliminado ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_clientes') ? 6 : 5 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-users text-secondary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No hay clientes registrados</h5>
                                        <p class="text-muted mb-0">Todavía no se han agregado clientes al sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div class="text-muted small">
                Mostrando {{ $clientes->firstItem() ?? 0 }} a {{ $clientes->lastItem() ?? 0 }} de {{ $clientes->total() }} registros
            </div>
            <div>
                {{ $clientes->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($clientes as $item)
    @php
        $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
        $persona = $item->persona;
        $nombreMostrado = $persona?->tipo_persona === 'juridica'
            ? ($persona?->razon_social ?? 'Sin razón social')
            : trim(($persona?->nombres ?? '') . ' ' . ($persona?->apellidos ?? ''));
    @endphp

    <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    @if($estaEliminado)
                        <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x"></i></div>
                        <h4 class="fw-bold text-dark">¿Restaurar cliente?</h4>
                        <p class="text-muted">El cliente "{{ $nombreMostrado }}" volverá a estar activo.</p>
                    @else
                        <div class="text-danger mb-3"><i class="fas fa-exclamation-circle fa-4x"></i></div>
                        <h4 class="fw-bold text-dark">¿Desactivar cliente?</h4>
                        <p class="text-muted">El cliente "{{ $nombreMostrado }}" pasará a estado inactivo.</p>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('clientes.destroy', $item) }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn {{ $estaEliminado ? 'btn-success' : 'btn-danger' }} px-4">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection