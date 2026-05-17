@extends('template')

@section('title', 'Crear producto')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Crear Producto</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">Crear producto</li>
    </ol>

    <div class="container w-100 border border-3 border-primary rounded p-4 mt-3">
        <form action="{{ route('productos.store') }}" method="post">
            @csrf
            <div class="row g-3">
                <!-- Código -->
                <div class="col-md-6">
                    <label for="codigo" class="form-label">Código:</label>
                    <input type="text" name="codigo" id="codigo" class="form-control" value="{{ old('codigo') }}">
                    @error('codigo')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}">
                    @error('nombre')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Descripcion -->
                <div class="col-md-12">
                    <label for="descripcion" class="form-label">Descripcion:</label>
                    <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Fecha de vencimiento -->
                <div class="col-md-6">
                    <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento:</label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
                    @error('fecha_vencimiento')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Imagen -->
                <div class="col-md-6">
                    <label for="img_path" class="form-label">Imagen:</label>
                    <input type="file" name="img_path" id="img_path" class="form-control" value="{{ old('img_path') }}">
                    @error('img_path')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Marca -->
                <div class="col-md-6">
                    <label for="marca_id" class="form-label">Marca:</label>
                    <select data-size="4" title="Seleccione una marca" data-live-search="true" name="marca_id" id="marca_id" class="form-control selectpicker">
                        @foreach($marcas as $item)
                            <option value="{{ $item->id }}" {{ old('marca_id')== $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                    @error('marca_id')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Presentación -->
                <div class="col-md-6">
                    <label for="presentacione_id" class="form-label">Presentación:</label>
                    <select data-size="4" title="Seleccione una presentación" data-live-search="true" name="presentacione_id" id="presentacione_id" class="form-control selectpicker show-tick">
                        @foreach($presentaciones as $item)
                            <option value="{{ $item->id }}" {{ old('presentacione_id')== $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                    @error('presentacione_id')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Categoría -->
                <div class="col-md-6">
                    <label for="categoria_id" class="form-label">Categoría:</label>
                    <select data-size="4" title="Seleccione una categoría" data-live-search="true" name="categoria_id[]" id="categoria_id" class="form-control selectpicker show-tick" multiple>
                        @foreach($categorias as $item)
                            <option value="{{ $item->id }}" {{ in_array($item->id, old('categoria_id', [])) ? 'selected' : '' }}>{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <small class="text-danger">{{ '*'.$message }}</small>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush