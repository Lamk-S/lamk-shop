@extends('template')

@section('title','Crear presentación')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Crear Presentación</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('presentaciones.index')}}">Presentaciones</a></li>
        <li class="breadcrumb-item active">Crear presentación</li>
    </ol>

    <div class="card">
        <form action="{{ route('presentaciones.store') }}" method="post">
            @csrf
            <div class="card-body text-bg-light">

                <div class="row g-4">

                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre')}}">
                        @error('nombre')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="sigla" class="form-label">
                            Sigla:
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="sigla" id="sigla" class="form-control" value="{{ old('sigla') }}" required>

                        @error('sigla')
                            <small class="text-danger">
                                {{ '*'.$message }}
                            </small>
                        @enderror

                    </div>

                    <div class="col-12">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{old('descripcion')}}</textarea>
                        @error('descripcion')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('js')

@endpush