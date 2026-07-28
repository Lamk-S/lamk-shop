<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 | Página no encontrada</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        :root { --bg:#f4f6f9; --card:#fff; --border:#e5e7eb; --text:#111827; --muted:#6b7280; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; display:flex; justify-content:center; align-items:center; background:var(--bg); font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }
        .error-wrapper { width:100%; max-width:560px; padding:24px; }
        .error-card { background:var(--card); border:1px solid var(--border); border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(15,23,42,.05),0 1px 3px rgba(15,23,42,.08); }
        .error-accent { height:4px; background:#111827; }
        .error-body { padding:48px; text-align:center; }
        .error-icon { width:64px; height:64px; margin:0 auto 24px; border-radius:16px; background:#f3f4f6; color:#111827; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
        .error-code { font-size:4.5rem; font-weight:800; line-height:1; color:#d1d5db; letter-spacing:-2px; margin-bottom:16px; }
        .error-title { font-size:1.75rem; font-weight:700; color:var(--text); margin-bottom:12px; }
        .error-description { max-width:420px; margin:0 auto 36px; color:var(--muted); font-size:.97rem; line-height:1.7; }
        .btn-return { min-width:220px; height:48px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; gap:.6rem; font-weight:600; transition:.25s ease; }
        .btn-return:hover { transform:translateY(-1px); }
        .error-footer { margin-top:22px; text-align:center; color:#9ca3af; font-size:.85rem; }
        @media (max-width:576px) {
            .error-body { padding:36px 28px; }
            .error-code { font-size:3.6rem; }
            .error-title { font-size:1.45rem; }
            .btn-return { width:100%; }
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="error-card">
            <div class="error-accent"></div>
            <div class="error-body">
                <div class="error-icon">
                    <i class="fas fa-magnifying-glass"></i>
                </div>
                <div class="error-code">404</div>
                <h1 class="error-title">Página no encontrada</h1>
                <p class="error-description">
                    El recurso solicitado no existe o ya no se encuentra disponible. Verifique la dirección o regrese al panel principal para continuar navegando.
                </p>
                <a href="{{ route('panel') }}" class="btn btn-dark btn-return">
                    <i class="fas fa-house"></i>
                    Volver al panel
                </a>
            </div>
        </div>
        <div class="error-footer">
            &copy; {{ date('Y') }} Lamk Sports. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>