@extends('layouts.app')
@section('title', 'Categorías')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Líneas de Producto (Categorías)</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Categorías</li>
            </ol>
        </div>

        @can('gestionar_categorias')
            <a href="{{ route('categorias.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="fas fa-plus me-2"></i>Crear Categoría
            </a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('categorias.index') }}" id="filtro-form" class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-6">
                    <label for="q" class="form-label text-muted small fw-bold text-uppercase">Buscar Categoría</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Ej. Zapatillas Running, Poleras...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="estado" class="form-label text-muted small fw-bold text-uppercase">Disponibilidad</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Cualquier estado</option>
                        <option value="activa" @selected(request('estado') === 'activa')>Solo Activas</option>
                        <option value="inactiva" @selected(request('estado') === 'inactiva')>Solo Inactivas</option>
                        <option value="eliminada" @selected(request('estado') === 'eliminada')>En Papelera</option>
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
                    <a href="{{ route('categorias.index') }}" class="btn btn-light w-100 fw-bold border shadow-sm">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                <i class="fa-solid fa-tags fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Estructura del Catálogo</h5>
                <div class="text-muted small mt-1">Familias de productos para organizar el inventario y tienda virtual.</div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0" style="min-width: 220px;">Nombre Comercial</th>
                            <th class="border-bottom-0">Descripción</th>
                            <th class="text-center border-bottom-0" style="width: 130px;">Estado</th>
                            @can('gestionar_categorias')
                                <th class="text-center pe-4 border-bottom-0" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categorias as $categoria)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6">{{ $categoria->nombre }}</div>
                                    <div class="small text-muted font-monospace mt-1">ID Interno: {{ str_pad($categoria->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="text-muted py-3">
                                    {{ \Illuminate\Support\Str::limit($categoria->descripcion, 80, '...') ?: 'Sin descripción registrada' }}
                                </td>
                                <td class="text-center py-3">
                                    @if($categoria->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-trash me-1"></i> Eliminada</span>
                                    @elseif((int) $categoria->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Activa</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-ban me-1"></i> Inactiva</span>
                                    @endif
                                </td>
                                @can('gestionar_categorias')
                                    <td class="text-center pe-4 py-3">
                                        <div class="btn-group shadow-sm bg-white rounded-2" role="group">
                                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-light border text-primary" title="Editar Categoría">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Botón unificado que lanza el Modal Global -->
                                            <button type="button"
                                                class="btn btn-sm btn-light border btn-confirmar {{ $categoria->trashed() ? 'text-success' : 'text-danger' }}"
                                                data-id="{{ $categoria->id }}"
                                                data-nombre="{{ $categoria->nombre }}"
                                                data-eliminado="{{ $categoria->trashed() ? 'true' : 'false' }}"
                                                title="{{ $categoria->trashed() ? 'Restaurar' : 'Eliminar' }}">
                                                <i class="fas {{ $categoria->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_categorias') ? 4 : 3 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-box-open text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Catálogo Vacío</h5>
                                        <p class="text-muted mb-0">No se han encontrado categorías activas con tu búsqueda.</p>
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
                Mostrando del <span class="fw-bold text-dark">{{ $categorias->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $categorias->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $categorias->total() }}</span> registros
            </div>
            <div class="pagination-custom">
                {{ $categorias->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@can('gestionar_categorias')
<!-- MODAL GLOBAL -->
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
                        <button type="submit" id="modalBtnConfirm" class="btn fw-bold px-4 rounded-pill shadow-sm">
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
            typingTimer = setTimeout(() => { form.submit(); }, 500);
        });

        const modalConfirmObj = document.getElementById('modalConfirmacionGlobal');
        if (modalConfirmObj) {
            const modalConfirm = new bootstrap.Modal(modalConfirmObj);
            
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-confirmar');
                if (btn) {
                    const { id, nombre, eliminado } = btn.dataset;
                    
                    const escapeHtml = (text) => text.replace(/[&<>"']/g, match => ({
                        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                    })[match]);
                    const safeNombre = escapeHtml(nombre || 'la categoría');

                    document.getElementById('formConfirmacionGlobal').action = `/categorias/${id}`; 
                    
                    const icon = document.getElementById('modalIcon');
                    const iconContainer = document.getElementById('modalIconContainer');
                    const title = document.getElementById('modalTitle');
                    const desc = document.getElementById('modalDesc');
                    const btnSubmit = document.getElementById('modalBtnConfirm');

                    if (eliminado === 'false') {
                        icon.className = 'fas fa-ban fa-3x text-danger';
                        iconContainer.className = 'bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                        title.textContent = '¿Archivar categoría?';
                        desc.innerHTML = `La línea <strong>${safeNombre}</strong> dejará de mostrarse al clasificar nuevos productos.`;
                        btnSubmit.className = 'btn btn-danger fw-bold px-4 rounded-pill shadow-sm';
                    } else {
                        icon.className = 'fas fa-check-circle fa-3x text-success';
                        iconContainer.className = 'bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                        title.textContent = '¿Restaurar categoría?';
                        desc.innerHTML = `La línea <strong>${safeNombre}</strong> volverá a estar activa en todo el sistema.`;
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