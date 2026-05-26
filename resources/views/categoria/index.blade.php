@extends('layouts.app')

@section('title','Categorías')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<style>
    .table-custom th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .table-custom td {
        vertical-align: middle;
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Catálogo de Categorías</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Categorías</li>
            </ol>
        </div>

        @can('crear-categoria')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('categorias.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Nueva Categoría
            </a>
        </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-tags"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Registros Actuales</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center">Estado</th>
                            @canany(['editar-categoria', 'eliminar-categoria'])
                            <th class="text-center">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorias as $categoria)
                            <tr>
                                <td class="fw-medium text-dark">{{ $categoria->nombre }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($categoria->descripcion, 60, '...') }}</td>
                                <td class="text-center align-content-center">
                                    @if(!$categoria->trashed())
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                                    @endif
                                </td>

                                @canany(['editar-categoria', 'eliminar-categoria'])
                                <td class="text-center align-content-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        @can('editar-categoria')
                                        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar-categoria')
                                            @if(!$categoria->trashed())
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-danger border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $categoria->id }}" title="Desactivar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-success border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $categoria->id }}" title="Restaurar">
                                                    <i class="fas fa-trash-restore-alt"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>

                            <div class="modal fade" id="confirmModal-{{ $categoria->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center pb-4">
                                            @if(!$categoria->trashed())
                                                <div class="text-danger mb-3"><i class="fas fa-exclamation-circle fa-4x"></i></div>
                                                <h4 class="fw-bold text-dark">¿Desactivar categoría?</h4>
                                                <p class="text-muted">El registro "{{ $categoria->nombre }}" ya no estará disponible para nuevas transacciones.</p>
                                            @else
                                                <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x"></i></div>
                                                <h4 class="fw-bold text-dark">¿Restaurar categoría?</h4>
                                                <p class="text-muted">El registro "{{ $categoria->nombre }}" volverá a estar activo en el sistema.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-0 pt-0 justify-content-center">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('categorias.destroy', $categoria) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn {{ !$categoria->trashed() ? 'btn-danger' : 'btn-success' }} px-4">
                                                    Confirmar
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