<div class="card border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden scanner-card"
    style="background-color: #f0f7ff;">

    <div class="position-absolute top-0 end-0 p-3 opacity-10 scanner-bg-icon">
        <i class="fa-solid fa-barcode text-primary"></i>
    </div>

    <div class="card-body p-4 d-flex flex-column gap-2 scanner-body">

        <label for="codigo_escaner" class="form-label fw-bold text-dark mb-0 scanner-title">
            <i class="fas fa-wifi text-success me-2"></i>
            Recepción de Escáner Inalámbrico
        </label>

        <p class="text-muted small mb-2 scanner-description">
            Conecta tu app móvil a la PC. Escanea la etiqueta de la prenda o zapatilla y se agregará al instante.
        </p>

        <div class="input-group input-group-lg shadow-sm rounded-3 scanner-group">
            <span class="input-group-text bg-white border-0 text-primary">
                <i class="fas fa-qrcode"></i>
            </span>

            <input
                type="text"
                id="codigo_escaner"
                class="form-control border-0 fw-bold font-monospace text-dark scanner-input"
                placeholder="Esperando código..."
                autocomplete="off"
                autofocus>
        </div>

        <div id="scanner-indicator" class="small mt-1 text-success fw-medium">
            <i class="fas fa-circle-notch fa-spin me-1"></i>
            Cursor activo. Listo para escanear.
        </div>

    </div>
</div>