@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Resumen Operativo</h2>
            <p class="text-muted small mb-0">Visión general del estado del sistema</p>
        </div>
        <div>
            <span class="text-muted small border bg-white px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-calendar-event me-1"></i> {{ date('d M, Y') }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('clientes.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Clientes</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\Cliente::count() }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('proveedores.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Proveedores</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\Proveedor::count() }}</h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-truck-moving fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('productos.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Productos</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\Producto::count() }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-box fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('ventas.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Ventas</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\Venta::count() }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-cash-register fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('compras.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Compras</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\Compra::count() }}</h3>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-store fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('cajas.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Cajas</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\Caja::count() }}</h3>
                            </div>
                            <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-cash-register fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Sesiones activas</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\SesionCaja::where('estado', 1)->count() }}</h3>
                            </div>
                            <div class="bg-dark bg-opacity-10 text-dark p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-lock-open fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        @isset($tesoreria)
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('tesoreria.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Tesorería efectivo</p>
                                <h3 class="fw-bold text-dark mb-0">S/ {{ number_format($tesoreria->saldo_efectivo ?? 0, 2) }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-vault fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('tesoreria.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Tesorería banco</p>
                                <h3 class="fw-bold text-dark mb-0">S/ {{ number_format($tesoreria->saldo_banco ?? 0, 2) }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-building-columns fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endisset

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('users.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted text-uppercase fw-semibold fs-7 mb-1">Usuarios</p>
                                <h3 class="fw-bold text-dark mb-0">{{ \App\Models\User::count() }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-user-shield fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
    }
    .fs-7 {
        font-size: 0.85rem;
    }
</style>
@endsection