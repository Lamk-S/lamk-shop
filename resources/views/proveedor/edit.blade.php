@extends('layouts.app')

@section('title', 'Editar Proveedor')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Modificar Proveedor</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}" class="text-decoration-none">Proveedores</a></li>
            <li class="breadcrumb-item active">Editar registro</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 900px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-truck-moving text-warning me-2"></i>Datos del Proveedor
            </h5>
            <span class="badge bg-light text-secondary border">ID: {{ $proveedor->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('proveedores.update', $proveedor) }}" method="post">
                @method('PATCH')
                @csrf

                @php
                    $persona = $proveedor->persona;
                @endphp

                @include('persona.partials.form', [
                    'persona' => $persona,
                    'documentos' => $documentos,
                    'showEstado' => true,
                    'cancelRoute' => route('proveedores.index'),
                    'submitLabel' => 'Actualizar Proveedor',
                    'submitIcon' => 'fas fa-sync-alt',
                ])
            </form>
        </div>
    </div>
</div>
@endsection