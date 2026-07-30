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
                    <a class="nav-link {{ request()->routeIs('ventas.*') || request()->routeIs('pagos-venta.*') ? 'active text-info' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVentas" aria-expanded="{{ request()->routeIs('ventas.*') ? 'true' : 'false' }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                        Punto de Venta
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ request()->routeIs('ventas.*') || request()->routeIs('pagos-venta.*') ? 'show' : '' }}" id="collapseVentas" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ request()->routeIs('ventas.create') ? 'active' : '' }}" href="{{ route('ventas.create') }}">Nueva Venta</a>
                            <a class="nav-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}" href="{{ route('ventas.index') }}">Historial de Ventas</a>
                            @can('gestionar_tesoreria')
                                <a class="nav-link {{ request()->routeIs('pagos-venta.index') ? 'active' : '' }}" href="{{ route('pagos-venta.index') }}">Ingresos (Pagos)</a>
                            @endcan
                        </nav>
                    </div>
                @endcan

                @can('registrar_compras')
                    <a class="nav-link {{ request()->routeIs('compras.*') || request()->routeIs('cuentas-por-pagar.*') ? 'active text-info' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCompras" aria-expanded="{{ request()->routeIs('compras.*') ? 'true' : 'false' }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-truck-ramp-box"></i></div>
                        Abastecimiento
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ request()->routeIs('compras.*') || request()->routeIs('cuentas-por-pagar.*') ? 'show' : '' }}" id="collapseCompras" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ request()->routeIs('compras.create') ? 'active' : '' }}" href="{{ route('compras.create') }}">Registrar Compra</a>
                            <a class="nav-link {{ request()->routeIs('compras.index') ? 'active' : '' }}" href="{{ route('compras.index') }}">Historial de Compras</a>
                            @can('gestionar_tesoreria')
                                <a class="nav-link {{ request()->routeIs('cuentas-por-pagar.index') ? 'active' : '' }}" href="{{ route('cuentas-por-pagar.index') }}">Cuentas por Pagar</a>
                            @endcan
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

                @canany(['gestionar_productos', 'ver_productos'])
                    <a class="nav-link {{ request()->routeIs('productos.*') ? 'active text-info' : '' }}" href="{{ route('productos.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-shirt"></i></div> Catálogo de Ropa
                    </a>
                    <a class="nav-link {{ request()->routeIs('producto-variantes.*') ? 'active text-info' : '' }}" href="{{ route('producto-variantes.index') }}">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-shoe-prints"></i></div> Calzado y Variantes
                    </a>
                @endcanany

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
            <div class="small text-muted mb-1 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">Estado de Terminal</div>

            @if(isset($sesionAbierta) && $sesionAbierta->caja)
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