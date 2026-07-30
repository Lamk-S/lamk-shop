@extends('layouts.app')
@section('title', 'Editar Producto')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Editar Producto</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none text-muted">Productos</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Editar registro</li>
            </ol>
        </div>
        <div class="badge bg-light text-secondary border shadow-sm px-3 py-2 fs-6 rounded-pill">
            ID: {{ str_pad($producto->id, 4, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <form action="{{ route('productos.update', $producto) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        @include('producto.partials.form')
    </form>
</div>
@endsection