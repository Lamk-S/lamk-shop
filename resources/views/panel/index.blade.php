@extends('template')

@section('title', 'panel')

@push('css')
<link href="https://cdn.jsdelivr.net/nSpm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

@if(@session('success'))
<script>
    let message = "{{ session('success') }}";
    Swal.fire({
    title: message,
    showClass: { popup: `
        animate__animated
        animate__fadeInUp
        animate__faster
        ` },
    hideClass: { popup: `
        animate__animated
        animate__fadeOutDown
        animate__faster
        ` }
    });
</script>
@endif

<div class="container-fluid px-4">
    <h1 class="mt-4">Panel</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Panel</li>
    </ol>
    <div class="row">
        <!-- Clientes -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-solid fa-users"></i><span> Clientes</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\Cliente;
                            $clientes = count(Cliente::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $clientes }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('clientes.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Categorias -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-solid fa-tag"></i><span> Categorias</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\Categoria;
                            $categorias = count(Categoria::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $categorias }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('categorias.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Compras -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-solid fa-store"></i><span> Compras</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\Compras;
                            $compras = count(Compras::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $compras }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('compras.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Marcas -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-solid fa-bullhorn"></i><span> Marcas</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\Marca;
                            $marcas = count(Marca::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $marcas }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('marcas.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Presentaciones -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-solid fa-box-archive"></i><span> Presentaciones</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\Presentacione;
                            $presentaciones = count(Presentacione::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $presentaciones }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('presentaciones.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Productos -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-brands fa-shopify"></i><span> Productos</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\Producto;
                            $productos = count(Producto::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $productos }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('productos.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Proveedores -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-solid fa-user-group"></i><span> Proveedores</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\Proveedore;
                            $proveedores = count(Proveedore::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $proveedores }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('proveedores.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Usuarios -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <i class="fa-solid fa-user"></i><span> Usuarios</span>
                        </div>
                        <div class="col-4">
                            <?php 
                            use App\Models\User;
                            $usuarios = count(User::all());
                            ?>
                            <p class="text-center fw-bold fs-4">{{ $usuarios }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('users.index') }}">Ver más</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="{{ asset("assets/demo/chart-area-demo.js") }}"></script>
<script src="{{ asset("assets/demo/chart-bar-demo.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="{{ asset("js/datatables-simple-demo.js") }}"></script>
@endpush