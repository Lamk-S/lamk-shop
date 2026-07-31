@extends('layouts.app')
@section('title', 'Control de Medidas y Tallas')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Parámetros de Medida (Tallas)</h2>
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

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('tallas.index') }}" id="filtro-form" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="q" class="form-label text-muted small fw-bold text-uppercase">Buscar Medida</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Ej. XL, 42...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="tipo_talla" class="form-label text-muted small fw-bold text-uppercase">Familia / Tipo</label>
                    <select name="tipo_talla" id="tipo_talla" class="form-select shadow-sm">
                        <option value="">Todas las Familias</option>
                        <option value="CALZADO" @selected(request('tipo_talla') === 'CALZADO')>Solo Calzado (Zapatillas)</option>
                        <option value="ROPA" @selected(request('tipo_talla') === 'ROPA')>Solo Ropa (Deportiva)</option>
                        <option value="UNICA" @selected(request('tipo_talla') === 'UNICA')>Accesorios (Talla Única)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label text-muted small fw-bold text-uppercase">Estado</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Cualquier estado</option>
                        <option value="activa" @selected(request('estado') === 'activa')>Activas</option>
                        <option value="inactiva" @selected(request('estado') === 'inactiva')>Inactivas</option>
                        <option value="eliminada" @selected(request('estado') === 'eliminada')>Eliminadas</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <label for="per_page" class="form-label text-muted small fw-bold text-uppercase">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select shadow-sm">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }} filas</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <a href="{{ route('tallas.index') }}" class="btn btn-light w-100 fw-bold border shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-ruler-combined fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Matriz de Dimensiones</h5>
                <div class="text-muted small mt-1">Estandarización de numeración para el control de SKUs e inventario.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover mb-0 align-middle text-nowrap">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0" style="min-width: 150px;">Abreviatura</th>
                            <th class="border-bottom-0" style="min-width: 200px;">Descripción</th>
                            <th class="border-bottom-0" style="min-width: 140px;">Categoría</th>
                            <th class="text-center border-bottom-0" style="width: 110px;">Prioridad</th>
                            <th class="text-center border-bottom-0" style="width: 130px;">Estado</th>
                            @can('gestionar_tallas')
                                <th class="text-center pe-4 border-bottom-0" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tallas as $talla)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $talla->codigo }}</div>
                                    <div class="small text-muted mt-1 font-monospace"><i class="fas fa-barcode me-1"></i> ID: {{ str_pad($talla->id, 4, '0', STR_PAD_LEFT) }}</div>
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
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-trash me-1"></i> De Baja</span>
                                    @elseif((int) $talla->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Activa</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-ban me-1"></i> Inactiva</span>
                                    @endif
                                </td>
                                @can('gestionar_tallas')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2">
                                            <a href="{{ route('tallas.edit', $talla) }}" class="btn btn-sm btn-light border text-primary" title="Modificar Parámetros">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-light border btn-toggle-status {{ $talla->trashed() ? 'text-success' : 'text-danger' }}"
                                                    data-id="{{ $talla->id }}"
                                                    data-name="{{ $talla->codigo }} - {{ $talla->nombre }}"
                                                    data-trashed="{{ $talla->trashed() ? '1' : '0' }}"
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
                                            <i class="fas fa-ruler-vertical text-muted fs-1 opacity-50"></i>
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

            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
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
<!-- MODAL -->
<div class="modal fade" id="modalConfirmStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-4 pb-5">
                <div id="modalIconContainer" class="mb-4">
                    <i id="modalIcon" class="fas fa-ban fa-4x opacity-75"></i>
                </div>
                <h4 id="modalTitle" class="fw-bold text-dark mb-3">¿Cambiar estado?</h4>
                <p id="modalMessage" class="text-muted mb-4"></p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formToggleStatus" action="" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" id="btnSubmitModal" class="btn fw-bold px-4 rounded-pill shadow-sm">
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

        const modalStatusEl = document.getElementById('modalConfirmStatus');
        if (modalStatusEl) {
            const modalStatus = new bootstrap.Modal(modalStatusEl);
            document.querySelectorAll('.btn-toggle-status').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const isTrashed = this.dataset.trashed === '1';

                    document.getElementById('formToggleStatus').action = `/tallas/${id}`;
                    
                    const title = document.getElementById('modalTitle');
                    const message = document.getElementById('modalMessage');
                    const btnSubmit = document.getElementById('btnSubmitModal');
                    const icon = document.getElementById('modalIcon');
                    const iconContainer = document.getElementById('modalIconContainer');

                    if (isTrashed) {
                        title.textContent = '¿Habilitar medida?';
                        message.innerHTML = `La medida <strong>${name}</strong> podrá ser utilizada nuevamente para registrar ingresos.`;
                        btnSubmit.className = 'btn btn-success fw-bold px-4 rounded-pill shadow-sm';
                        icon.className = 'fas fa-check-circle fa-3x text-success';
                        iconContainer.className = 'bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                    } else {
                        title.textContent = '¿Dar de baja medida?';
                        message.innerHTML = `La variante <strong>${name}</strong> será bloqueada y no aparecerá al asociar SKUs.`;
                        btnSubmit.className = 'btn btn-danger fw-bold px-4 rounded-pill shadow-sm';
                        icon.className = 'fas fa-trash-alt fa-3x text-danger';
                        iconContainer.className = 'bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                    }
                    
                    iconContainer.style.width = '80px';
                    iconContainer.style.height = '80px';
                    
                    modalStatus.show();
                });
            });
        }
    });
</script>
@endpush