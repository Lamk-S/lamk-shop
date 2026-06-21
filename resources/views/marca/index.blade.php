@extends('layouts.app')
@section('title', 'Catálogo de Marcas')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; background: #fff; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
    .filters-row .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 0.3rem;}
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Fabricantes y Marcas</h2>
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

    <div class="card soft-card">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('marcas.index') }}" id="filtro-form" class="row g-3 filters-row">
                <div class="col-lg-5 col-md-6">
                    <label for="q" class="form-label">Buscar Fabricante</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Nike, Adidas, Puma...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="estado" class="form-label">Vigencia Comercial</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Todos los estados</option>
                        <option value="activa" @selected(request('estado') === 'activa')>Con Convenio (Activas)</option>
                        <option value="inactiva" @selected(request('estado') === 'inactiva')>Suspendidas (Inactivas)</option>
                        <option value="eliminada" @selected(request('estado') === 'eliminada')>Retiradas (Eliminadas)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="per_page" class="form-label">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select shadow-sm">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex align-items-end gap-2">
                    <a href="{{ route('marcas.index') }}" class="btn btn-outline-secondary w-100 fw-bold bg-white shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header soft-header p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-copyright"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Portafolio de Marcas</h5>
                <div class="text-muted small">Firmas deportivas disponibles en el inventario actual.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 220px;">Firma / Marca Comercial</th>
                            <th>Anotaciones Logísticas</th>
                            <th class="text-center" style="width: 130px;">Estado</th>
                            @can('gestionar_marcas')
                                <th class="text-center pe-4" style="width: 120px;">Acciones</th>
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
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Retirada</span>
                                    @elseif((int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Vigente</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Suspendida</span>
                                    @endif
                                </td>
                                @can('gestionar_marcas')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2" role="group">
                                            <a href="{{ route('marcas.edit', $item) }}" class="btn btn-sm btn-light border text-primary" title="Actualizar datos">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light border {{ $item->trashed() ? 'text-success' : 'text-danger' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmModal-{{ $item->id }}"
                                                title="{{ $item->trashed() ? 'Habilitar Marca' : 'Retirar Marca' }}">
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
                                            <i class="fas fa-building text-muted fs-2 opacity-50"></i>
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

            <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small fw-medium">
                    Mostrando del <span class="fw-bold text-dark">{{ $marcas->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $marcas->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $marcas->total() }}</span> registros
                </div>
                <div class="pagination-custom">
                    {{ $marcas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@can('gestionar_marcas')
    @foreach($marcas as $item)
        <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center p-4 pb-5">
                        @if(!$item->trashed())
                            <div class="text-danger mb-3"><i class="fas fa-ban fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Archivar la marca?</h4>
                            <p class="text-muted mb-4">La firma comercial <strong>{{ $item->nombre }}</strong> dejará de mostrarse en los inventarios activos.</p>
                        @else
                            <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Habilitar marca?</h4>
                            <p class="text-muted mb-4">La firma <strong>{{ $item->nombre }}</strong> volverá a estar operativa para asociar productos.</p>
                        @endif
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('marcas.destroy', $item) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn {{ !$item->trashed() ? 'btn-danger' : 'btn-success' }} fw-bold px-4 rounded-pill shadow-sm">
                                    Confirmar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endcan
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
            typingTimer = setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
</script>
@endpush