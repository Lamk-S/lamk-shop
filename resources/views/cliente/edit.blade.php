@extends('template')

@section('title', 'Editar cliente')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Editar Cliente</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
        <li class="breadcrumb-item active">Editar cliente</li>
    </ol>

    <div class="container w-100 border border-3 border-primary rounded p-4 mt-3">
        <form action="{{ route('clientes.update', ['cliente' => $cliente]) }}" method="post">
            @method('PATCH')
            @csrf
            <div class="row g-3">
                <!-- Tipo de persona -->
                <div class="col-md-6">
                    <label for="tipo_persona" class="form-label">Tipo de persona: <span class="fw-bold">{{ strtoupper($cliente->persona->tipo_persona) }}</span></label>
                </div>
                
                <!-- Razón Social -->
                <div class="col-md-12 mb-2" id="box-razon-social">
                    <label id="label-juridica" for="razon_social" class="form-label">{{ $cliente->persona->tipo_persona == 'Natural' ? 'Nombres y apellidos:' : 'Nombre de la empresa:'}}
                    </label>
                    <input type="text" name="razon_social" id="razon_social" class="form-control" value="{{ old('razon_social', $cliente->persona->razon_social) }}">
                    @error('razon_social')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="col-md-12 mb-2">
                    <label for="direccion" class="form-label">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $cliente->persona->direccion) }}">
                    @error('direccion')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Documento -->
                <div class="col-md-6">
                    <label for="documento_id" class="form-label">Tipo de documento:</label>
                    <select class="form-select" name="documento_id" id="documento_id">
                        @foreach($documentos as $item)
                        @if($cliente->persona->documento_id == $item->id)
                            <option selected value="{{ $item->id }}" {{ old('documento_id') == $item->id ? 'selected' : '' }}>{{ $item->tipo_documento }}</option>
                        @else
                            <option value="{{ $item->id }}" {{ old('documento_id') == $item->id ? 'selected' : '' }}>{{ $item->tipo_documento }}</option>
                        @endif
                            
                        @endforeach
                    </select>
                    @error('documento_id')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Número de documento -->
                <div class="col-md-6">
                    <label for="numero_documento" class="form-label">Número de documento:</label>
                    <input type="text" name="numero_documento" id="numero_documento" class="form-control" value="{{ old('numero_documento', $cliente->persona->numero_documento) }}">
                    @error('numero_documento')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="reset" class="btn btn-secondary">Reiniciar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush