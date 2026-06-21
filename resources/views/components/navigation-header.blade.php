<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark border-bottom border-secondary shadow-sm" style="height: 65px;">
    <a class="navbar-brand ps-3 fw-bold d-flex align-items-center" href="{{ route('panel') }}" style="width: 225px;">
        <img src="{{ asset('assets/img/Logo.png') }}" alt="Lamk's POS Logo" class="me-2" style="height: 32px; object-fit: contain;">
        <span class="text-white tracking-wide">Lamk</span> <span class="text-info ms-1">Sports</span>
    </a>

    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-white opacity-75 hover-info transition-hover rounded-circle ms-2"
            id="sidebarToggle"
            type="button" title="Contraer/Expandir menú">
        <i class="fas fa-bars fs-5"></i>
    </button>

    <form action="{{ route('productos.index') }}" method="GET" class="d-none d-md-flex align-items-center form-inline ms-4 me-0 me-md-3 my-2 my-md-0 w-100" style="max-width: 450px;">
        <div class="input-group search-box">
            <button class="btn bg-dark border-secondary text-muted pe-2 border-end-0 hover-info transition-hover" type="submit" title="Buscar">
                <i class="fas fa-search"></i>
            </button>
            <input class="form-control bg-dark text-white border-secondary border-start-0 focus-ring-info ps-1"
                   type="search"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Buscar zapatillas, ropa deportiva, marcas..."
                   aria-label="Buscar" />
        </div>
    </form>

    <div class="ms-auto d-flex align-items-center pe-3">
        @can('registrar_ventas')
            <a href="{{ route('ventas.create') }}" class="btn btn-info btn-sm fw-bold me-3 d-none d-sm-inline-flex align-items-center shadow-sm">
                <i class="fas fa-cart-plus me-2"></i> Nueva Venta
            </a>
        @endcan

        <ul class="navbar-nav ms-auto ms-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white opacity-100 hover-info d-flex align-items-center gap-2"
                   id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0dcaf0&color=000&bold=true" 
                         alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px;">
                    <div class="d-none d-lg-block text-start lh-1">
                        <div class="small fw-bold">{{ Str::words(auth()->user()->name, 2, '') }}</div>
                        <div class="text-info" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ auth()->user()->roles->first()?->name ?? 'Operador' }}
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg border-secondary mt-2 rounded-3" aria-labelledby="navbarDropdown">
                    <li class="px-3 py-2 d-lg-none border-bottom border-secondary mb-2">
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                        <div class="text-info small">{{ auth()->user()->roles->first()?->name ?? 'Operador' }}</div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('profile.index') }}">
                            <i class="fa-solid fa-user-gear text-info me-3 w-20px text-center"></i> Mi Perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider border-secondary my-1" /></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger fw-medium hover-danger border-0 bg-transparent d-flex align-items-center">
                                <i class="fa-solid fa-power-off me-3 w-20px text-center"></i> Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<style>
    .w-20px { width: 20px; }
    .tracking-wide { letter-spacing: 0.05em; }
    .transition-hover { transition: all 0.2s ease-in-out; }
    .hover-info:hover { opacity: 1 !important; color: #0dcaf0 !important; }
    .hover-danger:hover { background-color: rgba(220, 53, 69, 0.1) !important; color: #ff6b6b !important; }
    .search-box .form-control:focus, .search-box .btn { border-color: #0dcaf0; background-color: #2b3035 !important; }
    .search-box .form-control:focus { box-shadow: none; }
    .search-box:focus-within { box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.15); border-radius: 0.375rem; }
    .focus-ring-info::placeholder { color: #6c757d; }
</style>