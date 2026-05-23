<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark border-bottom border-secondary shadow-sm">
    <!-- Marca / Logo -->
    <a class="navbar-brand ps-3 fw-bold d-flex align-items-center" href="{{ route('panel') }}">
        <img src="{{ asset('assets/img/Logo.png') }}" alt="Lamk's POS Logo" class="me-2" style="height: 32px;">
        <span class="text-white">Lamk's</span> <span class="text-info ms-1">POS</span>
    </a>
    
    <!-- Botón para ocultar/mostrar el menú lateral -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-white opacity-75 hover-info transition-hover" id="sidebarToggle" href="#!">
        <i class="fas fa-bars fs-5"></i>
    </button>
    
    <!-- Barra de búsqueda global -->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control bg-dark text-white border-secondary focus-ring-info px-3" type="text" placeholder="Buscar en el sistema..." aria-label="Buscar" aria-describedby="btnNavbarSearch" />
            <button class="btn btn-info px-3 text-dark fw-bold" id="btnNavbarSearch" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
    
    <!-- Menú de Usuario -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white opacity-75 hover-info d-flex align-items-center" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg me-1"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary mt-2 rounded-3" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                        <i class="fa-solid fa-gear text-info me-2 w-20px text-center"></i> Configuraciones
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="#!">
                        <i class="fa-solid fa-list-check text-info me-2 w-20px text-center"></i> Registro de actividad
                    </a>
                </li>
                <li><hr class="dropdown-divider border-secondary my-1" /></li>
                <li>
                    <a class="dropdown-item py-2 text-danger fw-medium hover-danger" href="{{ route('logout') }}">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2 w-20px text-center"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<style>
    .w-20px { width: 20px; }
    /* Transición suave para los iconos de la barra */
    .transition-hover { transition: all 0.2s ease-in-out; }
    /* Al pasar el mouse, los iconos se iluminan en cyan */
    .hover-info:hover { opacity: 1 !important; color: #0dcaf0 !important; }
    /* Efecto hover especial para el botón de cerrar sesión */
    .hover-danger:hover { background-color: rgba(220, 53, 69, 0.1) !important; color: #ff6b6b !important; }
    /* Resaltado del input de búsqueda en color cyan en lugar del azul por defecto */
    .focus-ring-info:focus { 
        border-color: #0dcaf0; 
        box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25); 
        background-color: #212529; /* Mantiene el fondo oscuro al escribir */
    }
    .focus-ring-info::placeholder { color: #adb5bd; }
</style>