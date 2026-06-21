@extends('layouts.app')
@section('title', 'Nuevo Producto')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Nuevo Producto</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none text-muted">Productos</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Crear registro</li>
            </ol>
        </div>
    </div>

    <form action="{{ route('productos.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('producto.partials.form')
    </form>
</div>
@endsection