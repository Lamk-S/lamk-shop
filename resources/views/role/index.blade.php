@extends('layouts.app')
@section('title', 'Roles y Permisos')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); overflow: hidden; background: #fff; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #edf2f7; }
    .table-soft th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
    .table-soft td { vertical-align: middle; color: #334155; border-bottom: 1px solid #f1f5f9; }
    .role-avatar { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: bold; }
    .role-admin { background: #f3e8ff; color: #9333ea; border: 1px solid #e9d5ff; }
    .role-vendedor { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .role-cajero { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
    .role-almacen { background: #ffedd5; color: #ea580c; border: 1px solid #fed7aa; }
    .role-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .chip { display: inline-flex; align-items: center; gap: .35rem; padding: .25rem .6rem; border-radius: 6px; font-size: .75rem; font-weight: 600; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; margin: .15rem; white-space: nowrap; transition: all 0.2s; }
    .chip:hover { border-color: #cbd5e1; background: #fff; }
    .chip-accent { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
    .table-actions .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 8px; }
    .empty-state { padding: 4rem 1rem; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Control de Accesos</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Roles y Permisos</li>
            </ol>
        </div>

        @can('gestionar_roles_permisos')
            <a href="{{ route('roles.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-bold">
                <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Rol
            </a>
        @endcan
    </div>

    <div class="card soft-card">
        <div class="card-header soft-header p-4">
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
            <div class="p-4 bg-white border-bottom">
                <form method="GET" action="{{ route('roles.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-8 col-md-7">
                        <label for="q" class="form-label text-muted small fw-bold text-uppercase tracking-wider">Buscar Perfil</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Ej. Administrador, Cajero...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <label for="per_page" class="form-label text-muted small fw-bold text-uppercase tracking-wider">Mostrar</label>
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

            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 250px;">Perfil de Usuario</th>
                            <th>Permisos Asignados</th>
                            <th class="text-center" style="width: 120px;">Métricas</th>
                            @can('gestionar_roles_permisos')
                                <th class="text-center pe-4" style="width: 100px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            @php
                                $roleName = strtolower($role->name);
                                $avatarClass = match(true) {
                                    str_contains($roleName, 'admin') => 'role-admin',
                                    str_contains($roleName, 'vendedor') => 'role-vendedor',
                                    str_contains($roleName, 'cajero') => 'role-cajero',
                                    str_contains($roleName, 'almacen') => 'role-almacen',
                                    default => 'role-default'
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
                                        <div class="role-avatar {{ $avatarClass }}">
                                            <i class="fas {{ $iconClass }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6 text-capitalize">{{ $role->name }}</div>
                                            <div class="small text-muted mt-1"><i class="fas fa-fingerprint me-1"></i> ID: {{ str_pad($role->id, 3, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center">
                                        @if(strtolower($role->name) === 'administrador')
                                            <span class="chip chip-accent bg-purple-100 text-purple-700 border-purple-200">
                                                <i class="fas fa-star text-warning"></i> Acceso Total al Sistema
                                            </span>
                                        @else
                                            @forelse($role->permissions->take(6) as $permission)
                                                <span class="chip" title="{{ $permission->name }}">
                                                    {{ Str::headline(str_replace('_', ' ', $permission->name)) }}
                                                </span>
                                            @empty
                                                <span class="text-danger small bg-danger bg-opacity-10 px-2 py-1 rounded border border-danger border-opacity-25"><i class="fas fa-exclamation-triangle me-1"></i> Sin accesos</span>
                                            @endforelse

                                            @if($role->permissions_count > 6)
                                                <span class="chip bg-secondary text-white border-secondary">
                                                    +{{ $role->permissions_count - 6 }} más
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark fs-5">{{ $role->permissions_count }}</div>
                                    <div class="small text-muted text-uppercase tracking-wider" style="font-size: 0.65rem;">Permisos</div>
                                </td>
                                @can('gestionar_roles_permisos')
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm table-actions" role="group">
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-light text-primary border" data-bs-toggle="tooltip" title="Modificar Perfil">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(strtolower($role->name) !== 'administrador')
                                                <button type="button" class="btn btn-light text-danger border" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $role->id }}" title="Eliminar Perfil">
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
                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-4" style="width: 100px; height: 100px;">
                                            <i class="fas fa-shield-alt text-secondary fs-1 opacity-50"></i>
                                        </div>
                                        <h4 class="fw-bold text-dark mb-2">No hay roles configurados</h4>
                                        <p class="text-muted mb-0">Comienza creando un rol organizativo para asegurar tu sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-light">
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

@foreach($roles as $role)
    @if(strtolower($role->name) !== 'administrador')
        <div class="modal fade" id="confirmModal-{{ $role->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body text-center p-5">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-trash-alt fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">¿Eliminar Perfil?</h4>
                        <p class="text-muted mb-4">El rol <strong class="text-dark text-capitalize">{{ $role->name }}</strong> será borrado permanentemente y los usuarios asociados perderán sus accesos.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-3" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('roles.destroy', $role) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger fw-bold px-4 rounded-3">Sí, Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush