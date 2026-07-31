@extends('layouts.app')
@section('title', 'Roles y Permisos')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Control de Accesos</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Roles y Permisos</li>
            </ol>
        </div>

        @can('gestionar_roles_permisos')
            <a href="{{ route('roles.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Rol
            </a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-users-gear fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Estructura Organizacional</h5>
                    <div class="text-muted small mt-1">Define qué puede ver y hacer cada perfil dentro de la plataforma.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="p-4 bg-light bg-opacity-50 border-bottom">
                <form method="GET" action="{{ route('roles.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-8 col-md-7">
                        <label for="q" class="form-label text-muted small fw-bold text-uppercase">Buscar Perfil</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Ej. Administrador, Cajero...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <label for="per_page" class="form-label text-muted small fw-bold text-uppercase">Mostrar</label>
                        <select name="per_page" id="per_page" class="form-select shadow-sm" onchange="this.form.submit()">
                            @foreach([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }} registros</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <a href="{{ route('roles.index') }}" class="btn btn-light fw-medium border w-100 shadow-sm">Limpiar</a>
                    </div>
                </form>
            </div>

            <div class="table-responsive bg-white">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0" style="min-width: 250px;">Perfil de Usuario</th>
                            <th class="border-bottom-0">Permisos Asignados</th>
                            <th class="text-center border-bottom-0" style="width: 120px;">Métricas</th>
                            @can('gestionar_roles_permisos')
                                <th class="text-center pe-4 border-bottom-0" style="width: 100px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            @php
                                $roleName = strtolower($role->name);
                                $themeClass = match(true) {
                                    str_contains($roleName, 'admin') => 'primary',
                                    str_contains($roleName, 'vendedor') => 'success',
                                    str_contains($roleName, 'cajero') => 'info',
                                    str_contains($roleName, 'almacen') => 'warning',
                                    default => 'secondary'
                                };
                                
                                $iconClass = match(true) {
                                    str_contains($roleName, 'admin') => 'fa-user-shield',
                                    str_contains($roleName, 'vendedor') => 'fa-tags',
                                    str_contains($roleName, 'cajero') => 'fa-cash-register',
                                    str_contains($roleName, 'almacen') => 'fa-boxes-stacked',
                                    default => 'fa-user-tag'
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-{{ $themeClass }} bg-opacity-10 text-{{ $themeClass }} rounded-3 d-flex align-items-center justify-content-center border border-{{ $themeClass }} border-opacity-25" style="width: 45px; height: 45px;">
                                            <i class="fas {{ $iconClass }} fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6 text-capitalize">{{ $role->name }}</div>
                                            <div class="small text-muted font-monospace"><i class="fas fa-fingerprint me-1"></i> ID: {{ str_pad($role->id, 3, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-wrap" style="min-width: 300px;">
                                    @if(strtolower($role->name) === 'administrador')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3 rounded-pill shadow-sm">
                                            <i class="fas fa-star text-warning me-1"></i> Acceso Total al Sistema
                                        </span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse($role->permissions->take(6) as $permission)
                                                <span class="badge bg-light text-secondary border border-secondary border-opacity-25 px-2 py-1 fw-medium" title="{{ $permission->name }}">
                                                    {{ Str::headline(str_replace('_', ' ', $permission->name)) }}
                                                </span>
                                            @empty
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i> Sin accesos configurados</span>
                                            @endforelse

                                            @if($role->permissions_count > 6)
                                                <span class="badge bg-secondary px-2 py-1">
                                                    +{{ $role->permissions_count - 6 }} más
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark fs-5">{{ $role->permissions_count }}</div>
                                    <div class="small text-muted text-uppercase" style="font-size: 0.65rem;">Permisos</div>
                                </td>
                                @can('gestionar_roles_permisos')
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Modificar Perfil">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(strtolower($role->name) !== 'administrador')
                                                <button type="button" class="btn btn-sm btn-light border text-danger btn-delete-role" 
                                                        data-id="{{ $role->id }}" 
                                                        data-name="{{ $role->name }}" 
                                                        title="Eliminar Perfil">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-shield-alt text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No hay roles configurados</h5>
                                        <p class="text-muted mb-0">Comienza creando un rol organizativo para asegurar tu sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small fw-medium">
                    Mostrando <span class="fw-bold text-dark">{{ $roles->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $roles->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $roles->total() }}</span> perfiles
                </div>
                <div class="pagination-custom">
                    {{ $roles->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL GLOBAL PARA ELIMINAR ROL -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="fas fa-trash-alt fa-2x"></i>
                </div>
                <h4 class="fw-bold text-dark mb-3">¿Eliminar Perfil?</h4>
                <p class="text-muted mb-4">El rol <strong class="text-dark text-capitalize" id="roleNameToDelete"></strong> será borrado permanentemente y los usuarios asociados perderán sus accesos.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formDeleteRole" action="" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm">Sí, Eliminar</button>
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
        tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });

        const modalDeleteEl = document.getElementById('modalConfirmDelete');
        if(modalDeleteEl) {
            const modalDelete = new bootstrap.Modal(modalDeleteEl);
            document.querySelectorAll('.btn-delete-role').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    document.getElementById('roleNameToDelete').textContent = name;
                    document.getElementById('formDeleteRole').action = `/roles/${id}`;
                    modalDelete.show();
                });
            });
        }
    });
</script>
@endpush