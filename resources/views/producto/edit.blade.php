@extends('layouts.app')
@section('title', 'Editar Producto')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Editar Producto</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none">Productos</a></li>
                <li class="breadcrumb-item active">Editar registro</li>
            </ol>
        </div>
        <div class="text-muted small">ID del producto: {{ $producto->id }}</div>
    </div>

    <form action="{{ route('productos.update', $producto) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        @include('producto.partials.form')
    </form>
</div>
@endsection