@extends('layouts.app')
@section('title', 'Nuevo Rol')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Nuevo Rol Organizativo</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}" class="text-decoration-none text-muted">Roles</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Creación</li>
            </ol>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-light border shadow-sm fw-medium rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 1200px;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-user-shield fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Definición de Matriz de Accesos</h5>
                    <div class="text-muted small mt-1">Habilita o deshabilita módulos funcionales para este nuevo puesto de trabajo.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5 bg-light bg-opacity-25">
            <form action="{{ route('roles.store') }}" method="post">
                @csrf

                <div class="card border border-secondary border-opacity-25 rounded-4 p-4 mb-4 bg-white shadow-sm">
                    <div class="row align-items-end g-3">
                        <div class="col-lg-6">
                            <label for="name" class="form-label text-secondary small fw-bold text-uppercase">Denominación del Rol <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg fs-6 fw-bold text-dark border-secondary-subtle @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej. Administrador de Ventas">
                            @error('name') <div class="text-danger mt-1 small fw-medium">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="perm_search" class="form-label text-secondary small fw-bold text-uppercase">Filtrar Módulos Rápidamente</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="search" id="perm_search" class="form-control border-start-0 bg-light" placeholder="Buscar permisos...">
                                <button type="button" class="btn btn-outline-primary fw-medium" id="select-all-global">Marcar Todo</button>
                                <button type="button" class="btn btn-outline-secondary fw-medium" id="clear-all-global">Limpiar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 d-flex align-items-center gap-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-cubes text-muted me-2"></i>Módulos del Sistema</h5>
                    <span class="badge bg-secondary rounded-pill shadow-sm" id="counter-badge">0 / 0 Activos</span>
                </div>

                @error('permission')
                    <div class="alert alert-danger border-0 shadow-sm rounded-3"><i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}</div>
                @enderror

                <div class="row g-4" id="modules-container">
                    @foreach($permissionGroups as $groupName => $permisos)
                        <div class="col-xl-4 col-lg-6 module-wrapper" data-module="{{ strtolower($groupName) }}">
                            <div class="card h-100 border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden bg-white">
                                <div class="card-header bg-light bg-opacity-75 d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                                        @php
                                            $icon = match(strtolower($groupName)) {
                                                'dashboard' => 'fa-chart-line', 'usuarios' => 'fa-users', 'roles permisos' => 'fa-key',
                                                'configuracion' => 'fa-cogs', 'clientes', 'proveedores' => 'fa-address-book',
                                                'productos', 'categorias', 'marcas', 'tallas' => 'fa-box',
                                                'caja', 'cajas' => 'fa-cash-register', 'tesoreria' => 'fa-wallet',
                                                'compras' => 'fa-shopping-cart', 'ventas' => 'fa-store',
                                                'kardex' => 'fa-exchange-alt', 'auditoria' => 'fa-clipboard-list',
                                                'comprobantes' => 'fa-file-invoice', default => 'fa-layer-group'
                                            };
                                        @endphp
                                        <i class="fas {{ $icon }} text-primary opacity-75 me-1"></i> {{ $groupName }}
                                    </h6>
                                    <div class="form-check form-switch m-0" title="Activar todo el módulo">
                                        <input class="form-check-input select-module-switch" type="checkbox" role="switch">
                                    </div>
                                </div>
                                <div class="list-group list-group-flush">
                                    @foreach($permisos as $permiso)
                                        <div class="list-group-item d-flex justify-content-between align-items-center p-3 permission-item" data-perm-name="{{ strtolower($permiso->name) }}">
                                            <label class="form-check-label small fw-bold text-secondary text-capitalize mb-0 w-100 cursor-pointer" for="perm-{{ $permiso->id }}">
                                                {{ Str::headline(str_replace('_', ' ', $permiso->name)) }}
                                            </label>
                                            <div class="form-check form-switch m-0 ms-2">
                                                <input class="form-check-input perm-checkbox" type="checkbox" role="switch" name="permission[]" value="{{ $permiso->id }}" id="perm-{{ $permiso->id }}" @checked(in_array($permiso->id, old('permission', [])))>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-top d-flex flex-column flex-sm-row justify-content-end align-items-center gap-3">
                    <a href="{{ route('roles.index') }}" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm">Cancelar Operación</a>
                    <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm">
                        <i class="fas fa-save me-2"></i>Registrar Rol
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
            counterBadge.className = `badge rounded-pill shadow-sm ${active > 0 ? 'bg-primary' : 'bg-secondary'}`;
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
                    
                    if (match) {
                        item.classList.remove('d-none');
                        item.classList.add('d-flex');
                        hasVisible = true;
                    } else {
                        item.classList.add('d-none');
                        item.classList.remove('d-flex');
                    }
                });
                
                mod.style.display = hasVisible ? '' : 'none';
            });
        });

        document.querySelectorAll('.select-module-switch').forEach(masterSwitch => {
            masterSwitch.addEventListener('change', (e) => {
                const moduleCard = e.target.closest('.card');
                moduleCard.querySelectorAll('.perm-checkbox').forEach(cb => {
                    if (cb.closest('.permission-item').style.display !== 'none' && !cb.closest('.permission-item').classList.contains('d-none')) { 
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
            allCheckboxes.forEach(cb => {
                if(!cb.closest('.permission-item').classList.contains('d-none')){
                    cb.checked = true;
                }
            });
            syncModuleSwitches();
        });

        document.getElementById('clear-all-global')?.addEventListener('click', () => {
            allCheckboxes.forEach(cb => {
                if(!cb.closest('.permission-item').classList.contains('d-none')){
                    cb.checked = false;
                }
            });
            syncModuleSwitches();
        });

        syncModuleSwitches();
    });
</script>
@endpush