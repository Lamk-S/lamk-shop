@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

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
            <h2 class="fw-bold text-dark mb-0">Directorio de Usuarios</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Usuarios</li>
            </ol>
        </div>

        @can('crear-user')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Nuevo Usuario
            </a>
        </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-users"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Usuarios Registrados</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo Electrónico</th>
                            <th class="text-center">Rol Asignado</th>
                            <th class="text-center">Estado</th>
                            @canany(['editar-user', 'eliminar-user'])
                            <th class="text-center" style="width: 120px;">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center text-secondary me-3" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="fw-bold text-dark">{{ $item->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted"><i class="fas fa-envelope me-2 opacity-50"></i>{{ $item->email }}</span>
                                </td>
                                <td class="text-center align-content-center">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 rounded-pill">
                                        {{ $item->getRoleNames()->first() ?? 'Sin Rol' }}
                                    </span>
                                </td>
                                <td class="text-center align-content-center">
                                    @if($item->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Eliminado</span>
                                    @elseif((int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill">Inactivo</span>
                                    @endif
                                </td>

                                @canany(['editar-user', 'eliminar-user'])
                                <td class="text-center align-content-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        @can('editar-user')
                                        <a href="{{ route('users.edit', ['user' => $item]) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar-user')
                                            @if($item->trashed())
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-success border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Restaurar">
                                                    <i class="fas fa-trash-restore-alt"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-danger border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->canAny(['editar-user', 'eliminar-user']) ? 5 : 4 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                                            style="width: 90px; height: 90px;">
                                            <i class="fas fa-users text-primary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">
                                            No hay usuarios registrados
                                        </h5>
                                        <p class="text-muted mb-0">
                                            Aún no existen usuarios creados en el sistema.
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

@foreach($users as $item)
    <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    @if($item->trashed())
                        <div class="text-success mb-3"><i class="fas fa-trash-restore-alt fa-4x opacity-75"></i></div>
                        <h4 class="fw-bold text-dark">¿Restaurar usuario?</h4>
                        <p class="text-muted">El usuario "{{ $item->name }}" volverá a estar disponible en el sistema.</p>
                    @else
                        <div class="text-danger mb-3"><i class="fas fa-user-times fa-4x opacity-75"></i></div>
                        <h4 class="fw-bold text-dark">¿Eliminar usuario?</h4>
                        <p class="text-muted">El usuario "{{ $item->name }}" perderá el acceso al sistema.</p>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('users.destroy', ['user' => $item->id]) }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn {{ $item->trashed() ? 'btn-success' : 'btn-danger' }} px-4">
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