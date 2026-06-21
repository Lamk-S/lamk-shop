<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>500 - Error de Sistema | Lamk Sports</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body { background-color: #f8fafc; display: flex; align-items: center; min-height: 100vh; }
        .error-card { border: 0; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); background: #fff; overflow: hidden; padding: 4rem 2rem; }
        .error-code { font-size: 6rem; font-weight: 900; line-height: 1; color: #cbd5e1; letter-spacing: -0.05em; margin-bottom: 1rem; }
        .icon-circle { width: 80px; height: 80px; background-color: rgba(255, 193, 7, 0.1); color: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; font-size: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="error-card text-center">
                    <div class="icon-circle">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="error-code">500</div>
                    <h2 class="fw-bold text-dark mb-3">Error Interno</h2>
                    <p class="text-muted mb-4 fs-6">
                        El sistema encontró un problema inesperado procesando tu solicitud. Por favor, intenta de nuevo en unos momentos o contacta a soporte técnico.
                    </p>
                    <button onclick="window.location.reload();" class="btn btn-warning px-4 py-2 text-dark rounded-pill fw-bold shadow-sm mb-2 me-2">
                        <i class="fas fa-rotate-right me-2"></i> Reintentar
                    </button>
                    <a href="{{ route('panel') }}" class="btn btn-light border px-4 py-2 rounded-pill fw-bold shadow-sm mb-2">
                        <i class="fas fa-arrow-left me-2"></i> Panel
                    </a>
                </div>
                <div class="text-center mt-4 text-muted small fw-medium">
                    &copy; {{ date('Y') }} Lamk Sports. Todos los derechos reservados.
                </div>
            </div>
        </div>
    </div>
</body>
</html>