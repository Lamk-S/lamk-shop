@php
    $defaultComprobanteId = old('comprobante_id', optional($comprobantes->first())->id);
    $defaultMetodoPago = old('metodo_pago', 'EFECTIVO');
    $defaultMoneda = old('moneda', 'PEN');
@endphp

<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex align-items-center">
        <i class="fa-solid fa-file-invoice text-success fs-5 me-2"></i>
        <h5 class="mb-0 fw-semibold text-dark fs-6 fs-md-5">Datos de Facturación y Pago</h5>
    </div>

    <div class="card-body p-3 p-md-4">
        <div class="row g-3">
            <div class="col-12">
                <label for="proveedor_id" class="form-label fw-medium text-secondary small">Proveedor <span class="text-danger">*</span></label>
                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                    <small class="text-muted">Seleccione o registre un nuevo proveedor.</small>
                    <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#quickProveedorModal">
                        <i class="fa-solid fa-user-plus me-1"></i>Nuevo
                    </button>
                </div>
                <select name="proveedor_id" id="proveedor_id" class="form-control selectpicker show-tick" data-live-search="true" title="Seleccione proveedor" data-size="7">
                    @foreach ($proveedores as $item)
                        <option value="{{ $item->id }}" @selected((string) old('proveedor_id') === (string) $item->id)>
                            {{ $item->persona?->nombre_completo ?? $item->persona?->razon_social ?? 'Proveedor' }}
                            — {{ $item->persona?->documento?->codigo ?? 'DOC' }} {{ $item->persona?->numero_documento ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('proveedor_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="comprobante_id" class="form-label fw-medium text-secondary small">Comprobante</label>
                <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker show-tick" data-live-search="true" title="Seleccione comprobante" data-size="6">
                    <option value="">Sin comprobante</option>
                    @foreach ($comprobantes as $item)
                        <option value="{{ $item->id }}" @selected((string) old('comprobante_id', $defaultComprobanteId) === (string) $item->id)>
                            {{ $item->tipo_comprobante }} - {{ $item->serie }}
                        </option>
                    @endforeach
                </select>
                @error('comprobante_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-medium text-secondary small">Fecha de Emisión</label>
                <input readonly type="text" class="form-control bg-light" value="{{ date('d/m/Y') }}">
                <input type="hidden" name="fecha_emision" value="{{ old('fecha_emision', now()->toDateTimeString()) }}">
            </div>

            <div class="col-12 col-md-6">
                <label for="moneda" class="form-label fw-medium text-secondary small">Moneda</label>
                <select name="moneda" id="moneda" class="form-select">
                    <option value="PEN" @selected($defaultMoneda === 'PEN')>Soles (PEN)</option>
                    <option value="USD" @selected($defaultMoneda === 'USD')>Dólares (USD)</option>
                </select>
                @error('moneda') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="metodo_pago" class="form-label fw-medium text-secondary small">Método de Pago <span class="text-danger">*</span></label>
                <select name="metodo_pago" id="metodo_pago" class="form-select">
                    <option value="EFECTIVO" @selected($defaultMetodoPago === 'EFECTIVO')>EFECTIVO</option>
                    <option value="TARJETA" @selected($defaultMetodoPago === 'TARJETA')>TARJETA</option>
                    <option value="TRANSFERENCIA" @selected($defaultMetodoPago === 'TRANSFERENCIA')>TRANSFERENCIA</option>
                    <option value="CREDITO" @selected($defaultMetodoPago === 'CREDITO')>CRÉDITO</option>
                    <option value="MIXTO" @selected($defaultMetodoPago === 'MIXTO')>MIXTO (Múltiples pagos)</option>
                </select>
                @error('metodo_pago') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label for="fecha_vencimiento" class="form-label fw-medium text-secondary small">Fecha de Vencimiento (Solo Crédito)</label>
                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
                @error('fecha_vencimiento') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Sección Pagos Mixtos -->
            <div class="col-12">
                <div id="pagoMultipleSection" class="border rounded-3 p-3 bg-light" style="display:none;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <strong class="small text-dark">Detalle de Pagos</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPaymentRow">
                            <i class="fa-solid fa-plus me-1"></i>Agregar pago
                        </button>
                    </div>

                    <div id="paymentRowsContainer"></div>

                    <div class="card border mt-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Total a Pagar:</span>
                                <strong class="text-dark">S/ <span id="compraTotalResumen">0.00</span></strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Total Abonado:</span>
                                <strong class="text-success">S/ <span id="pagoTotalRegistrado">0.00</span></strong>
                            </div>
                            <div class="d-flex justify-content-between small border-top pt-2 mt-2">
                                <span class="fw-bold">Saldo Pendiente:</span>
                                <strong class="text-danger fw-bold fs-6">S/ <span id="pagoSaldoPendiente">0.00</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
                @error('pagos') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label for="observacion" class="form-label fw-medium text-secondary small">Observación General</label>
                <textarea name="observacion" id="observacion" rows="2" class="form-control" placeholder="Anotaciones adicionales de la compra">{{ old('observacion') }}</textarea>
                @error('observacion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <div class="card bg-light border-0 p-3 mt-2">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="actualizar_precio_venta" name="actualizar_precio_venta" value="1" @checked(old('actualizar_precio_venta'))>
                        <label for="actualizar_precio_venta" class="form-check-label fw-medium text-dark small">
                            Actualizar precio de venta en el catálogo
                        </label>
                    </div>
                    <div class="ps-4">
                        <input type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" class="form-control form-control-sm w-100" placeholder="Nuevo precio de venta" value="{{ old('precio_venta') }}" @disabled(!old('actualizar_precio_venta'))>
                        @error('precio_venta') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white border-top p-3 p-md-4">
        <div class="row g-2">
            <div class="col-12 col-md-6 order-2 order-md-1">
                <button id="cancelar" type="button" class="btn btn-light w-100 py-2 text-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-times me-2"></i>Cancelar Compra
                </button>
            </div>
            <div class="col-12 col-md-6 order-1 order-md-2">
                <button type="submit" class="btn btn-success w-100 py-2 shadow-sm fw-bold" id="guardar">
                    <i class="fas fa-check-circle me-2"></i>Procesar Compra
                </button>
            </div>
        </div>
    </div>
</div>