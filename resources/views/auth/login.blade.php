<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Inicio de sesión - Lamk's POS" />
    <meta name="author" content="Lamk-S" />
    <title>Iniciar Sesión | Lamk Sports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .form-control:focus {
            border-color: #0dcaf0;
            box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
        }

        .transition {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>

<body class="bg-dark d-flex flex-column min-vh-100">
    <main class="flex-grow-1 d-flex align-items-center justify-content-center p-3">
        <div class="login-container">
            <div class="card bg-body-tertiary border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}" class="text-decoration-none">
                            <img src="{{ asset('assets/img/Logo.png') }}" alt="Lamk Sports Logo" class="mb-3"
                                style="height: 50px;">
                            <h3 class="fw-bold text-white mb-1">Lamk <span class="text-info">Sports</span></h3>
                        </a>
                        <p class="text-muted fs-6">Ingresa tus credenciales para continuar</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm"
                            role="alert">
                            <ul class="mb-0 ps-3 pe-3">
                                @foreach ($errors->all() as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Cerrar"></button>
                        </div>
                    @endif

                    <form action="{{ route('login.store') }}" method="post">
                        @csrf
                        <div class="form-floating mb-3">
                            <input
                                class="form-control bg-dark border-secondary text-white rounded-3 @error('email') is-invalid @enderror"
                                name="email" id="inputEmail" type="email" placeholder="name@example.com"
                                value="{{ old('email', $lastEmail) }}" required autofocus />
                            <label for="inputEmail" class="text-muted">
                                <i class="bi bi-envelope me-2"></i>Correo electrónico
                            </label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input
                                class="form-control bg-dark border-secondary text-white rounded-3 @error('password') is-invalid @enderror"
                                name="password" id="inputPassword" type="password" placeholder="Password" required />
                            <label for="inputPassword" class="text-muted">
                                <i class="bi bi-shield-lock me-2"></i>Contraseña
                            </label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input border-secondary" type="checkbox" name="remember"
                                    id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small fw-medium" for="remember">
                                    Mantener sesión activa
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit"
                                class="btn btn-info text-dark fw-bold py-3 rounded-3 shadow-sm transition">
                                Iniciar Sesión <i class="bi bi-box-arrow-in-right ms-2"></i>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top border-secondary">
                        <a href="{{ url('/') }}" class="text-muted text-decoration-none small">
                            <i class="bi bi-arrow-left me-1"></i> Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-3 mt-auto border-top border-secondary bg-dark text-center">
        <div class="container">
            <span class="text-muted small">
                Copyright &copy; {{ date('Y') }} Lamk Sports. Creado por
                <a href="https://github.com/Lamk-S" class="text-info text-decoration-none fw-semibold" target="_blank"
                    rel="noreferrer">Lamk-S</a>
            </span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
</body>

</html>
