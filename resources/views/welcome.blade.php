<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Lamk Sports POS - El Sistema de Punto de Venta definitivo para tiendas deportivas. Gestiona tallas, marcas, inventario y ventas con tecnología de vanguardia." />
    <meta name="author" content="Lamk-S" />
    <title>Lamk Sports | POS para Tiendas Deportivas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --brand-primary: #0dcaf0; --brand-dark: #121416; }
        body { background-color: var(--brand-dark); font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .navbar { backdrop-filter: blur(10px); background-color: rgba(33, 37, 41, 0.95) !important; }
        .hero-section { position: relative; height: 85vh; min-height: 600px; display: flex; align-items: center; overflow: hidden; }
        .hero-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: linear-gradient(rgba(18, 20, 22, 0.8), rgba(18, 20, 22, 0.9)), url('{{ asset('assets/img/Banner.jpg') }}'); background-size: cover; background-position: center; z-index: -1; }
        .feature-card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 1.5rem; transition: transform 0.3s ease, border-color 0.3s ease; }
        .feature-card:hover { transform: translateY(-10px); border-color: var(--brand-primary); background: rgba(255, 255, 255, 0.05); }
        .feature-icon { width: 60px; height: 60px; background: rgba(13, 202, 240, 0.1); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: var(--brand-primary); margin-bottom: 1.5rem; }
        .gradient-text { background: linear-gradient(45deg, #fff, #0dcaf0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .separator { border: 0; height: 0px; background: rgba(255, 255, 255, 0.08); margin: 3rem 0; }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-md navbar-dark sticky-top shadow-sm border-bottom border-secondary">
        <div class="container-fluid px-4 py-2">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/Logo.png') }}" alt="Logo" class="me-2" style="height: 35px; object-fit: contain;">
                <span class="tracking-wide">Lamk</span> <span class="text-info ms-1">Sports</span>
            </a>
            <button class="navbar-toggler border-0 focus-ring" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-medium ms-md-4">
                    <li class="nav-item"><a class="nav-link text-white" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#soluciones">Soluciones</a></li>
                    <li class="nav-item"><a class="nav-link" href="#modulos">Módulos</a></li>
                </ul>

                <div class="d-flex gap-3 align-items-center mt-3 mt-md-0">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('panel') }}" class="btn btn-info text-dark fw-bold px-4 rounded-pill shadow-sm">
                                <i class="bi bi-speedometer2 me-2"></i>Ir al Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-light text-decoration-none fw-medium hover-info">Ingresar al POS</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-info rounded-pill px-4 fw-medium">Solicitar Demo</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="hero-bg"></div>
        <div class="container text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-10 mx-auto mx-md-0">
                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill mb-4 fw-bold">v2.0 Especializada en Retail Deportivo</span>
                    <h1 class="display-3 fw-bold text-white mb-4 lh-sm">
                        Potencia tu Tienda con <br> <span class="gradient-text">Lamk Sports POS</span>
                    </h1>
                    <p class="fs-4 text-light mb-5 opacity-75 fw-light">
                        El sistema definitivo para gestionar zapatillas, ropa y accesorios. Controla tallas, marcas, cajas y fideliza a tus clientes deportivos en tiempo real.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-md-start">
                        @auth
                            <a href="{{ route('panel') }}" class="btn btn-info btn-lg text-dark fw-bold px-5 rounded-pill shadow">Acceder al Sistema</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-info btn-lg text-dark fw-bold px-5 rounded-pill shadow">Iniciar Turno</a>
                        @endauth
                        <a href="#soluciones" class="btn btn-outline-light btn-lg px-5 rounded-pill">Ver características</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <hr class="separator" id="soluciones">
    <section class="container my-5 py-5" >
        <div class="text-center mb-5 pb-3">
            <h2 class="fw-bold display-6">Pensado para el <span class="text-info">Retail Deportivo</span></h2>
            <p class="text-muted fs-5">Herramientas creadas específicamente para las dinámicas de venta de ropa y calzado.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-tag-fill"></i></div>
                    <h5 class="fw-bold text-white mb-3">Tallas y Variantes</h5>
                    <p class="text-muted mb-0">Gestiona un mismo modelo de zapatilla o camiseta en diferentes colores y tallas sin saturar tu catálogo. Todo unificado.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-upc-scan"></i></div>
                    <h5 class="fw-bold text-white mb-3">Venta Rápida POS</h5>
                    <p class="text-muted mb-0">Interfaz optimizada para el uso de lectores de códigos de barra. Evita colas y procesa las ventas de tus clientes en segundos.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-box-seam-fill"></i></div>
                    <h5 class="fw-bold text-white mb-3">Kardex Preciso</h5>
                    <p class="text-muted mb-0">Auditoría completa de movimientos de inventario. Conoce exactamente cuándo ingresó o se vendió el último par de zapatillas.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-wallet-fill"></i></div>
                    <h5 class="fw-bold text-white mb-3">Control de Caja</h5>
                    <p class="text-muted mb-0">Aperturas, cierres y movimientos de tesorería transparentes. Soporte para pagos mixtos (Efectivo, Tarjeta, Transferencia).</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-fluid bg-black py-5 border-top border-secondary" id="modulos">
        <div class="container my-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 order-2 order-lg-1">
                    <img src="{{ asset('assets/img/Operacion-Tienda.jpg') }}" class="img-fluid rounded-4 shadow-lg border border-secondary" alt="Operación en tienda deportiva">
                </div>
                <div class="col-lg-6 order-1 order-lg-2 ps-lg-5">
                    <h2 class="fw-bold mb-4 display-6">Cero pérdidas, <br><span class="text-info">máxima productividad</span></h2>
                    <p class="fs-5 text-muted mb-4">Lamk Sports conecta tu almacén con tu mostrador. Cada venta descuenta el stock exacto y actualiza tu tesorería al instante.</p>
                    
                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-check2-circle text-info fs-3 me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold text-white">Directorio de Clientes</h5>
                            <p class="text-muted mb-0">Registra a tus compradores, conoce su historial de compras de ropa y calzado para ofrecerles mejores promociones.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-check2-circle text-info fs-3 me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold text-white">Gestión de Proveedores</h5>
                            <p class="text-muted mb-0">Administra tus marcas (Nike, Adidas, Puma) y lleva el control de cuentas por pagar de tu abastecimiento.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <i class="bi bi-check2-circle text-info fs-3 me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold text-white">Seguridad Basada en Roles</h5>
                            <p class="text-muted mb-0">Los cajeros solo ven el POS, los administradores tienen acceso a la auditoría y rentabilidad. Privacidad garantizada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark pt-5 pb-4 border-top border-secondary">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-5 col-md-12 mb-4 mb-md-0">
                    <a class="navbar-brand fw-bold d-flex align-items-center mb-3" href="#">
                        <img src="{{ asset('assets/img/Logo.png') }}" alt="Logo" class="me-2" style="height: 30px;">
                        <span class="text-white">Lamk</span> <span class="text-info ms-1">Sports</span>
                    </a>
                    <p class="text-muted pe-lg-5">
                        El motor tecnológico detrás de las mejores tiendas deportivas. Innovación, velocidad y control para hacer crecer tu pasión por el deporte y las ventas.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h6 class="text-uppercase fw-bold text-white mb-3">Navegación</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="#soluciones" class="text-muted text-decoration-none hover-info">Características</a></li>
                        <li class="mb-2"><a href="#modulos" class="text-muted text-decoration-none hover-info">Módulos del Sistema</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-muted text-decoration-none hover-info">Acceso Empleados</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 mb-4 mb-md-0 text-md-end text-sm-start">
                    <h6 class="text-uppercase fw-bold text-white mb-3">Desarrollo y Soporte</h6>
                    <p class="text-muted mb-3">Plataforma diseñada y estructurada por <br><strong class="text-white">Lamk-S</strong></p>
                    <div class="d-flex justify-content-md-end justify-content-start gap-2">
                        <a class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" href="https://www.linkedin.com/in/lamk-sd" target="_blank" title="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" href="https://github.com/Lamk-S" target="_blank" title="GitHub">
                            <i class="bi bi-github"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center p-3 border-top border-secondary mt-2">
                <span class="text-muted small">© {{ date('Y') }} Lamk Sports. Operando con tecnología moderna. | Software creado por </span>
                <a class="text-info text-decoration-none fw-semibold small" href="https://github.com/Lamk-S" target="_blank">Lamk-S</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .hover-info { transition: color 0.2s; }
        .hover-info:hover { color: var(--brand-primary) !important; }
    </style>
</body>
</html>