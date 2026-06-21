@extends('layouts.app')
@section('title', 'Directorio de Proveedores')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .table-soft th { background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: .05em; white-space: nowrap; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; }
    .fs-7 { font-size: 0.875rem; }
    .filters-row .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 0.3rem; }
    .pagination-custom nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }
    .pagination-custom nav > div.d-flex.justify-content-between.d-sm-none { display: none !important; }
    .pagination-custom .pagination { margin-bottom: 0; gap: .25rem; }
    .pagination-custom .page-link { border-radius: .5rem; padding: .45rem .75rem; font-size: .875rem; border: 1px solid #e2e8f0; color: #475569; }
    .pagination-custom .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 600; }
</style>
@endpush

@section('content')
@php
    $qActual = request('q');
    $tipoActual = request('tipo_persona');
    $estadoActual = request('estado');
@endphp

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-0">Gestión de Proveedores</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Logística y Suministro</li>
            </ol>
        </div>

        @can('gestionar_proveedores')
            <div>
                <a href="{{ route('proveedores.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-truck-loading me-2"></i>Registrar Proveedor
                </a>
            </div>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('proveedores.index') }}" id="filtro-form" class="row g-3 filters-row">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Búsqueda Logística</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="search_q" class="form-control border-start-0 ps-0" value="{{ $qActual }}" placeholder="RUC, Marca comercial, correo...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Tipo Entidad</label>
                    <select name="tipo_persona" class="form-select shadow-sm">
                        <option value="">Todas</option>
                        <option value="natural" @selected($tipoActual === 'natural')>Natural</option>
                        <option value="juridica" @selected($tipoActual === 'juridica')>Empresa (Jurídica)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label">Relación Comercial</label>
                    <select name="estado" class="form-select shadow-sm">
                        <option value="">Todas</option>
                        <option value="1" @selected((string) $estadoActual === '1')>Activa</option>
                        <option value="0" @selected((string) $estadoActual === '0')>Suspendida</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label">Registros por pág.</label>
                    <select name="per_page" class="form-select shadow-sm">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 d-flex align-items-end">
                    <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary w-100 fw-bold bg-white shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Directorio de Abastecimiento</h5>
                <div class="text-muted small">Distribuidores y mayoristas de zapatillas, ropa y accesorios.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover table-soft align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Entidad / Razón Social</th>
                            <th>Ubicación Fiscal</th>
                            <th>Identificación (RUC)</th>
                            <th class="text-center">Modalidad</th>
                            <th class="text-center">Estado</th>
                            @can('gestionar_proveedores')
                                <th class="text-center pe-4">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $item)
                            @php
                                $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
                                $persona = $item->persona;
                                $nombreMostrado = $persona?->tipo_persona === 'juridica'
                                    ? ($persona?->razon_social ?? 'Sin razón social')
                                    : trim(($persona?->nombres ?? '') . ' ' . ($persona?->apellidos ?? ''));
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $nombreMostrado ?: 'Sin nombre' }}</div>
                                    @if($persona?->email)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-envelope text-primary text-opacity-50 me-1"></i>{{ $persona->email }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="text-muted small text-truncate" style="max-width: 250px;" title="{{ $persona?->direccion }}">
                                        <i class="fas fa-map-marker-alt text-danger text-opacity-50 me-1"></i>{{ $persona?->direccion ?? 'No registrada' }}
                                    </div>
                                    @if($persona?->telefono)
                                        <div class="text-muted small mt-1 font-monospace">
                                            <i class="fas fa-phone-alt text-success text-opacity-50 me-1"></i>{{ $persona->telefono }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">
                                        {{ optional($persona?->documento)->tipo_documento ?? 'Sin doc.' }}
                                    </div>
                                    <div class="font-monospace text-dark fs-7">
                                        {{ $persona?->numero_documento ?? '—' }}
                                    </div>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill shadow-sm fs-7">
                                        {{ ucfirst($persona?->tipo_persona ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="text-center py-3">
                                    @if($estaEliminado)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Bloqueado</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm">Aprobado</span>
                                    @endif
                                </td>
                                @can('gestionar_proveedores')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2" role="group">
                                            <a href="{{ route('proveedores.edit', $item) }}" class="btn btn-sm btn-light border text-primary" title="Actualizar datos">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light border {{ $estaEliminado ? 'text-success' : 'text-danger' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmModal-{{ $item->id }}"
                                                title="{{ $estaEliminado ? 'Restaurar Entidad' : 'Suspender Entidad' }}">
                                                <i class="fas {{ $estaEliminado ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_proveedores') ? 6 : 5 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-truck-ramp-box text-muted fs-2 opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Directorio logístico vacío</h6>
                                        <p class="text-muted small mb-0">No se encontraron proveedores que coincidan con la búsqueda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top border-light p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="text-muted small fw-medium">
                Viendo del <span class="text-dark fw-bold">{{ $proveedores->firstItem() ?? 0 }}</span> al <span class="text-dark fw-bold">{{ $proveedores->lastItem() ?? 0 }}</span> de <span class="text-dark fw-bold">{{ $proveedores->total() }}</span> registros
            </div>
            <div class="pagination-custom">
                {{ $proveedores->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@foreach($proveedores as $item)
    @php
        $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
        $persona = $item->persona;
        $nombreMostrado = $persona?->tipo_persona === 'juridica'
            ? ($persona?->razon_social ?? 'Sin razón social')
            : trim(($persona?->nombres ?? '') . ' ' . ($persona?->apellidos ?? ''));
    @endphp
    <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center p-4 pb-5">
                    @if($estaEliminado)
                        <div class="text-success mb-3"><i class="fas fa-check-circle fa-4x opacity-75"></i></div>
                        <h4 class="fw-bold text-dark">¿Restaurar proveedor?</h4>
                        <p class="text-muted mb-4">La entidad <strong>{{ $nombreMostrado }}</strong> podrá volver a registrar compras y facturas al sistema.</p>
                    @else
                        <div class="text-danger mb-3"><i class="fas fa-ban fa-4x opacity-75"></i></div>
                        <h4 class="fw-bold text-dark">¿Suspender proveedor?</h4>
                        <p class="text-muted mb-4">La entidad comercial <strong>{{ $nombreMostrado }}</strong> será bloqueada de la lista de ingresos.</p>
                    @endif
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('proveedores.destroy', $item) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn {{ $estaEliminado ? 'btn-success' : 'btn-danger' }} fw-bold px-4 rounded-pill shadow-sm">
                                Confirmar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filtro-form');
        const searchInput = document.getElementById('search_q');

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