<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Lamk's POS - El Sistema de Punto de Venta definitivo para gestionar compras, ventas, inventario y clientes. Optimiza tu negocio con tecnología de vanguardia." />
    <meta name="author" content="Lamk-S" />
    <title>Lamk's POS | Tu Negocio Inteligente</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .carousel-item img {
            height: 600px;
            object-fit: cover;
            filter: brightness(40%);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0dcaf0;
        }
    </style>
</head>

<body>

    <!-- Barra de navegación --->
    <nav class="navbar navbar-expand-md bg-body-tertiary sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <!-- Marca navegación -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/Logo.png') }}" 
                    alt="Logo" 
                    class="me-2"
                    style="height: 40px;">

                Lamk's <span class="text-info ms-1">POS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Lista de opciones del menú -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-medium">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#beneficios">Beneficios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#caracteristicas">Características</a>
                    </li>
                </ul>

                <div class="d-flex gap-2">
                    <!-- Suponiendo que usas Laravel Fortify, Breeze o similar para auth -->
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-info">Ir al Panel</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light">Iniciar sesión</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-info text-dark fw-semibold">Prueba Gratis</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Carrusel Principal --->
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=1470&auto=format&fit=crop" class="d-block w-100" alt="Punto de venta moderno">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 mb-0">
                    <h1 class="display-4 fw-bold text-white mb-3">Control total de tus ventas</h1>
                    <p class="fs-4 text-light mb-4">Gestiona tu inventario, ventas y clientes desde un solo lugar con Lamk's POS. Rápido, seguro y en la nube.</p>
                    <div>
                        <a href="#beneficios" class="btn btn-info btn-lg text-dark fw-bold px-4 rounded-pill">Descubrir más</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1556740749-887f6717d7e4?q=80&w=1470&auto=format&fit=crop" class="d-block w-100" alt="Análisis de datos de ventas">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 mb-0">
                    <h1 class="display-4 fw-bold text-white mb-3">Toma decisiones inteligentes</h1>
                    <p class="fs-4 text-light mb-4">Reportes en tiempo real y métricas exactas para hacer crecer tu negocio al siguiente nivel.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- Section Beneficios --->
    <div class="container-md my-5 py-5" id="beneficios">
        <div class="text-center mb-5">
            <h2 class="fw-bold">¿Por qué elegir <span class="text-info">Lamk's POS</span>?</h2>
            <p class="text-muted fs-5">Diseñado para simplificar la operación diaria de tu negocio.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-body-tertiary shadow-sm text-center p-4 hover-shadow transition">
                    <i class="bi bi-box-seam feature-icon mb-3"></i>
                    <h5 class="card-title fw-bold">Inventario en Tiempo Real</h5>
                    <p class="card-text text-muted">Controla tus existencias al milímetro. Recibe alertas de stock bajo y automatiza tus órdenes de compra.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-body-tertiary shadow-sm text-center p-4">
                    <i class="bi bi-receipt feature-icon mb-3"></i>
                    <h5 class="card-title fw-bold">Facturación Ágil</h5>
                    <p class="card-text text-muted">Cobra en segundos. Genera tickets, facturas y comprobantes digitales con múltiples métodos de pago.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-body-tertiary shadow-sm text-center p-4">
                    <i class="bi bi-graph-up-arrow feature-icon mb-3"></i>
                    <h5 class="card-title fw-bold">Reportes y Analíticas</h5>
                    <p class="card-text text-muted">Visualiza tus productos más vendidos, ingresos diarios y rendimiento de vendedores al instante.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 bg-body-tertiary shadow-sm text-center p-4">
                    <i class="bi bi-cloud-check feature-icon mb-3"></i>
                    <h5 class="card-title fw-bold">100% en la Nube</h5>
                    <p class="card-text text-muted">Accede a tu sistema 24/7 desde cualquier dispositivo (PC, Tablet o Smartphone) de forma segura.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Comparativa (Características) --->
    <div class="container-fluid bg-body-tertiary py-5" id="caracteristicas">
        <div class="container my-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">El salto tecnológico que tu empresa necesita</h2>
                    <p class="fs-5 text-muted mb-4">Dejar atrás el papel o las hojas de cálculo no solo ahorra tiempo, sino que evita fugas de dinero por errores humanos.</p>
                    
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent text-light border-secondary d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-info me-3 fs-5"></i> Cierre de caja automático y sin descuadres.
                        </li>
                        <li class="list-group-item bg-transparent text-light border-secondary d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-info me-3 fs-5"></i> Gestión de múltiples sucursales y almacenes.
                        </li>
                        <li class="list-group-item bg-transparent text-light border-secondary d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-info me-3 fs-5"></i> Base de datos de clientes para fidelización.
                        </li>
                        <li class="list-group-item bg-transparent text-light border-secondary d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-info me-3 fs-5"></i> Perfiles y permisos personalizados para cajeros y administradores.
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1430&auto=format&fit=crop" class="img-fluid rounded-4 shadow-lg" alt="Dashboard Lamk's POS">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer --->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase fw-bold text-info mb-3">
                        <img src="{{ asset('assets/img/Logo.png') }}" alt="Lamk's POS Logo" height="32" class="me-2">
                        Lamk's POS
                    </h5>
                    <p class="text-muted">
                        Solución integral de Punto de Venta diseñada para pequeñas, medianas y grandes empresas. Automatiza, controla y crece.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase fw-bold mb-3">Enlaces Útiles</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="#beneficios" class="text-muted text-decoration-none hover-white">Beneficios</a></li>
                        <li class="mb-2"><a href="#caracteristicas" class="text-muted text-decoration-none hover-white">Características</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-white">Soporte Técnico</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0 text-lg-end text-sm-start">
                    <h5 class="text-uppercase fw-bold mb-3">Conecta con el Desarrollador</h5>
                    <p class="text-muted mb-2">Desarrollado y mantenido por <strong>Lamk-S</strong></p>
                    <!-- Redes Sociales de Lamk-S -->
                    <div class="mt-3">
                        <!-- LinkedIn -->
                        <a class="btn btn-outline-info btn-floating m-1" href="https://www.linkedin.com/in/lamk-sd" role="button" target="_blank" title="LinkedIn de Lamk-S">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <!-- GitHub -->
                        <a class="btn btn-outline-light btn-floating m-1" href="https://github.com/Lamk-S" role="button" target="_blank" title="GitHub de Lamk-S">
                            <i class="bi bi-github"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center p-3 border-top border-secondary mt-4">
                <span class="text-muted">© {{ date('Y') }} Lamk's POS. Todos los derechos reservados. | Software creado por </span>
                <a class="text-info text-decoration-none fw-semibold" href="https://github.com/Lamk-S" target="_blank">Lamk-S</a>
            </div>
        </div>
    </footer>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>