<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark bg-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Inicio</div>
                <a class="nav-link" href="{{ route('panel') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Panel de Control
                </a>

                @canany(['ver-venta', 'ver-compra'])
                <div class="sb-sidenav-menu-heading">Operaciones</div>
                @endcanany

                @can('ver-compra')
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCompras" aria-expanded="false">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-store"></i></div>
                    Compras
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseCompras" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('compras.index') }}">Gestión de Compras</a>
                        @can('crear-compra')
                        <a class="nav-link" href="{{ route('compras.create') }}">Nueva Compra</a>
                        @endcan
                    </nav>
                </div>
                @endcan

                @can('ver-venta')
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVentas" aria-expanded="false">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    Ventas
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseVentas" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('ventas.index') }}">Historial de Ventas</a>
                        @can('crear-venta')
                        <a class="nav-link" href="{{ route('ventas.create') }}">Nueva Venta</a>
                        @endcan
                    </nav>
                </div>
                @endcan

                @canany(['ver-caja', 'ver-sesion-caja', 'ver-movimiento-caja', 'ver-tesoreria', 'ver-movimiento-tesoreria'])
                <div class="sb-sidenav-menu-heading">Caja y Tesorería</div>
                @endcanany

                @can('ver-caja')
                <a class="nav-link" href="{{ route('cajas.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-cash-register"></i></div> Cajas
                </a>
                @endcan

                @can('ver-sesion-caja')
                <a class="nav-link" href="{{ route('sesiones-caja.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-lock-open"></i></div> Sesiones de Caja
                </a>
                @endcan

                @can('ver-movimiento-caja')
                <a class="nav-link" href="{{ route('movimientos-caja.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill-wave"></i></div> Movimientos de Caja
                </a>
                @endcan

                @can('ver-tesoreria')
                <a class="nav-link" href="{{ route('tesoreria.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-vault"></i></div> Tesorería
                </a>
                @endcan

                @can('ver-movimiento-tesoreria')
                <a class="nav-link" href="{{ route('movimientos-tesoreria.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-right-left"></i></div> Movimientos Tesorería
                </a>
                @endcan

                @can('ver-kardex')
                <a class="nav-link" href="{{ route('kardex.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-box-open"></i></div> Kardex
                </a>
                @endcan

                @canany(['ver-categoria', 'ver-presentacion', 'ver-marca', 'ver-producto'])
                <div class="sb-sidenav-menu-heading">Catálogos</div>
                @endcanany

                @can('ver-categoria')
                <a class="nav-link" href="{{ route('categorias.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-tags"></i></div> Categorías
                </a>
                @endcan

                @can('ver-presentacion')
                <a class="nav-link" href="{{ route('presentaciones.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-box"></i></div> Presentaciones
                </a>
                @endcan

                @can('ver-marca')
                <a class="nav-link" href="{{ route('marcas.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-award"></i></div> Marcas
                </a>
                @endcan

                @can('ver-producto')
                <a class="nav-link" href="{{ route('productos.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></div> Productos
                </a>
                @endcan

                @canany(['ver-cliente', 'ver-proveedor'])
                <div class="sb-sidenav-menu-heading">Directorio</div>
                @endcanany

                @can('ver-cliente')
                <a class="nav-link" href="{{ route('clientes.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div> Clientes
                </a>
                @endcan

                @can('ver-proveedor')
                <a class="nav-link" href="{{ route('proveedores.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-truck-moving"></i></div> Proveedores
                </a>
                @endcan

                @canany(['ver-user', 'ver-role'])
                <div class="sb-sidenav-menu-heading">Administración</div>
                @endcanany

                @can('ver-user')
                <a class="nav-link" href="{{ route('users.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-user-shield"></i></div> Usuarios
                </a>
                @endcan

                @can('ver-role')
                <a class="nav-link" href="{{ route('roles.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-key"></i></div> Roles y Permisos
                </a>
                @endcan
            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small text-muted mb-1">Sesión iniciada como:</div>
            <div class="fw-semibold text-white">{{ auth()->user()->name }}</div>
        </div>
    </nav>
</div>