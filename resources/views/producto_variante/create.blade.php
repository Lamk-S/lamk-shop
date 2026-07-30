@extends('layouts.app')
@section('title', 'Nueva Variante')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 980px;">
        <div class="card-header bg-white border-bottom p-4">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-plus me-2 text-primary"></i>Registrar nueva variante</h5>
        </div>
        <div class="card-body p-4 p-md-5 bg-light bg-opacity-50">
            <form action="{{ route('producto-variantes.store') }}" method="post">
                @csrf
                @include('producto_variante.partials.form')
            </form>
        </div>
    </div>
</div>
@endsection