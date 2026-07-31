<div class="card border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden scanner-card" id="scanner-container">
    <div class="position-absolute top-0 end-0 p-3 opacity-10 d-none d-md-block">
        <i class="fa-solid fa-barcode fa-4x text-primary"></i>
    </div>
    <div class="card-body p-3 p-md-4">
        <label for="codigo_escaner" class="form-label fw-bold text-dark d-flex align-items-center mb-2">
            <span class="scanner-dot"></span>
            <span class="fs-6 fs-md-5">
                Lector de Código de Barras
            </span>
        </label>
        <p class="text-muted small mb-3">
            Pistola o App activa. El cursor debe estar en la caja azul para escanear.
        </p>
        <div class="input-group input-group-lg shadow-sm rounded-3 border border-2" id="scanner-input-group">
            <span class="input-group-text bg-white border-0 text-primary">
                <i class="fas fa-qrcode"></i>
            </span>
            <input type="text" id="codigo_escaner" class="form-control border-0 fw-bold font-monospace" placeholder="Pistolee el código aquí..." autocomplete="off" autofocus>
        </div>
        <div id="scanner-indicator" class="small mt-2 text-success fw-medium">
            <i class="fas fa-keyboard me-1"></i>
            <span id="scanner-status-text">
                Listo para recibir datos
            </span>
        </div>
    </div>
</div>