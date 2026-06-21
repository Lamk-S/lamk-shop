<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark bg-dark border-end border-secondary" id="sidenavAccordion">
        <div class="sb-sidenav-menu custom-scrollbar">
            <div class="nav pt-3">
                
                <div class="sb-sidenav-menu-heading">Gestión Principal</div>
                <a class="nav-link {{ request()->routeIs('panel') ? 'active text-info' : '' }}" href="{{ route('panel') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-pie"></i></div>
                    Panel de Control
                </a>

                @canany(['registrar_compras', 'registrar_ventas'])
                    <div class="sb-sidenav-menu-heading">Operaciones Comerciales</div>
                @endcanany

                @can('registrar_ventas')
                    <a class="nav-link {{ request()->routeIs('ventas.*') ? 'active text-info' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVentas" aria-expanded="{{ request()->routeIs('ventas.*') ? 'true' : 'false' }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                        Punto de Venta
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ request()->routeIs('ventas.*') ? 'show' : '' }}" id="collapseVentas" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ request()->routeIs('ventas.create') ? 'active' : '' }}" href="{{ route('ventas.create') }}">Nueva Venta</a>
                            <a class="nav-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}" href="{{ route('ventas.index') }}">Historial de Ventas</a>
                        </nav>
                    </div>
                @endcan

                @can('registrar_compras')
                    <a class="nav-link {{ request()->routeIs('compras.*') ? 'active text-info' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCompras" aria-expanded="{{ request()->routeIs('compras.*') ? 'true' : 'false' }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-truck-ramp-box"></i></div>
                        Abastecimiento
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ request()->routeIs('compras.*') ? 'show' : '' }}" id="collapseCompras" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ request()->routeIs('compras.create') ? 'active' : '' }}" href="{{ route('compras.create') }}">Registrar Compra</a>
                            <a class="nav-link {{ request()->routeIs('compras.index') ? 'active' : '' }}" href="{{ route('compras.index') }}">Historial de Compras</a>
                        </nav>
                    </div>
                @endcan

                @canany(['gestionar_cajas', 'abrir_caja', 'cerrar_caja', 'movimientos_caja', 'gestionar_tesoreria'])
                    <div class="sb-sidenav-menu-heading">Finanzas</div>
                @endcanany

                @can('gestionar_cajas')
                    <a class="nav-link {{ request()->routeIs('cajas.*') ? 'active text-info' : '' }}" href="{{ route('cajas.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-cash-register"></i></div> Terminales / Cajas
                    </a>
                @endcan

                @can('abrir_caja')
                    <a class="nav-link {{ request()->routeIs('sesiones-caja.*') ? 'active text-info' : '' }}" href="{{ route('sesiones-caja.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-clock-rotate-left"></i></div> Apertura / Cierre
                    </a>
                @endcan

                @can('movimientos_caja')
                    <a class="nav-link {{ request()->routeIs('movimientos-caja.*') ? 'active text-info' : '' }}" href="{{ route('movimientos-caja.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill-transfer"></i></div> Movimientos Caja
                    </a>
                @endcan

                @can('gestionar_tesoreria')
                    <a class="nav-link {{ request()->routeIs('tesorerias.*') ? 'active text-info' : '' }}" href="{{ route('tesorerias.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-building-columns"></i></div> Bóveda / Tesorería
                    </a>
                @endcan

                @canany(['ver_kardex', 'gestionar_productos', 'gestionar_categorias', 'gestionar_marcas', 'gestionar_tallas'])
                    <div class="sb-sidenav-menu-heading">Inventario Deportivo</div>
                @endcanany

                @can('ver_kardex')
                    <a class="nav-link {{ request()->routeIs('kardex.*') ? 'active text-info' : '' }}" href="{{ route('kardex.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></div> Kardex
                    </a>
                @endcan

                @can('gestionar_productos')
                    <a class="nav-link {{ request()->routeIs('productos.*') ? 'active text-info' : '' }}" href="{{ route('productos.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-shirt"></i></div> Catálogo de Ropa
                    </a>
                    <a class="nav-link {{ request()->routeIs('producto-variantes.*') ? 'active text-info' : '' }}" href="{{ route('producto-variantes.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-shoe-prints"></i></div> Calzado y Variantes
                    </a>
                @endcan

                @can('gestionar_categorias')
                    <a class="nav-link {{ request()->routeIs('categorias.*') ? 'active text-info' : '' }}" href="{{ route('categorias.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-tags"></i></div> Categorías
                    </a>
                @endcan

                @can('gestionar_marcas')
                    <a class="nav-link {{ request()->routeIs('marcas.*') ? 'active text-info' : '' }}" href="{{ route('marcas.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-copyright"></i></div> Marcas
                    </a>
                @endcan

                @can('gestionar_tallas')
                    <a class="nav-link {{ request()->routeIs('tallas.*') ? 'active text-info' : '' }}" href="{{ route('tallas.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-ruler-horizontal"></i></div> Guía de Tallas
                    </a>
                @endcan

                @canany(['gestionar_clientes', 'gestionar_proveedores'])
                    <div class="sb-sidenav-menu-heading">Contactos</div>
                @endcanany

                @can('gestionar_clientes')
                    <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active text-info' : '' }}" href="{{ route('clientes.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div> Clientes
                    </a>
                @endcan

                @can('gestionar_proveedores')
                    <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active text-info' : '' }}" href="{{ route('proveedores.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-handshake"></i></div> Proveedores
                    </a>
                @endcan

                @canany(['gestionar_configuracion', 'ver_auditoria', 'gestionar_usuarios', 'gestionar_roles_permisos'])
                    <div class="sb-sidenav-menu-heading">Sistema</div>
                @endcanany

                @can('gestionar_comprobantes')
                    <a class="nav-link {{ request()->routeIs('comprobantes.*') ? 'active text-info' : '' }}" href="{{ route('comprobantes.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-file-invoice"></i></div> Tipo Comprobantes
                    </a>
                @endcan

                @can('gestionar_usuarios')
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active text-info' : '' }}" href="{{ route('users.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-user-shield"></i></div> Usuarios
                    </a>
                @endcan

                @can('gestionar_roles_permisos')
                    <a class="nav-link {{ request()->routeIs('roles.*') ? 'active text-info' : '' }}" href="{{ route('roles.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-key"></i></div> Roles y Permisos
                    </a>
                @endcan
                
                @can('ver_auditoria')
                    <a class="nav-link {{ request()->routeIs('auditoria-operaciones.*') ? 'active text-info' : '' }}" href="{{ route('auditoria-operaciones.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-list-check"></i></div> Auditoría
                    </a>
                @endcan

                @can('gestionar_configuracion')
                    <a class="nav-link {{ request()->routeIs('empresa-configuracion.*') ? 'active text-info' : '' }}" href="{{ route('empresa-configuracion.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-gear"></i></div> Configuración Tienda
                    </a>
                @endcan

            </div>
        </div>

        <div class="sb-sidenav-footer border-top border-secondary bg-dark pb-3">
            <div class="small text-muted mb-1 text-uppercase tracking-wide" style="font-size: 0.65rem;">Estado de Terminal</div>
            
            @php
                $sesionAbierta = \App\Models\SesionCaja::with('caja')
                    ->where('user_id', auth()->id())
                    ->whereNull('saldo_final_declarado')
                    ->first();
            @endphp

            @if($sesionAbierta && $sesionAbierta->caja)
                <div class="d-flex align-items-center gap-2">
                    <span class="position-relative d-flex" style="width: 10px; height: 10px;">
                        <span class="animate-ping position-absolute h-100 w-100 rounded-circle bg-success opacity-75"></span>
                        <span class="position-relative rounded-circle w-100 h-100 bg-success"></span>
                    </span>
                    <div class="fw-bold text-white small text-truncate" title="{{ $sesionAbierta->caja->nombre }}">
                        {{ $sesionAbierta->caja->nombre }}
                    </div>
                </div>
                <div class="text-info mt-1" style="font-size: 0.65rem;">
                    <i class="fas fa-clock me-1"></i> Operando desde {{ $sesionAbierta->created_at->format('H:i') }}
                </div>
            @else
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-danger rounded-circle" style="width: 10px; height: 10px;"></div>
                    <div class="fw-bold text-white small opacity-75">Terminal Cerrada</div>
                </div>
                
                @can('abrir_caja')
                    <a href="{{ route('sesiones-caja.create') }}" class="text-decoration-none text-info mt-2 d-inline-block fw-medium" style="font-size: 0.75rem;">
                        <i class="fas fa-cash-register me-1"></i> Iniciar Turno de Caja
                    </a>
                @endcan
            @endif
        </div>
    </nav>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.2); }
    .sb-sidenav-menu-heading { font-size: 0.70rem !important; letter-spacing: 0.08em; text-transform: uppercase; color: #6c757d !important; font-weight: 700; margin-top: 1rem; }
    .sb-sidenav .nav-link { transition: all 0.2s ease; font-weight: 500; font-size: 0.9rem; padding-top: 0.6rem; padding-bottom: 0.6rem; }
    .sb-sidenav .nav-link:hover { color: #fff !important; background-color: rgba(255,255,255,0.05); }
    .sb-sidenav .nav-link.active { font-weight: 700; background-color: rgba(13, 202, 240, 0.1); border-right: 3px solid #0dcaf0; }
    .sb-sidenav .nav-link .sb-nav-link-icon { font-size: 1.1rem; width: 1.5rem; text-align: center; }
    @keyframes ping {
        0% { transform: scale(1); opacity: 1; }
        75%, 100% { transform: scale(2.5); opacity: 0; }
    }
    .animate-ping { animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; }
</style>