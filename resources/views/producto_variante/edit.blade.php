@extends('layouts.app')
@section('title', 'Editar Variante')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 980px;">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Editar Variante</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('producto-variantes.index') }}" class="text-decoration-none text-muted">Variantes</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Registro #{{ $productoVariante->id }}</li>
            </ol>
        </div>
        <span class="badge bg-white text-dark border px-3 py-2 fs-6 shadow-sm">
            <i class="fas fa-barcode text-muted me-1"></i>{{ $productoVariante->codigo_variante }}
        </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 980px;">
        <div class="card-header bg-white border-bottom p-4">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i>Actualizar datos de la variante</h5>
        </div>
        <div class="card-body p-4 p-md-5 bg-light bg-opacity-50">
            <form action="{{ route('producto-variantes.update', $productoVariante) }}" method="post">
                @method('PATCH')
                @csrf
                @include('producto_variante.partials.form')
            </form>
        </div>
    </div>
</div>
@endsection