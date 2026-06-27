<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema de Punto de Venta Corporativo" />
    <meta name="author" content="Lamk-S" />
    <title>Lamk Sports | @yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    @stack('css-datatable')
    @stack('css')

    @vite(['resources/js/app.js'])

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>

    @include('layouts.partials.alert')
    @stack('js')
</body>
</html>