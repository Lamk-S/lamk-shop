<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark bg-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Inicio</div>
                <a class="nav-link" href="{{ route('panel') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Panel de Control
                </a>
                
                <div class="sb-sidenav-menu-heading">Módulos Operativos</div>
                
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

                <div class="sb-sidenav-menu-heading">Catálogos</div>

                @can('ver-categoria')
                <a class="nav-link" href="{{ route('categorias.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-tags"></i></div> Categorías
                </a>
                @endcan

                @can('ver-presentacione')
                <a class="nav-link" href="{{ route('presentaciones.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-box-open"></i></div> Presentaciones
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

                <div class="sb-sidenav-menu-heading">Directorio</div>

                @can('ver-cliente')
                <a class="nav-link" href="{{ route('clientes.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div> Clientes
                </a>
                @endcan

                @can('ver-proveedore')
                <a class="nav-link" href="{{ route('proveedores.index') }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-truck-field"></i></div> Proveedores
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