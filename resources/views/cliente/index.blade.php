@extends('layouts.app')
@section('title', 'Directorio de Clientes')

@section('content')
@php
    $qActual = request('q');
    $tipoActual = request('tipo_persona');
    $estadoActual = request('estado');
@endphp

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Directorio de Clientes</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Clientes Frecuentes</li>
            </ol>
        </div>

        @can('gestionar_clientes')
            <div>
                <a href="{{ route('clientes.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-user-plus me-2"></i>Registrar Cliente
                </a>
            </div>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('clientes.index') }}" id="filtro-form" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label for="search_q" class="form-label text-muted small fw-bold text-uppercase">Búsqueda Inteligente</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="search_q" class="form-control border-start-0 ps-0" value="{{ $qActual }}" placeholder="DNI, RUC, nombres, correo...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="tipo_persona" class="form-label text-muted small fw-bold text-uppercase">Tipo Perfil</label>
                    <select name="tipo_persona" id="tipo_persona" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="natural" @selected($tipoActual === 'natural')>Natural</option>
                        <option value="juridica" @selected($tipoActual === 'juridica')>Jurídica</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label for="estado" class="form-label text-muted small fw-bold text-uppercase">Estado de Cuenta</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="1" @selected((string) $estadoActual === '1')>Activos</option>
                        <option value="0" @selected((string) $estadoActual === '0')>Inactivos</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label for="per_page" class="form-label text-muted small fw-bold text-uppercase">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select shadow-sm">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <a href="{{ route('clientes.index') }}" class="btn btn-light w-100 fw-bold border shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-address-book fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Cartera de Clientes</h5>
                <div class="text-muted small mt-1">Personas naturales y empresas registradas para facturación y envíos.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover align-middle text-nowrap mb-0">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">Nombre / Razón Social</th>
                            <th class="border-bottom-0">Identificación</th>
                            <th class="border-bottom-0">Contacto y Ubicación</th>
                            <th class="text-center border-bottom-0">Tipo</th>
                            <th class="text-center border-bottom-0">Estado</th>
                            @can('gestionar_clientes')
                                <th class="text-center pe-4 border-bottom-0">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $item)
                            @php
                                $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
                                $persona = $item->persona;
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $persona?->nombre_completo ?? 'Sin entidad asignada' }}</div>
                                    @if($persona?->email)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-envelope text-primary text-opacity-50 me-1"></i>{{ $persona->email }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">
                                        {{ optional($persona?->documento)->tipo_documento ?? 'Sin documento' }}
                                    </div>
                                    <div class="font-monospace text-dark fs-7">
                                        {{ $persona?->numero_documento ?? '—' }}
                                    </div>
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
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill shadow-sm fs-7">
                                        {{ ucfirst($persona?->tipo_persona->value ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="text-center py-3">
                                    @if($estaEliminado)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-ban me-1"></i> Inactivo</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Activo</span>
                                    @endif
                                </td>
                                @can('gestionar_clientes')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2" role="group">
                                            <a href="{{ route('clientes.edit', $item) }}" class="btn btn-sm btn-light border text-primary" title="Editar ficha">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light border btn-confirmar {{ $estaEliminado ? 'text-success' : 'text-danger' }}"
                                                data-id="{{ $item->id }}"
                                                data-nombre="{{ $persona?->nombre_completo }}"
                                                data-accion="{{ $estaEliminado ? 'restaurar' : 'bloquear' }}"
                                                title="{{ $estaEliminado ? 'Restaurar' : 'Bloquear/Eliminar' }}">
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
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-user-tag text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Directorio vacío o sin coincidencias</h6>
                                        <p class="text-muted small mb-0">No se encontraron clientes bajo los filtros aplicados.</p>
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
                Viendo del <span class="text-dark fw-bold">{{ $clientes->firstItem() ?? 0 }}</span> al <span class="text-dark fw-bold">{{ $clientes->lastItem() ?? 0 }}</span> de <span class="text-dark fw-bold">{{ $clientes->total() }}</span> registros
            </div>
            <div class="pagination-custom">
                {{ $clientes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@can('gestionar_clientes')
<div class="modal fade" id="modalConfirmacionGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-4 pb-5">
                <div id="modalIconContainer" class="mb-4">
                    <i id="modalIcon" class="fas fa-ban fa-4x opacity-75"></i>
                </div>
                <h4 class="fw-bold text-dark" id="modalTitle">¿Cambiar estado?</h4>
                <p class="text-muted mb-4" id="modalDesc"></p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formConfirmacionGlobal" action="" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn fw-bold px-4 rounded-pill shadow-sm" id="modalBtnConfirm">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
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

        const modalConfirmObj = document.getElementById('modalConfirmacionGlobal');
        if (modalConfirmObj) {
            const modalConfirm = new bootstrap.Modal(modalConfirmObj);
            
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-confirmar');
                if (btn) {
                    const { id, nombre, accion } = btn.dataset;
                    
                    const form = document.getElementById('formConfirmacionGlobal');
                    form.action = `/clientes/${id}`;
                    
                    const icon = document.getElementById('modalIcon');
                    const iconContainer = document.getElementById('modalIconContainer');
                    const title = document.getElementById('modalTitle');
                    const desc = document.getElementById('modalDesc');
                    const btnSubmit = document.getElementById('modalBtnConfirm');

                    if (accion === 'bloquear') {
                        icon.className = 'fas fa-ban fa-3x text-danger';
                        iconContainer.className = 'bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                        title.textContent = '¿Bloquear cliente?';
                        desc.innerHTML = `El registro de <strong>${nombre}</strong> pasará a estar inactivo.`;
                        btnSubmit.className = 'btn btn-danger fw-bold px-4 rounded-pill shadow-sm';
                    } else {
                        icon.className = 'fas fa-check-circle fa-3x text-success';
                        iconContainer.className = 'bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                        title.textContent = '¿Restaurar acceso comercial?';
                        desc.innerHTML = `El registro de <strong>${nombre}</strong> volverá a estar operativo.`;
                        btnSubmit.className = 'btn btn-success fw-bold px-4 rounded-pill shadow-sm';
                    }
                    
                    iconContainer.style.width = '80px';
                    iconContainer.style.height = '80px';

                    modalConfirm.show();
                }
            });
        }
    });
</script>
@endpush