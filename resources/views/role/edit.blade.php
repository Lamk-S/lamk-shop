@extends('layouts.app')
@section('title', isset($role) ? 'Editar Rol' : 'Nuevo Rol')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #edf2f7; }
    .module-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; height: 100%; transition: all 0.2s ease; overflow: hidden; }
    .module-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .module-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; }
    .module-title { font-weight: 700; color: #334155; display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .perm-switch-container { padding: 0.75rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s; }
    .perm-switch-container:last-child { border-bottom: none; }
    .perm-switch-container:hover { background: #f8fafc; }
    .perm-label-text { font-weight: 600; color: #475569; font-size: 0.85rem; margin: 0; cursor: pointer; text-transform: capitalize; }
    .form-switch .form-check-input { width: 2.5em; height: 1.25em; margin-top: 0; cursor: pointer; }
    .form-switch .form-check-input:checked { background-color: #10b981; border-color: #10b981; }
    .search-panel { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">{{ isset($role) ? 'Configurar Perfil' : 'Nuevo Rol Organizativo' }}</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}" class="text-decoration-none text-muted">Roles</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">{{ isset($role) ? $role->name : 'Creación' }}</li>
            </ol>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-light border shadow-sm fw-medium">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card soft-card mx-auto" style="max-width: 1200px;">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-user-shield fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Definición de Matriz de Accesos</h5>
                    <div class="text-muted small mt-1">Habilita o deshabilita módulos funcionales para este puesto de trabajo.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}" method="post">
                @csrf
                @if(isset($role)) @method('PATCH') @endif

                <div class="search-panel mb-5">
                    <div class="row align-items-end g-3">
                        <div class="col-lg-6">
                            <label for="name" class="form-label text-muted small fw-bold text-uppercase tracking-wider">
                                Denominación del Rol <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg fs-6 fw-bold text-dark border-secondary-subtle @error('name') is-invalid @enderror" value="{{ old('name', $role->name ?? '') }}" placeholder="Ej. Administrador de Ventas" {{ (isset($role) && strtolower($role->name) === 'administrador') ? 'readonly' : '' }}>
                            @error('name') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="perm_search" class="form-label text-muted small fw-bold text-uppercase tracking-wider">
                                Filtrar Módulos
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="search" id="perm_search" class="form-control border-start-0 bg-light" placeholder="Escribe para buscar permisos rápidamente...">
                                <button type="button" class="btn btn-outline-primary fw-medium" id="select-all-global">Marcar Todo</button>
                                <button type="button" class="btn btn-outline-secondary fw-medium" id="clear-all-global">Limpiar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 d-flex align-items-center gap-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-cubes text-muted me-2"></i>Módulos del Sistema</h5>
                    <span class="badge bg-secondary rounded-pill" id="counter-badge">0 / 0 Activos</span>
                </div>

                @error('permission')
                    <div class="alert alert-danger border-0 shadow-sm rounded-3"><i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}</div>
                @enderror

                <div class="row g-4" id="modules-container">
                    @foreach($permissionGroups as $groupName => $permisos)
                        <div class="col-xl-4 col-lg-6 module-wrapper" data-module="{{ strtolower($groupName) }}">
                            <div class="module-card">
                                <div class="module-header">
                                    <h6 class="module-title">
                                        @php
                                            $icon = match(strtolower($groupName)) {
                                                'dashboard' => 'fa-chart-line',
                                                'usuarios' => 'fa-users',
                                                'roles permisos' => 'fa-key',
                                                'configuracion' => 'fa-cogs',
                                                'clientes', 'proveedores' => 'fa-address-book',
                                                'productos', 'categorias', 'marcas', 'tallas' => 'fa-box',
                                                'caja', 'cajas' => 'fa-cash-register',
                                                'tesoreria' => 'fa-wallet',
                                                'compras' => 'fa-shopping-cart',
                                                'ventas' => 'fa-store',
                                                'kardex' => 'fa-exchange-alt',
                                                'auditoria' => 'fa-clipboard-list',
                                                'comprobantes' => 'fa-file-invoice',
                                                default => 'fa-layer-group'
                                            };
                                        @endphp
                                        <i class="fas {{ $icon }} text-primary opacity-75"></i> {{ $groupName }}
                                    </h6>
                                    <div class="form-check form-switch m-0" title="Activar todo el módulo">
                                        <input class="form-check-input select-module-switch" type="checkbox" role="switch">
                                    </div>
                                </div>
                                <div class="module-body">
                                    @foreach($permisos as $permiso)
                                        @php
                                            $actionRaw = str_replace(['_','-'], ' ', $permiso->name);
                                            $actionRaw = str_replace(strtolower($groupName), '', $actionRaw);
                                        @endphp
                                        <div class="perm-switch-container permission-item" data-perm-name="{{ strtolower($permiso->name) }}">
                                            <label class="perm-label-text user-select-none" for="perm-{{ $permiso->id }}">
                                                {{ Str::headline(str_replace('_', ' ', $permiso->name)) }}
                                            </label>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input perm-checkbox" 
                                                       type="checkbox" 
                                                       role="switch" 
                                                       name="permission[]" 
                                                       value="{{ $permiso->id }}" 
                                                       id="perm-{{ $permiso->id }}"
                                                       @checked(in_array($permiso->id, old('permission', $selectedPermissions ?? [])))>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-top d-flex flex-column flex-sm-row justify-content-end align-items-center gap-3">
                    <a href="{{ route('roles.index') }}" class="btn btn-light fw-bold px-4 rounded-3 text-muted">Cancelar Operación</a>
                    <button type="submit" class="btn btn-primary fw-bold px-5 rounded-3 shadow-sm btn-lg fs-6">
                        <i class="fas fa-save me-2"></i>{{ isset($role) ? 'Guardar Cambios' : 'Registrar Rol' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('perm_search');
        const modules = Array.from(document.querySelectorAll('.module-wrapper'));
        const allCheckboxes = Array.from(document.querySelectorAll('.perm-checkbox'));
        const counterBadge = document.getElementById('counter-badge');

        function updateCounter() {
            const total = allCheckboxes.length;
            const active = allCheckboxes.filter(cb => cb.checked).length;
            counterBadge.textContent = `${active} / ${total} Activos`;
            counterBadge.className = `badge rounded-pill ${active > 0 ? 'bg-primary' : 'bg-secondary'}`;
        }

        function syncModuleSwitches() {
            modules.forEach(mod => {
                const cbs = Array.from(mod.querySelectorAll('.perm-checkbox'));
                const master = mod.querySelector('.select-module-switch');
                if(!master || cbs.length === 0) return;
                
                const allChecked = cbs.every(cb => cb.checked);
                const someChecked = cbs.some(cb => cb.checked);
                
                master.checked = allChecked;
                master.indeterminate = someChecked && !allChecked;
            });
            updateCounter();
        }

        searchInput?.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase().trim();
            modules.forEach(mod => {
                let hasVisible = false;
                const moduleName = mod.dataset.module;
                
                mod.querySelectorAll('.permission-item').forEach(item => {
                    const permName = item.dataset.permName;
                    const match = moduleName.includes(term) || permName.includes(term);
                    item.style.display = match ? '' : 'none';
                    if(match) hasVisible = true;
                });
                
                mod.style.display = hasVisible ? '' : 'none';
            });
        });

        document.querySelectorAll('.select-module-switch').forEach(masterSwitch => {
            masterSwitch.addEventListener('change', (e) => {
                const moduleCard = e.target.closest('.module-card');
                moduleCard.querySelectorAll('.perm-checkbox').forEach(cb => {
                    if (cb.offsetParent !== null) { 
                        cb.checked = e.target.checked;
                    }
                });
                updateCounter();
            });
        });

        allCheckboxes.forEach(cb => {
            cb.addEventListener('change', syncModuleSwitches);
        });

        document.getElementById('select-all-global')?.addEventListener('click', () => {
            allCheckboxes.forEach(cb => cb.checked = true);
            syncModuleSwitches();
        });

        document.getElementById('clear-all-global')?.addEventListener('click', () => {
            allCheckboxes.forEach(cb => cb.checked = false);
            syncModuleSwitches();
        });

        syncModuleSwitches();
    });
</script>
@endpush