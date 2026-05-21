@extends('template')

@section('title', 'Perfil')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

@if(session('success'))
<script>
    let message = "{{ session('success') }}";
    Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    }).fire({
        icon: "success",
        title: message
    });
</script>
@endif

<div class="container">
    <h1 class="mt-4 text-center">Configurar perfil</h1>
    <div class="container card">
        <div class="card-body">
            <form action="{{ route('profile.update', ['profile' => $user]) }}" method="post">
                @method('PATCH')
                @csrf
                <!-- Nombres -->
                <div class="row">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-square-check"></i></span>
                            <input disabled type="text" class="form-control" value="Nombres">
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}">
                        @error('name')
                            <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!-- Correo electrónico -->
                <div class="row mt-3">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-square-check"></i></span>
                            <input disabled type="text" class="form-control" value="Correo electrónico">
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="row mt-3">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-square-check"></i></span>
                            <input disabled type="text" class="form-control" value="Contraseña">
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese una nueva contraseña">
                        @error('password')
                            <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>                

                <div class="col text-center mt-4">
                    <button type="submit" class="btn btn-success">Actualizar perfil</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
    
@endpush