@php
    $defaultComprobanteId = old('comprobante_id', optional($comprobantes->first())->id);
    $defaultMetodoPago = old('metodo_pago', 'EFECTIVO');
    $defaultMoneda = old('moneda', 'PEN');
@endphp

<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
        <i class="fa-solid fa-file-invoice text-success fs-5 me-2"></i>
        <h5 class="mb-0 fw-semibold text-dark">Datos de la Compra</h5>
    </div>

    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-12">
                <label for="proveedor_id" class="form-label fw-medium text-secondary small">
                    Proveedor <span class="text-danger">*</span>
                </label>

                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                    <small class="help-text-soft">Proveedor natural o jurídico identificado.</small>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickProveedorModal">
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

            <div class="col-12">
                <label for="comprobante_id" class="form-label fw-medium text-secondary small">
                    Comprobante de Compra
                </label>
                <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker show-tick" data-live-search="true" title="Seleccione comprobante" data-size="6">
                    <option value="">Sin comprobante</option>
                    @foreach ($comprobantes as $item)
                        <option value="{{ $item->id }}" @selected((string) old('comprobante_id', $defaultComprobanteId) === (string) $item->id)>
                            {{ $item->tipo_comprobante }} - {{ $item->serie }}
                        </option>
                    @endforeach
                </select>
                @error('comprobante_id') <small class="text-danger">{{ $message }}</small> @enderror
                <small class="help-text-soft d-block mt-2">La serie y el correlativo se asignan automáticamente al guardar.</small>
            </div>

            <div class="col-12">
                <label class="form-label fw-medium text-secondary small">Fecha de Emisión</label>
                <input readonly type="text" class="form-control bg-light" value="{{ date('d/m/Y') }}">
                <input type="hidden" name="fecha_emision" value="{{ old('fecha_emision', now()->toDateTimeString()) }}">
            </div>

            <div class="col-12">
                <label for="fecha_vencimiento" class="form-label fw-medium text-secondary small">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
                @error('fecha_vencimiento') <small class="text-danger">{{ $message }}</small> @enderror
                <small class="help-text-soft d-block mt-2" id="creditoHelp">
                    Si eliges contado o transferencia, este campo no es necesario.
                </small>
            </div>

            <div class="col-12">
                <label for="moneda" class="form-label fw-medium text-secondary small">Moneda</label>
                <select name="moneda" id="moneda" class="form-select">
                    <option value="PEN" @selected($defaultMoneda === 'PEN')>PEN</option>
                    <option value="USD" @selected($defaultMoneda === 'USD')>USD</option>
                </select>
                @error('moneda') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label for="metodo_pago" class="form-label fw-medium text-secondary small">
                    Método de pago <span class="text-danger">*</span>
                </label>
                <select name="metodo_pago" id="metodo_pago" class="form-select">
                    <option value="EFECTIVO" @selected($defaultMetodoPago === 'EFECTIVO')>EFECTIVO</option>
                    <option value="TARJETA" @selected($defaultMetodoPago === 'TARJETA')>TARJETA</option>
                    <option value="TRANSFERENCIA" @selected($defaultMetodoPago === 'TRANSFERENCIA')>TRANSFERENCIA</option>
                    <option value="CREDITO" @selected($defaultMetodoPago === 'CREDITO')>CRÉDITO</option>
                    <option value="MIXTO" @selected($defaultMetodoPago === 'MIXTO')>MIXTO</option>
                </select>
                @error('metodo_pago') <small class="text-danger">{{ $message }}</small> @enderror
                <small class="help-text-soft d-block mt-2">
                    La compra a crédito no exige pago inmediato. La compra mixta permite varios pagos.
                </small>
            </div>

            <div class="col-12">
                <div id="pagoMultipleSection" class="border rounded-3 p-3 bg-white" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="small text-dark">Pagos múltiples</strong>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPaymentRow">
                            <i class="fa-solid fa-plus me-1"></i>Agregar pago
                        </button>
                    </div>

                    <div id="paymentRowsContainer"></div>

                    <div class="alert alert-light border mb-0 mt-3">
                        <div class="d-flex justify-content-between small">
                            <span>Total compra</span>
                            <strong>S/ <span id="compraTotalResumen">0.00</span></strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Total pagos</span>
                            <strong>S/ <span id="pagoTotalRegistrado">0.00</span></strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Pendiente</span>
                            <strong class="text-danger">S/ <span id="pagoSaldoPendiente">0.00</span></strong>
                        </div>
                    </div>
                </div>

                @error('pagos') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label for="observacion" class="form-label fw-medium text-secondary small">Observación</label>
                <textarea name="observacion" id="observacion" rows="3" class="form-control" placeholder="Opcional">{{ old('observacion') }}</textarea>
                @error('observacion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="actualizar_precio_venta" name="actualizar_precio_venta" value="1" @checked(old('actualizar_precio_venta'))>
                    <label for="actualizar_precio_venta" class="form-check-label">
                        Actualizar precio de venta desde esta compra
                    </label>
                </div>
                <small class="help-text-soft d-block mt-1">
                    Si activas esto, el nuevo precio se aplicará a los productos del detalle. No es magia; es una regla operativa.
                </small>
            </div>

            <div class="col-12">
                <label for="precio_venta" class="form-label fw-medium text-secondary small">Nuevo precio de venta</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="precio_venta"
                    id="precio_venta"
                    class="form-control"
                    value="{{ old('precio_venta') }}"
                    @disabled(!old('actualizar_precio_venta'))
                >
                @error('precio_venta') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="card-footer bg-white border-top border-light p-4">
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success py-2 shadow-sm fw-bold" id="guardar">
                <i class="fas fa-check-circle me-2"></i>Procesar Compra
            </button>
            <button id="cancelar" type="button" class="btn btn-light py-2 text-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#cancelModal">
                Cancelar Compra
            </button>
        </div>
    </div>
</div>