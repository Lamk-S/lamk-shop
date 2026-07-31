@extends('layouts.app')
@section('title', 'Directorio de Usuarios')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Directorio de Usuarios</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Personal y Accesos</li>
            </ol>
        </div>

        @can('gestionar_usuarios')
            <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="fas fa-user-plus me-2"></i>Registrar Empleado
            </a>
        @endcan
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label for="q" class="form-label text-muted small fw-bold text-uppercase">Búsqueda Rápida</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Nombre o correo...">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="role" class="form-label text-muted small fw-bold text-uppercase">Rol Asignado</label>
                    <select name="role" id="role" class="form-select shadow-sm">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ Str::headline($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label text-muted small fw-bold text-uppercase">Estado</label>
                    <select name="estado" id="estado" class="form-select shadow-sm">
                        <option value="">Cualquiera</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Suspendidos</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 d-flex gap-2 justify-content-end mt-3 mt-lg-0">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-medium">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-light border shadow-sm" data-bs-toggle="tooltip" title="Limpiar filtros">
                        <i class="fas fa-eraser"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">Perfil del Usuario</th>
                            <th class="border-bottom-0">Contacto Corporativo</th>
                            <th class="border-bottom-0">Rol del Sistema</th>
                            <th class="text-center border-bottom-0">Estado</th>
                            @can('gestionar_usuarios')
                                <th class="text-center pe-4 border-bottom-0">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $item)
                            @php
                                $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
                                $isActive = !$estaEliminado && (int) $item->estado === 1;
                                $rolPrincipal = strtolower($item->roles->first()?->name ?? 'sin rol');
                                
                                $badgeColor = match(true) {
                                    str_contains($rolPrincipal, 'admin') => 'primary',
                                    str_contains($rolPrincipal, 'cajero') => 'info',
                                    str_contains($rolPrincipal, 'vendedor') => 'success',
                                    default => 'secondary'
                                };
                                
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($item->name) . "&background=random&color=fff&bold=true";
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                                            <span class="position-absolute bottom-0 end-0 p-1 border border-light rounded-circle bg-{{ $isActive ? 'success' : 'danger' }}" title="{{ $isActive ? 'Activo' : 'Suspendido' }}"></span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                            <div class="small text-muted font-monospace"><i class="fas fa-id-card me-1"></i> ID: {{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $item->email }}" class="text-decoration-none text-dark fw-medium">
                                        <i class="far fa-envelope text-muted me-1"></i> {{ $item->email }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} border-opacity-25 px-2 py-1 rounded">
                                        <i class="fas {{ str_contains($rolPrincipal, 'admin') ? 'fa-crown text-warning' : 'fa-user-tag' }} me-1"></i> 
                                        {{ Str::headline($rolPrincipal) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($isActive)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill"><i class="fas fa-check-circle me-1"></i> Operativo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill"><i class="fas fa-lock me-1"></i> Suspendido</span>
                                    @endif
                                </td>
                                @can('gestionar_usuarios')
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('users.edit', $item) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Editar Credenciales">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(Auth::id() !== $item->id)
                                                <button type="button" 
                                                    class="btn btn-sm btn-light border {{ $isActive ? 'text-danger' : 'text-success' }} btn-toggle-status" 
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->name }}"
                                                    data-active="{{ $isActive ? '1' : '0' }}"
                                                    title="{{ $isActive ? 'Desactivar Acceso' : 'Restaurar Cuenta' }}">
                                                    <i class="fas {{ $isActive ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-user-times text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No se encontraron resultados</h5>
                                        <p class="text-muted mb-0">No hay usuarios que coincidan con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <form method="GET" action="{{ route('users.index') }}" class="d-flex align-items-center gap-2">
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                <select name="per_page" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                    @foreach ([10, 15, 25, 50] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <div class="text-muted small border-start ps-3 ms-2">
                    Viendo {{ $users->firstItem() ?? 0 }} a {{ $users->lastItem() ?? 0 }} de {{ $users->total() }} registros
                </div>
            </form>
            <div class="pagination-custom">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL GLOBAL PARA DESACTIVAR/ACTIVAR -->
<div class="modal fade" id="modalConfirmStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <div class="mb-4" id="modalIconContainer">
                    <!-- Icono inyectado por JS -->
                </div>
                <h4 class="fw-bold text-dark mb-2" id="modalTitleStatus"></h4>
                <p class="text-muted mb-4" id="modalMessageStatus"></p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formToggleStatus" action="" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" id="btnSubmitStatus" class="btn fw-bold px-4 rounded-pill shadow-sm">
                            Confirmar Acción
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const modalStatusEl = document.getElementById('modalConfirmStatus');
        if(modalStatusEl){
            const modalStatus = new bootstrap.Modal(modalStatusEl);
            
            document.querySelectorAll('.btn-toggle-status').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const isActive = this.dataset.active === '1';

                    document.getElementById('formToggleStatus').action = `/users/${id}`;
                    
                    const iconContainer = document.getElementById('modalIconContainer');
                    const btnSubmit = document.getElementById('btnSubmitStatus');
                    
                    if(isActive) {
                        iconContainer.innerHTML = `<div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-user-lock fa-2x"></i></div>`;
                        document.getElementById('modalTitleStatus').textContent = '¿Suspender usuario?';
                        document.getElementById('modalMessageStatus').innerHTML = `El usuario <strong>${name}</strong> perderá el acceso inmediatamente. Sus operaciones pasadas se conservarán.`;
                        btnSubmit.className = 'btn btn-danger fw-bold px-4 rounded-pill shadow-sm';
                    } else {
                        iconContainer.innerHTML = `<div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-user-check fa-2x"></i></div>`;
                        document.getElementById('modalTitleStatus').textContent = '¿Reactivar acceso?';
                        document.getElementById('modalMessageStatus').innerHTML = `El usuario <strong>${name}</strong> recuperará acceso al sistema con sus permisos previos.`;
                        btnSubmit.className = 'btn btn-success fw-bold px-4 rounded-pill shadow-sm';
                    }

                    modalStatus.show();
                });
            });
        }
    });
</script>
@endpush