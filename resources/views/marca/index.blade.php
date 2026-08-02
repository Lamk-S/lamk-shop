@extends('layouts.app')
@section('title', 'Catálogo de Marcas')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Fabricantes y Marcas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Marcas Registradas</li>
            </ol>
        </div>

        @can('gestionar_marcas')
            <a href="{{ route('marcas.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="fas fa-plus me-2"></i>Registrar Marca
            </a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('marcas.index') }}" id="filtro-form" class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-6">
                    <label for="q" class="form-label text-muted small fw-bold text-uppercase">Buscar Fabricante</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Nike, Adidas, Puma...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="estado" class="form-label text-muted small fw-bold text-uppercase">Vigencia Comercial</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Todos los estados</option>
                        <option value="activa" @selected(request('estado') === 'activa')>Con Convenio (Activas)</option>
                        <option value="inactiva" @selected(request('estado') === 'inactiva')>Suspendidas (Inactivas)</option>
                        <option value="eliminada" @selected(request('estado') === 'eliminada')>Retiradas (Eliminadas)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="per_page" class="form-label text-muted small fw-bold text-uppercase">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select shadow-sm">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <a href="{{ route('marcas.index') }}" class="btn btn-light w-100 fw-bold border shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-copyright fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Portafolio de Marcas</h5>
                <div class="text-muted small mt-1">Firmas deportivas disponibles en el inventario actual.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0" style="min-width: 220px;">Firma / Marca Comercial</th>
                            <th class="border-bottom-0">Anotaciones Logísticas</th>
                            <th class="text-center border-bottom-0" style="width: 130px;">Estado</th>
                            @can('gestionar_marcas')
                                <th class="text-center pe-4 border-bottom-0" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marcas as $item)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $item->nombre }}</div>
                                    <div class="small text-muted font-monospace mt-1">Cód: M-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="text-muted py-3">
                                    {{ \Illuminate\Support\Str::limit($item->descripcion, 80, '...') ?: 'Sin anotaciones' }}
                                </td>
                                <td class="text-center py-3">
                                    @if($item->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-trash me-1"></i> Retirada</span>
                                    @elseif((int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Vigente</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-ban me-1"></i> Suspendida</span>
                                    @endif
                                </td>
                                @can('gestionar_marcas')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2" role="group">
                                            <a href="{{ route('marcas.edit', $item) }}" class="btn btn-sm btn-light border text-primary" title="Actualizar datos">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                class="btn btn-sm btn-light border btn-confirmar {{ $item->trashed() ? 'text-success' : 'text-danger' }}"
                                                data-nombre="{{ $item->nombre }}"
                                                data-accion="{{ $item->trashed() ? 'restaurar' : 'desactivar' }}"
                                                data-url="{{ $item->trashed() ? route('marcas.restore', $item) : route('marcas.destroy', $item) }}"
                                                data-metodo="{{ $item->trashed() ? 'PATCH' : 'DELETE' }}"
                                                title="{{ $item->trashed() ? 'Restaurar' : 'Desactivar' }}">
                                                <i class="fas {{ $item->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_marcas') ? 4 : 3 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-building text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Sin Firmas Registradas</h5>
                                        <p class="text-muted mb-0">No hay coincidencias con la marca buscada en el sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="text-muted small fw-medium">
                Mostrando del <span class="fw-bold text-dark">{{ $marcas->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $marcas->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $marcas->total() }}</span> registros
            </div>
            <div>
                {{ $marcas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filtro-form');
        const searchInput = document.getElementById('q');

        form.querySelectorAll('select').forEach(element => {
            element.addEventListener('change', () => form.submit());
        });

        let typingTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => { form.submit(); }, 500);
        });
    });
</script>
@endpush