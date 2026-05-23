@extends('layouts.app')

@section('title', 'Gestión de Roles')

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
    <!-- Encabezado y Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Gestión de Roles</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </div>
        
        @can('crear-role')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('roles.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Nuevo Rol
            </a>
        </div>
        @endcan
    </div>

    <!-- Contenedor de la Tabla -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Roles del Sistema</h5>
        </div>
        
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Nombre del Rol</th>
                            @canany(['editar-role', 'eliminar-role'])
                            <th class="text-center" style="width: 150px;">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark text-uppercase"><i class="fas fa-shield-alt text-secondary me-2"></i>{{ $item->name }}</div>
                                </td>
                                
                                @canany(['editar-role', 'eliminar-role'])
                                <td class="text-center align-content-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        @can('editar-role')
                                        <a href="{{ route('roles.edit', ['role' => $item]) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('eliminar-role')
                                        <button type="button" class="btn btn-sm btn-outline-secondary text-danger border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>

                            <!-- Modal de Confirmación -->
                            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center pb-4">
                                            <div class="text-danger mb-3"><i class="fas fa-exclamation-triangle fa-4x opacity-75"></i></div>
                                            <h4 class="fw-bold text-dark">¿Eliminar rol?</h4>
                                            <p class="text-muted">Estás a punto de eliminar el rol "<span class="fw-bold">{{ $item->name }}</span>". Los usuarios con este rol podrían perder el acceso al sistema.</p>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 justify-content-center">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('roles.destroy', ['role' => $item->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-danger px-4">
                                                    Confirmar Eliminación
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush