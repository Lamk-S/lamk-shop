@extends('layouts.app')
@section('title', 'Gestión de Cajas')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.82rem; white-space: nowrap; }
    .table-custom td { vertical-align: middle; color: #495057; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="page-title mb-0">Gestión de Cajas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Cajas</li>
            </ol>
        </div>
        @can('gestionar_cajas')
            <div class="mt-3 mt-md-0">
                <a href="{{ route('cajas.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                    <i class="fas fa-plus me-2"></i>Nueva Caja
                </a>
            </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <h5 class="mb-0 fw-semibold text-dark">Cajas registradas</h5>
            </div>
            <div class="text-muted small">
                Mostrando {{ $cajas->firstItem() ?? 0 }} - {{ $cajas->lastItem() ?? 0 }} de {{ $cajas->total() }} registros
            </div>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-end">Fondo fijo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Creado</th>
                            @can('gestionar_cajas')
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center text-secondary me-3" style="width: 35px; height: 35px;">
                                            <i class="fas fa-cash-register"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->nombre }}</div>
                                            <div class="small text-muted">ID: {{ $item->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-medium">S/ {{ number_format($item->fondo_fijo, 2) }}</td>
                                <td class="text-center">
                                    @if($item->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Eliminada</span>
                                    @elseif((int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Activa</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill">Inactiva</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="text-muted small">{{ optional($item->created_at)->format('d/m/Y H:i') }}</div>
                                </td>
                                @can('gestionar_cajas')
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm" role="group">
                                            <a href="{{ route('cajas.edit', $item) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary {{ $item->trashed() ? 'text-success' : 'text-danger' }} border-light"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal-{{ $item->id }}"
                                                    title="{{ $item->trashed() ? 'Restaurar' : 'Eliminar' }}">
                                                <i class="fas {{ $item->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_cajas') ? 5 : 4 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-cash-register text-success fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No hay cajas registradas</h5>
                                        <p class="text-muted mb-0">Actualmente no existen cajas disponibles en el sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $cajas->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($cajas as $item)
    <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    @if($item->trashed())
                        <div class="text-success mb-3"><i class="fas fa-trash-restore-alt fa-4x opacity-75"></i></div>
                        <h4 class="fw-bold text-dark">¿Restaurar caja?</h4>
                        <p class="text-muted">La caja "<span class="fw-bold">{{ $item->nombre }}</span>" volverá a estar activa en el sistema.</p>
                    @else
                        <div class="text-danger mb-3"><i class="fas fa-exclamation-triangle fa-4x opacity-75"></i></div>
                        <h4 class="fw-bold text-dark">¿Eliminar caja?</h4>
                        <p class="text-muted">La caja "<span class="fw-bold">{{ $item->nombre }}</span>" pasará a estado eliminado.</p>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('cajas.destroy', $item) }}" method="post">
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