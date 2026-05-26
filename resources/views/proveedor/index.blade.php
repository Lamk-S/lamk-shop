@extends('layouts.app')

@section('title', 'Directorio de Proveedores')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
    .table-custom td { vertical-align: middle; color: #495057; }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Directorio de Proveedores</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Proveedores</li>
            </ol>
        </div>

        @can('crear-proveedor')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('proveedores.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Nuevo Proveedor
            </a>
        </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-truck-moving"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Registros Actuales</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Entidad / Nombre</th>
                            <th>Dirección</th>
                            <th>Documento</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estado</th>
                            @canany(['editar-proveedor', 'eliminar-proveedor'])
                                <th class="text-center">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($proveedores as $item)
                            @php
                                $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
                            @endphp

                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->persona->razon_social }}</div>
                                    @if($item->persona->email)
                                        <div class="small text-muted">
                                            <i class="fas fa-envelope me-1"></i>{{ $item->persona->email }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="text-muted small text-truncate" style="max-width: 200px;" title="{{ $item->persona->direccion }}">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $item->persona->direccion ?? 'No registrada' }}
                                    </div>
                                    @if($item->persona->telefono)
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-phone-alt me-1"></i>{{ $item->persona->telefono }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="small mb-1 text-muted">{{ $item->persona->documento->tipo_documento }}</div>
                                    <div class="fw-medium text-dark">
                                        <i class="fas fa-id-card text-secondary me-1"></i>{{ $item->persona->numero_documento }}
                                    </div>
                                </td>

                                <td class="text-center align-content-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1 fs-7">
                                        {{ ucfirst($item->persona->tipo_persona) }}
                                    </span>
                                </td>

                                <td class="text-center align-content-center">
                                    @if($estaEliminado)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                                    @endif
                                </td>

                                @canany(['editar-proveedor', 'eliminar-proveedor'])
                                    <td class="text-center align-content-center">
                                        <div class="btn-group shadow-sm" role="group">
                                            @can('editar-proveedor')
                                                <a href="{{ route('proveedores.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('eliminar-proveedor')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary {{ $estaEliminado ? 'text-success' : 'text-danger' }} border-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmModal-{{ $item->id }}"
                                                        title="{{ $estaEliminado ? 'Restaurar' : 'Desactivar' }}">
                                                    <i class="fas {{ $estaEliminado ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->canAny(['editar-proveedor', 'eliminar-proveedor']) ? 6 : 5 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                                            style="width: 90px; height: 90px;">
                                            <i class="fas fa-truck-loading text-secondary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">
                                            No hay proveedores registrados
                                        </h5>
                                        <p class="text-muted mb-0">
                                            Todavía no se han agregado proveedores al sistema.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($proveedores as $item)
    @php
        $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
    @endphp

    <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center pb-4">
                    @if($estaEliminado)
                        <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x"></i></div>
                        <h4 class="fw-bold text-dark">¿Restaurar proveedor?</h4>
                        <p class="text-muted">El proveedor "{{ $item->persona->razon_social }}" volverá a estar activo.</p>
                    @else
                        <div class="text-danger mb-3"><i class="fas fa-exclamation-circle fa-4x"></i></div>
                        <h4 class="fw-bold text-dark">¿Desactivar proveedor?</h4>
                        <p class="text-muted">El proveedor "{{ $item->persona->razon_social }}" pasará a estado inactivo.</p>
                    @endif
                </div>

                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('proveedores.destroy', $item->id) }}" method="post">
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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush