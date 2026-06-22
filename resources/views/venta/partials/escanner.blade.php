<div class="card border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden" style="background-color: #f0f7ff;">
    <div class="position-absolute top-0 end-0 p-3 opacity-10">
        <i class="fa-solid fa-barcode fa-4x text-primary"></i>
    </div>
    
    <div class="card-body p-4 d-flex flex-column gap-2">
        <label for="codigo_escaner" class="form-label fw-bold text-dark mb-0 fs-6">
            <i class="fas fa-wifi text-success me-2"></i>Recepción de Escáner Inalámbrico
        </label>
        <p class="text-muted small mb-2">Conecta tu app móvil a la PC. Escanea la etiqueta de la prenda o zapatilla y se agregará al instante.</p>
        
        <div class="input-group input-group-lg shadow-sm rounded-3">
            <span class="input-group-text bg-white border-0 text-primary"><i class="fas fa-qrcode"></i></span>
            <input type="text" id="codigo_escaner" class="form-control border-0 fw-bold font-monospace text-dark fs-4" placeholder="Esperando código de barras..." autocomplete="off" autofocus>
        </div>
        
        <div id="scanner-indicator" class="small mt-1 text-success fw-medium">
            <i class="fas fa-circle-notch fa-spin me-1"></i> Cursor activo. Listo para escanear.
        </div>
    </div>
</div>