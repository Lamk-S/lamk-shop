@extends('layouts.app')
@section('title', 'Directorio de Usuarios')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .main-card { border: 0; border-radius: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); overflow: hidden; background: #fff; }
    .card-gradient-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #edf2f7; }
    .table-custom th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: .75rem; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; }
    .table-custom td { vertical-align: middle; color: #334155; border-bottom: 1px solid #f1f5f9; }
    .user-avatar-wrap { position: relative; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); flex-shrink: 0; }
    .status-indicator { position: absolute; bottom: 0; right: 0; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff; }
    .status-active { background-color: #10b981; }
    .status-inactive { background-color: #ef4444; }
    .role-badge { display: inline-flex; align-items: center; gap: .35rem; padding: .3rem .75rem; border-radius: 8px; font-size: .75rem; font-weight: 600; border: 1px solid transparent; }
    .role-admin { background: #f3e8ff; color: #9333ea; border-color: #e9d5ff; }
    .role-cajero { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
    .role-vendedor { background: #dcfce7; color: #16a34a; border-color: #bbf7d0; }
    .role-default { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
    .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 8px; transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-2px); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Directorio de Usuarios</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Personal y Accesos</li>
            </ol>
        </div>

        @can('gestionar_usuarios')
            <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-bold">
                <i class="fas fa-user-plus me-2"></i>Registrar Empleado
            </a>
        @endcan
    </div>

    <div class="card main-card">
        <div class="card-header card-gradient-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-users fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Gestión de Cuentas</h5>
                    <div class="text-muted small mt-1">Controla quién tiene acceso a la plataforma y sus niveles de autorización.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="p-4 bg-white border-bottom">
                <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label for="q" class="form-label text-muted small fw-bold text-uppercase tracking-wider">Búsqueda Rápida</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="search" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Nombre o correo...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="role" class="form-label text-muted small fw-bold text-uppercase tracking-wider">Rol Asignado</label>
                        <select name="role" id="role" class="form-select shadow-sm" onchange="this.form.submit()">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ Str::headline($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="estado" class="form-label text-muted small fw-bold text-uppercase tracking-wider">Estado</label>
                        <select name="estado" id="estado" class="form-select shadow-sm" onchange="this.form.submit()">
                            <option value="">Cualquiera</option>
                            <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                            <option value="inactivo" @selected(request('estado') === 'inactivo')>Suspendidos</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex gap-2">
                        <div class="w-50">
                            <label for="per_page" class="form-label text-muted small fw-bold text-uppercase tracking-wider">Mostrar</label>
                            <select name="per_page" id="per_page" class="form-select shadow-sm" onchange="this.form.submit()">
                                @foreach([10, 15, 25, 50] as $size)
                                    <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-50 d-flex align-items-end">
                            <a href="{{ route('users.index') }}" class="btn btn-light fw-medium border w-100 shadow-sm">Limpiar</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Perfil del Usuario</th>
                            <th>Contacto Corporativo</th>
                            <th>Rol del Sistema</th>
                            <th class="text-center">Estado</th>
                            @can('gestionar_usuarios')
                                <th class="text-center pe-4">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $item)
                            @php
                                $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false;
                                $isActive = !$estaEliminado && (int) $item->estado === 1;
                                $rolPrincipal = strtolower($item->roles->first()?->name ?? 'sin rol');
                                
                                $badgeClass = match(true) {
                                    str_contains($rolPrincipal, 'admin') => 'role-admin',
                                    str_contains($rolPrincipal, 'cajero') => 'role-cajero',
                                    str_contains($rolPrincipal, 'vendedor') => 'role-vendedor',
                                    default => 'role-default'
                                };
                                
                                // Generador de avatar iniciales por UI Avatars
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($item->name) . "&background=random&color=fff&bold=true";
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar-wrap">
                                            <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover">
                                            <span class="status-indicator {{ $isActive ? 'status-active' : 'status-inactive' }}" title="{{ $isActive ? 'Conectado/Activo' : 'Suspendido' }}"></span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                            <div class="small text-muted"><i class="fas fa-id-card me-1"></i> ID: {{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $item->email }}" class="text-decoration-none text-primary fw-medium">
                                        <i class="far fa-envelope text-muted me-1"></i> {{ $item->email }}
                                    </a>
                                </td>
                                <td>
                                    <span class="role-badge {{ $badgeClass }}">
                                        <i class="fas {{ str_contains($rolPrincipal, 'admin') ? 'fa-crown text-warning' : 'fa-user-tag' }}"></i> 
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
                                        <div class="btn-group shadow-sm table-actions" role="group">
                                            <a href="{{ route('users.edit', $item) }}" class="btn btn-action btn-light text-primary border" title="Editar Credenciales">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(Auth::id() !== $item->id)
                                                <button type="button" 
                                                        class="btn btn-action btn-light {{ $isActive ? 'text-danger' : 'text-success' }} border" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#confirmModal-{{ $item->id }}" 
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
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-4" style="width: 90px; height: 90px;">
                                            <i class="fas fa-user-times text-secondary fs-1 opacity-50"></i>
                                        </div>
                                        <h4 class="fw-bold text-dark mb-2">No se encontraron resultados</h4>
                                        <p class="text-muted mb-0">No hay usuarios que coincidan con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-light">
                <div class="text-muted small fw-medium">
                    Mostrando <span class="fw-bold text-dark">{{ $users->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $users->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $users->total() }}</span> registros
                </div>
                <div>
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($users as $item)
    @if(Auth::id() !== $item->id)
        @php $estaEliminado = method_exists($item, 'trashed') ? $item->trashed() : false; @endphp
        <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            @if($estaEliminado || !(int) $item->estado)
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            @else
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user-lock fa-2x"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="fw-bold text-dark mb-2">{{ $estaEliminado || !(int) $item->estado ? '¿Reactivar acceso?' : '¿Suspender usuario?' }}</h4>
                        <p class="text-muted mb-4">
                            @if($estaEliminado || !(int) $item->estado)
                                El usuario <strong>{{ $item->name }}</strong> recuperará acceso al sistema con sus permisos previos.
                            @else
                                El usuario <strong>{{ $item->name }}</strong> perderá el acceso inmediatamente. Sus operaciones pasadas se conservarán.
                            @endif
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-3" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('users.destroy', $item) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn {{ $estaEliminado || !(int) $item->estado ? 'btn-success' : 'btn-danger' }} fw-bold px-4 rounded-3">
                                    Confirmar Acción
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection