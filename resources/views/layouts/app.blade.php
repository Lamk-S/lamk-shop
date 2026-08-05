<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema de Punto de Venta Corporativo" />
    <meta name="author" content="Lamk-S" />
    <title>Lamk Sports | @yield('title')</title>

    <!-- Librerías de Terceros (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    
    @vite([
        'resources/css/styles.css', 
        'resources/js/app.js', 
        'resources/js/scripts.js'
    ])

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous" defer></script>

    @stack('css-datatable')
    @stack('css')

    <style>
        body { background-color: #f4f6f9; color: #333; }
        .sb-nav-fixed #layoutSidenav #layoutSidenav_content { background-color: #f8f9fa; }
    </style>
</head>
<body class="sb-nav-fixed">
    <x-navigation-header />

    <div id="layoutSidenav">
        <x-navigation-menu />

        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            <x-footer />
        </div>
    </div>

    <!-- Scripts de Terceros (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Inicialización global simple
        $(function () {
            if($.fn.selectpicker) {
                $('.selectpicker').selectpicker();
            }
        });
    </script>

    <x-scripts-persona />
    
    @include('layouts.partials.alert')
    <x-modal-confirmacion />

    @stack('js')
</body>
</html>