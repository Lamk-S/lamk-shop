@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="page-title mb-0">Nuevo Cliente</h2>
        <ol class="breadcrumb mb-0 mt-1 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}" class="text-decoration-none text-muted">Clientes</a></li>
            <li class="breadcrumb-item active fw-medium text-dark">Crear registro</li>
        </ol>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 900px;">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-address-card text-primary me-2"></i>Datos del Cliente
            </h5>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('clientes.store') }}" method="post">
                @csrf
                @include('persona.partials.form', [
                    'persona' => null,
                    'documentos' => $documentos,
                    'showEstado' => false,
                    'cancelRoute' => route('clientes.index'),
                    'submitLabel' => 'Guardar Registro',
                    'submitIcon' => 'fas fa-save',
                ])
            </form>
        </div>
    </div>
</div>
@endsection