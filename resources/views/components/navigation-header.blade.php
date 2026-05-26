<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark border-bottom border-secondary shadow-sm">
    <a class="navbar-brand ps-3 fw-bold d-flex align-items-center" href="{{ route('panel') }}">
        <img src="{{ asset('assets/img/Logo.png') }}" alt="Lamk's POS Logo" class="me-2" style="height: 32px;">
        <span class="text-white">Lamk's</span> <span class="text-info ms-1">POS</span>
    </a>

    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-white opacity-75 hover-info transition-hover" id="sidebarToggle" href="#!">
        <i class="fas fa-bars fs-5"></i>
    </button>

    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control bg-dark text-white border-secondary focus-ring-info px-3" type="text" placeholder="Buscar en el sistema..." aria-label="Buscar" aria-describedby="btnNavbarSearch" />
            <button class="btn btn-info px-3 text-dark fw-bold" id="btnNavbarSearch" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white opacity-75 hover-info d-flex align-items-center" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg me-1"></i>
                <span class="d-none d-lg-inline small">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary mt-2 rounded-3" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                        <i class="fa-solid fa-gear text-info me-2 w-20px text-center"></i> Configuraciones
                    </a>
                </li>
                <li><hr class="dropdown-divider border-secondary my-1" /></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="dropdown-item py-2 text-danger fw-medium hover-danger border-0 bg-transparent">
                            <i class="fa-solid fa-arrow-right-from-bracket me-2 w-20px text-center"></i>
                            Cerrar sesión
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<style>
    .w-20px { width: 20px; }
    .transition-hover { transition: all 0.2s ease-in-out; }
    .hover-info:hover { opacity: 1 !important; color: #0dcaf0 !important; }
    .hover-danger:hover { background-color: rgba(220, 53, 69, 0.1) !important; color: #ff6b6b !important; }
    .focus-ring-info:focus {
        border-color: #0dcaf0;
        box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
        background-color: #212529;
    }
    .focus-ring-info::placeholder { color: #adb5bd; }
</style>