@extends('layouts.app')

@section('title', 'Editar Cliente')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="page-title mb-0">Modificar Cliente</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}" class="text-decoration-none text-muted">Clientes</a></li>
            <li class="breadcrumb-item active fw-medium text-dark">Editar registro</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 900px;">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-address-card text-warning me-2"></i>Datos del Cliente
            </h5>
            <span class="badge bg-light text-secondary border">ID: {{ $cliente->id }}</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('clientes.update', $cliente) }}" method="post">
                @method('PATCH')
                @csrf

                @php
                    $persona = $cliente->persona;
                @endphp

                @include('persona.partials.form', [
                    'persona' => $persona,
                    'documentos' => $documentos,
                    'showEstado' => true,
                    'cancelRoute' => route('clientes.index'),
                    'submitLabel' => 'Actualizar Cliente',
                    'submitIcon' => 'fas fa-sync-alt',
                ])
            </form>
        </div>
    </div>
</div>
@endsection