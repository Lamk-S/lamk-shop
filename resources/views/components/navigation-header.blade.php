<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark border-bottom border-secondary shadow-sm px-2 px-md-3">
    
    <!-- Lado Izquierdo: Marca y Toggle -->
    <div class="d-flex align-items-center">
        <a class="navbar-brand fw-bold d-flex align-items-center ps-2 pe-0 pe-md-4" href="{{ route('panel') }}" style="width: auto;">
            <img src="{{ asset('assets/img/Logo.png') }}" alt="Lamk's POS Logo" class="me-2" style="height: 30px; object-fit: contain;">
            <!-- Texto oculto en móviles (xs), visible desde sm -->
            <span class="text-white text-uppercase d-none d-sm-inline" style="letter-spacing: 1px;">Lamk</span> 
            <span class="text-info ms-1 text-uppercase d-none d-sm-inline" style="letter-spacing: 1px;">Sports</span>
        </a>
        
        <button class="btn btn-link btn-sm text-white-50 ms-1 ms-md-0" id="sidebarToggle" type="button" title="Contraer/Expandir menú">
            <i class="fas fa-bars fs-5"></i>
        </button>
    </div>

    <!-- Buscador (Oculto en móviles y tablets pequeñas, visible en lg) -->
    <form action="{{ route('productos.index') }}" method="GET" class="d-none d-lg-flex align-items-center ms-4 me-2 flex-grow-1" style="max-width: 450px;">
        <div class="input-group">
            <button class="btn bg-dark border-secondary text-muted pe-3 border-end-0" type="submit" title="Buscar">
                <i class="fas fa-search"></i>
            </button>
            <input class="form-control bg-dark text-white border-secondary border-start-0 focus-ring focus-ring-info ps-1"
                   type="search" name="q" value="{{ request('q') }}" placeholder="Buscar zapatillas, ropa, marcas..." aria-label="Buscar" />
        </div>
    </form>

    <!-- Lado Derecho: Acciones y Usuario -->
    <div class="ms-auto d-flex align-items-center gap-2 gap-md-3 pe-1 pe-md-2">
        
        @can('registrar_ventas')
            <!-- Botón Escritorio -->
            <a href="{{ route('ventas.create') }}" class="btn btn-info btn-sm fw-bold d-none d-md-inline-flex align-items-center shadow-sm rounded-pill px-3">
                <i class="fas fa-cart-plus me-2"></i> Nueva Venta
            </a>
            <!-- Botón Móvil (Solo ícono) -->
            <a href="{{ route('ventas.create') }}" class="btn btn-info btn-sm d-inline-flex d-md-none align-items-center justify-content-center rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0;">
                <i class="fas fa-cart-plus"></i>
            </a>
        @endcan

        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2 pe-0" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0dcaf0&color=000&bold=true" 
                         alt="Avatar" class="rounded-circle border border-secondary shadow-sm" style="width: 34px; height: 34px;">
                    <div class="d-none d-lg-block text-start lh-1">
                        <div class="small fw-bold">{{ Str::words(auth()->user()->name, 2, '') }}</div>
                        <div class="text-info" style="font-size: 0.65rem; text-transform: uppercase;">
                            {{ auth()->user()->roles->first()?->name ?? 'Operador' }}
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg border-secondary mt-2 rounded-3 position-absolute" aria-labelledby="navbarDropdown">
                    <li class="px-3 py-2 d-lg-none border-bottom border-secondary mb-2">
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                        <div class="text-info small">{{ auth()->user()->roles->first()?->name ?? 'Operador' }}</div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('profile.index') }}">
                            <i class="fa-solid fa-user-gear text-info me-3 text-center" style="width: 20px;"></i> Mi Perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider border-secondary my-1" /></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger fw-medium d-flex align-items-center">
                                <i class="fa-solid fa-power-off me-3 text-center" style="width: 20px;"></i> Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>