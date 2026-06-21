@extends('layouts.app')
@section('title', 'Control de Medidas y Tallas')

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
            <h2 class="page-title mb-0">Parámetros de Medida (Tallas)</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Tallas y Calibres</li>
            </ol>
        </div>

        @can('gestionar_tallas')
            <a href="{{ route('tallas.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="fas fa-ruler me-2"></i>Añadir Nueva Medida
            </a>
        @endcan
    </div>

    <div class="card soft-card">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('tallas.index') }}" id="filtro-form" class="row g-3 filters-row">
                <div class="col-lg-3 col-md-6">
                    <label for="q" class="form-label">Buscar Medida</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Ej. XL, 42...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="tipo_talla" class="form-label">Familia / Tipo</label>
                    <select name="tipo_talla" id="tipo_talla" class="form-select shadow-sm">
                        <option value="">Todas las Familias</option>
                        <option value="CALZADO" @selected(request('tipo_talla') === 'CALZADO')>Solo Calzado (Zapatillas)</option>
                        <option value="ROPA" @selected(request('tipo_talla') === 'ROPA')>Solo Ropa (Deportiva)</option>
                        <option value="UNICA" @selected(request('tipo_talla') === 'UNICA')>Accesorios (Talla Única)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label">Estado del Atributo</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Cualquier estado</option>
                        <option value="activa" @selected(request('estado') === 'activa')>Activas</option>
                        <option value="inactiva" @selected(request('estado') === 'inactiva')>Inactivas</option>
                        <option value="eliminada" @selected(request('estado') === 'eliminada')>Eliminadas</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <label for="per_page" class="form-label">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select shadow-sm">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6 d-flex align-items-end gap-2">
                    <a href="{{ route('tallas.index') }}" class="btn btn-outline-secondary w-100 fw-bold bg-white shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header soft-header p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-ruler-combined"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Matriz de Dimensiones</h5>
                <div class="text-muted small">Estandarización de numeración para el control de SKUs e inventario.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 150px;">Abreviatura (Cód)</th>
                            <th style="min-width: 200px;">Descripción / Nombre Completo</th>
                            <th style="min-width: 140px;">Categoría Asignada</th>
                            <th class="text-center" style="width: 110px;">Prioridad Vis.</th>
                            <th class="text-center" style="width: 130px;">Estado</th>
                            @can('gestionar_tallas')
                                <th class="text-center pe-4" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tallas as $talla)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $talla->codigo }}</div>
                                    <div class="small text-muted mt-1 font-monospace">SysID: {{ $talla->id }}</div>
                                </td>
                                <td class="fw-medium text-dark py-3">{{ $talla->nombre }}</td>
                                <td class="py-3">
                                    @if($talla->tipo_talla === 'CALZADO')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-shoe-prints me-1"></i>Calzado</span>
                                    @elseif($talla->tipo_talla === 'ROPA')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-tshirt me-1"></i>Ropa</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-cube me-1"></i>Única/Acc.</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold text-dark py-3">{{ $talla->orden }}</td>
                                <td class="text-center py-3">
                                    @if($talla->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Baja</span>
                                    @elseif((int) $talla->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Activa</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Bloqueada</span>
                                    @endif
                                </td>

                                @can('gestionar_tallas')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2" role="group">
                                            <a href="{{ route('tallas.edit', $talla) }}" class="btn btn-sm btn-light border text-primary" title="Modificar Parámetros">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light border {{ $talla->trashed() ? 'text-success' : 'text-danger' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmModal-{{ $talla->id }}"
                                                title="{{ $talla->trashed() ? 'Habilitar Talla' : 'Dar de Baja Talla' }}">
                                                <i class="fas {{ $talla->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_tallas') ? 6 : 5 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-ruler-vertical text-muted fs-2 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Sin Registros de Tallas</h5>
                                        <p class="text-muted mb-0">No existen medidas para la configuración de búsqueda indicada.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small fw-medium">
                    Mostrando del <span class="fw-bold text-dark">{{ $tallas->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $tallas->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $tallas->total() }}</span> medidas
                </div>
                <div class="pagination-custom">
                    {{ $tallas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@can('gestionar_tallas')
    @foreach($tallas as $talla)
        <div class="modal fade" id="confirmModal-{{ $talla->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center p-4 pb-5">
                        @if($talla->trashed())
                            <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Habilitar medida?</h4>
                            <p class="text-muted mb-4">El parámetro <strong>{{ $talla->codigo }} ({{ $talla->nombre }})</strong> podrá ser utilizado nuevamente para registrar ingresos de calzado/ropa.</p>
                        @else
                            <div class="text-danger mb-3"><i class="fas fa-ban fa-4x opacity-75"></i></div>
                            <h4 class="fw-bold text-dark">¿Dar de baja medida?</h4>
                            <p class="text-muted mb-4">La variante de talla <strong>{{ $talla->codigo }}</strong> será bloqueada y no aparecerá al asociar SKUs.</p>
                        @endif
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('tallas.destroy', $talla) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn {{ $talla->trashed() ? 'btn-success' : 'btn-danger' }} fw-bold px-4 rounded-pill shadow-sm">
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