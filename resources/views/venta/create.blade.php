@extends('layouts.app')

@section('title', 'Realizar Venta')

@push('css')
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <style>
        .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
        .fs-7 { font-size: 0.875rem; }
        .help-text-soft { font-size: 0.8rem; color: #6c757d; }
        .sale-alert { border-left: 4px solid #0d6efd; }
        .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; white-space: nowrap; }
        .table-custom td { vertical-align: middle; }
        .compact-note { font-size: 0.8rem; color: #6c757d; line-height: 1.35; }
        .scan-success { animation: flashGreen 0.6s ease-out; }
        @keyframes flashGreen {
            0% { background-color: #d1e7dd; }
            100% { background-color: transparent; }
        }
        .scanner-bg-icon i { font-size: 4rem; }
        .scanner-input { font-size: 1.75rem; }
        .scanner-title { font-size: 1rem; }
        @media (max-width: 992px) {
            .scanner-bg-icon i { font-size: 3rem; }
            .scanner-input { font-size: 1.35rem; }
        }
        @media (max-width: 576px) {
            .scanner-body { padding: 1rem !important; }
            .scanner-title { font-size: 0.95rem; }
            .scanner-description { font-size: 0.8rem; line-height: 1.4; }
            .scanner-group { font-size: 0.9rem; }
            .scanner-input { font-size: 1rem; padding: 0.75rem; }
            .scanner-bg-icon i { font-size: 2rem; }
            #scanner-indicator { font-size: 0.75rem; }
        }
    </style>
@endpush

@section('content')
    @php
        $defaultComprobanteId = old('comprobante_id', optional($comprobantes->first())->id);

        $variantData = $variantes
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'stock' => (int) $v->stock_actual,
                    'precio_venta' => (float) ($v->producto->precio_venta ?? 0),
                    'producto' => $v->producto->nombre,
                    'codigo' => $v->producto->codigo,
                    'codigo_barra' => $v->producto->codigo_barra,
                    'talla' => $v->talla?->nombre ?? 'Sin talla',
                    'afecto_igv' => (bool) ($v->producto->afecto_igv ?? true),
                ];
            })
            ->values();
    @endphp

    <div class="container-fluid px-4 py-4">
        <div class="mb-4">
            <h2 class="page-title mb-0">Registrar Venta</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}"
                        class="text-decoration-none text-muted">Ventas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Nueva Venta de Mostrador</li>
            </ol>
        </div>

        @include('layouts.partials.alert')

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-4 mb-4 border-0"
                style="border-left: 4px solid #dc3545 !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="fs-4 text-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <div class="fw-bold mb-1">No se pudo procesar la venta:</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('ventas.store') }}" method="post" id="formVenta">
            @csrf
            <div class="row g-4">
                <div class="col-xl-8">

                    @include('venta.partials.escanner')

                    <div class="accordion mb-4" id="accordionBuscador">
                        <div class="accordion-item border-0 shadow-sm rounded-4 overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-white fw-bold text-dark" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseBuscador" aria-expanded="false">
                                    <i class="fas fa-search me-2 text-secondary"></i> Falla el código / Búsqueda Manual
                                </button>
                            </h2>
                            <div id="collapseBuscador" class="accordion-collapse collapse"
                                data-bs-parent="#accordionBuscador">
                                <div class="accordion-body bg-light border-top">
                                    @include('venta.partials.buscador_producto')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            @include('venta.partials.detalle')
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    @include('venta.partials.pagos')
                </div>
            </div>
        </form>

        <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center pb-4">
                        <div class="text-danger mb-3"><i class="fas fa-trash-alt fa-4x"></i></div>
                        <h4 class="fw-bold text-dark">¿Cancelar la venta?</h4>
                        <p class="text-muted">Se vaciará la lista de productos y se perderá la información ingresada.</p>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Volver</button>
                        <button id="btnCancelarVenta" type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Sí,
                            cancelar todo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('cliente.partials.quick-create-modal')
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const variantData = @json($variantData, JSON_UNESCAPED_UNICODE);
        const oldDetails = @json(old('detalles', []), JSON_UNESCAPED_UNICODE);
        const oldPayments = @json(old('pagos', []), JSON_UNESCAPED_UNICODE);
        const initialMetodoPago = @json(old('metodo_pago', 'EFECTIVO'));

        let lineItems = [];
        let paymentItems = [];

        $(document).ready(function() {
            // 1. Reconstrucción en caso de recarga por error de validación (Old data)
            if (Array.isArray(oldDetails) && oldDetails.length > 0) {
                lineItems = oldDetails.map((detail) => {
                    const variantId = Number(detail.producto_variante_id);
                    const meta = variantData.find(v => Number(v.id) === variantId) || {};
                    return {
                        producto_variante_id: variantId,
                        cantidad: Number(detail.cantidad ?? 1),
                        precio_unitario: Number(detail.precio_unitario ?? meta.precio_venta ?? 0),
                        descuento: Number(detail.descuento ?? 0),
                        producto: meta.producto ?? 'Producto',
                        codigo: meta.codigo ?? '',
                        talla: meta.talla ?? 'Sin talla',
                        stock: meta.stock ?? 0,
                        afecto_igv: !!meta.afecto_igv
                    };
                });
                renderRows();
            } else {
                updateTotals();
            }

            // ==========================================
            //  LÓGICA DEL ESCÁNER
            // ==========================================

            const $inputEscaner = $('#codigo_escaner');
            const $indicador = $('#scanner-indicator');

            function recibirCodigo(codigo) {
                codigo = String(codigo || '').trim();
                if (!codigo) return;

                $inputEscaner.val(codigo);
                procesarCodigoEscaneado(codigo);
                $inputEscaner.val('').focus();
            }

            // Si alguien escribe o un lector físico de pistola manda Enter
            $inputEscaner.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    recibirCodigo($(this).val());
                }
            });

            // ESCUCHA DEL EVENTO WEBSOCKETS (CANAL PÚBLICO)
            if (window.Echo) {
                window.Echo.channel('pos-scanner-channel')
                    .listen('BarcodeScanned', (e) => {
                        console.log('Código recibido vía WebSockets:', e.codigo);
                        // Destello visual en el input para indicar recepción
                        $inputEscaner.addClass('scan-success');
                        setTimeout(() => $inputEscaner.removeClass('scan-success'), 600);
                        
                        recibirCodigo(e.codigo);
                    });
            } else {
                console.warn('Laravel Echo no está configurado. ¿Ejecutaste npm run dev/build?');
            }

            // UX: Mantener siempre el foco en el escáner si el usuario hace clic fuera de otras áreas vitales
            $(document).on('click', function(e) {
                const noForzarFoco = $(e.target).closest(
                    'input, select, textarea, button, a, .bootstrap-select, .modal').length;
                if (!noForzarFoco) {
                    $inputEscaner.focus();
                }
            });

            $inputEscaner.on('focus', function() {
                $indicador.html(
                    '<i class="fas fa-circle-notch fa-spin me-1"></i> Cursor activo. Listo para escanear.'
                ).removeClass('text-danger').addClass('text-success');
            }).on('blur', function() {
                $indicador.html(
                    '<i class="fas fa-exclamation-circle me-1"></i> Cursor inactivo. Haz clic arriba para escanear.'
                ).removeClass('text-success').addClass('text-danger');
            });

            $('#variante_id').on('change', mostrarValores);
            $('#btn_agregar').on('click', agregarProducto);
            $('#btnCancelarVenta').on('click', cancelarVenta);

            if (Array.isArray(oldPayments) && oldPayments.length > 0) {
                paymentItems = oldPayments.map((pago) => ({
                    metodo_pago: (pago.metodo_pago ?? 'EFECTIVO').toString().toUpperCase(),
                    monto: Number(pago.monto ?? 0),
                    referencia_operacion: pago.referencia_operacion ?? '',
                    observacion: pago.observacion ?? '',
                }));
            } else if (initialMetodoPago === 'MIXTO') {
                paymentItems = [{
                    metodo_pago: 'EFECTIVO',
                    monto: Number($('#inputTotal').val() || 0),
                    referencia_operacion: '',
                    observacion: ''
                }];
            }
            renderPaymentRows();
            updatePaymentSummary();
            updateMetodoPagoUI();

            $('#metodo_pago').on('change', function() {
                updateMetodoPagoUI();
                if ($(this).val() !== 'MIXTO') {
                    paymentItems = [];
                    renderPaymentRows();
                    updatePaymentSummary();
                } else if (paymentItems.length === 0) {
                    addPaymentRow(false);
                }
            });

            $('#btnAddPaymentRow').on('click', function() {
                addPaymentRow(true);
            });
            $('#paymentRowsContainer').on('input change', 'input, select', function() {
                updatePaymentSummary();
            });

            // Creación Rápida de Cliente AJAX
            const $quickClienteForm = $('#quickClienteModal form');
            if ($quickClienteForm.length > 0) {
                $quickClienteForm.on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $btnSubmit = $form.find('button[type="submit"]');
                    const originalBtnText = $btnSubmit.html();

                    $btnSubmit.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        headers: {
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            const data = response.cliente;
                            const $select = $('#cliente_id');
                            const text =
                                `${data.label} — ${data.documento || 'DOC'} ${data.numero_documento}`;

                            $select.selectpicker('destroy');
                            if ($select.find(`option[value="${data.id}"]`).length === 0) {
                                $select.append($('<option>', {
                                    value: data.id,
                                    text: text,
                                    'data-tipo-persona': data.tipo_persona,
                                    'data-doc-codigo': data.documento,
                                    'data-doc-numero': data.numero_documento
                                }));
                            }

                            $select.find('option').prop('selected', false);
                            $select.find(`option[value="${data.id}"]`).prop('selected', true);
                            $select.val(data.id);
                            $select.selectpicker();
                            $select[0].dispatchEvent(new Event('change'));
                            $('#quickClienteModal').modal('hide');
                            $form[0].reset();
                            showToast(response.message, 'success');

                            setTimeout(() => {
                                $('#codigo_escaner').focus();
                            }, 300);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors || {};
                                let errorMsg = Object.values(errors).map(err => err.join(
                                    '<br>')).join('<br>');
                                if (!errorMsg && xhr.responseJSON.error) errorMsg = xhr
                                    .responseJSON.error;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Verifica los datos',
                                    html: errorMsg
                                });
                            } else {
                                showToast('Ocurrió un error al guardar', 'error');
                            }
                        },
                        complete: function() {
                            $btnSubmit.prop('disabled', false).html(originalBtnText);
                        }
                    });
                });
            }

            mostrarValores();
        });

        // ==========================================
        //  NÚCLEO DE PROCESAMIENTO DEL ESCÁNER
        // ==========================================
        function procesarCodigoEscaneado(codigo) {
            // 1. Buscar coincidencia por código interno SKU
            const meta = variantData.find(v =>
                (v.codigo && v.codigo.toUpperCase() === codigo.toUpperCase()) || 
                (v.codigo_barra && v.codigo_barra.toUpperCase() === codigo.toUpperCase())
            );

            // 2. Validación de existencia
            if (!meta) {
                showToast('El producto no está registrado en el sistema.', 'error');
                reproducirSonido('error');
                return;
            }

            const idVariante = meta.id;
            const stock = meta.stock;
            const precioUnitario = meta.precio_venta;

            // 3. Validación de Stock
            if (stock <= 0) {
                showToast('Stock agotado para: ' + meta.producto, 'error');
                reproducirSonido('error');
                return;
            }

            // 4. Lógica de inserción en carrito
            const existingIndex = lineItems.findIndex(item => Number(item.producto_variante_id) === idVariante);

            if (existingIndex !== -1) {
                // Ya existe en la lista: Aumenta la cantidad
                const current = lineItems[existingIndex];
                if ((current.cantidad + 1) > stock) {
                    showToast(`Solo quedan ${stock} unidades de este producto.`, 'error');
                    reproducirSonido('error');
                    return;
                }
                current.cantidad += 1;
                showToast('+1 agregado (' + meta.producto + ')', 'success');
            } else {
                // Nuevo en la lista: Se agrega
                lineItems.push({
                    producto_variante_id: idVariante,
                    cantidad: 1,
                    precio_unitario: precioUnitario,
                    descuento: 0,
                    producto: meta.producto,
                    codigo: meta.codigo,
                    talla: meta.talla,
                    stock: stock,
                    afecto_igv: meta.afecto_igv
                });
                showToast('Producto agregado (' + meta.talla + ')', 'success');
            }

            // 5. Renderizar, animar fila y emitir sonido Beep
            reproducirSonido('success');
            renderRows();

            // Destello visual en la última fila agregada/modificada
            const $filaAfectada = existingIndex !== -1 ? $(`#tabla_detalle tbody tr:eq(${existingIndex})`) : $(
                '#tabla_detalle tbody tr:last');
            $filaAfectada.addClass('scan-success');
            setTimeout(() => {
                $filaAfectada.removeClass('scan-success');
            }, 600);
        }

        // Pequeño Beep sintético
        function reproducirSonido(tipo) {
            try {
                const ctx = new(window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (tipo === 'success') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(800, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0.1, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.1);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(300, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(100, ctx.currentTime + 0.2);
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.2);
                }
            } catch (e) {
                /* Fallback silencioso si el navegador bloquea audio automático */
            }
        }

        // ==========================================
        // BÚSQUEDA MANUAL Y TABLA
        // ==========================================

        function getVariantMeta(variantId) {
            return variantData.find(v => Number(v.id) === Number(variantId)) || null;
        }

        function parseBoolean(value) {
            return value === true || value === 1 || value === '1' || value === 'true';
        }

        function mostrarValores() {
            const variantId = Number($('#variante_id').val());
            const $option = $('#variante_id option:selected');
            const meta = getVariantMeta(variantId);

            if (!variantId) {
                $('#stock').val('');
                $('#precio_venta').val('');
                $('#variante_resumen').text('Seleccione un producto de la lista');
                return;
            }

            const stock = Number($option.data('stock') ?? meta?.stock ?? 0);
            const precioVenta = Number($option.data('precio') ?? meta?.precio_venta ?? 0);

            $('#stock').val(stock);
            $('#precio_venta').val(precioVenta.toFixed(2));
            $('#variante_resumen').html(
                `<strong>${meta?.producto ?? $option.data('producto')}</strong> · ${meta?.talla ?? $option.data('talla')}`
            );
        }

        // Agregar producto desde el formulario manual
        function agregarProducto() {
            const idVariante = Number($('#variante_id').val());
            if (!idVariante) {
                showToast('Seleccione un producto manual', 'error');
                return;
            }

            const $option = $('#variante_id option:selected');
            const meta = getVariantMeta(idVariante);
            const stock = Number($option.data('stock') ?? meta?.stock ?? 0);
            const precioUnitario = Number($('#precio_venta').val()) || Number($option.data('precio') ?? meta
                ?.precio_venta ?? 0);
            const producto = meta?.producto || $option.data('producto') || 'Producto';
            const codigo = meta?.codigo || $option.data('codigo') || '';
            const talla = meta?.talla || $option.data('talla') || 'Sin talla';
            const afectoIgv = parseBoolean(meta?.afecto_igv ?? true);
            const cantidad = Number($('#cantidad').val());
            const descuento = Number($('#descuento').val()) || 0;

            if (!Number.isInteger(cantidad) || cantidad <= 0) {
                showToast('Cantidad inválida', 'error');
                return;
            }
            if (cantidad > stock) {
                showToast('Supera stock disponible', 'error');
                return;
            }
            if (precioUnitario <= 0) {
                showToast('Precio inválido', 'error');
                return;
            }
            if (descuento < 0) {
                showToast('Descuento inválido', 'error');
                return;
            }

            const existingIndex = lineItems.findIndex(item => Number(item.producto_variante_id) === idVariante);

            if (existingIndex !== -1) {
                const current = lineItems[existingIndex];
                if ((Number(current.cantidad) + cantidad) > stock) {
                    showToast('Stock insuficiente para acumular', 'error');
                    return;
                }
                current.cantidad = Number(current.cantidad) + cantidad;
                current.precio_unitario = precioUnitario;
                current.descuento = round(Number(current.descuento) + descuento);
                showToast('Actualizado correctamente', 'success');
            } else {
                lineItems.push({
                    producto_variante_id: idVariante,
                    cantidad,
                    precio_unitario: precioUnitario,
                    descuento,
                    producto,
                    codigo,
                    talla,
                    stock,
                    afecto_igv: afectoIgv
                });
                showToast('Añadido desde búsqueda manual', 'success');
            }

            renderRows();
            limpiarCampos();

            $('#codigo_escaner').focus();
        }

        function renderRows() {
            const $tbody = $('#tabla_detalle tbody');
            $tbody.empty();

            if (lineItems.length === 0) {
                updateTotals();
                toggleActions();
                return;
            }

            lineItems.forEach((item, index) => {
                const base = round((item.cantidad * item.precio_unitario) - item.descuento);
                const igv = item.afecto_igv ? round(base * 0.18) : 0;
                const totalLinea = round(base + igv);

                const row = `
                <tr>
                    <th class="align-middle text-center text-muted fw-normal">${index + 1}</th>
                    <td class="align-middle fw-medium">
                        <input type="hidden" name="detalles[${index}][producto_variante_id]" value="${item.producto_variante_id}">
                        <input type="hidden" name="detalles[${index}][cantidad]" value="${item.cantidad}">
                        <input type="hidden" name="detalles[${index}][precio_unitario]" value="${item.precio_unitario}">
                        <input type="hidden" name="detalles[${index}][descuento]" value="${item.descuento}">
                        <div>${item.producto}</div>
                        <div class="small text-muted font-monospace">${item.codigo}</div>
                    </td>
                    <td class="align-middle text-center">${item.talla}</td>
                    <td class="align-middle text-center">
                        <span class="badge bg-light text-dark border fs-6">${item.cantidad}</span>
                    </td>
                    <td class="align-middle text-end font-monospace">${item.precio_unitario.toFixed(2)}</td>
                    <td class="align-middle text-end text-danger font-monospace">${item.descuento > 0 ? '-S/ ' + item.descuento.toFixed(2) : 'S/ 0.00'}</td>
                    <td class="align-middle text-end font-monospace">${igv.toFixed(2)}</td>
                    <td class="align-middle text-end fw-bold text-dark fs-6 font-monospace">${totalLinea.toFixed(2)}</td>
                    <td class="align-middle text-center">
                        <button class="btn btn-sm btn-outline-danger border-0" type="button" onclick="eliminarProducto(${index})" title="Quitar">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
                $tbody.append(row);
            });

            updateTotals();
            toggleActions();
        }

        function eliminarProducto(indice) {
            lineItems.splice(indice, 1);
            renderRows();
            showToast('Producto eliminado del carrito', 'success');
            $('#codigo_escaner').focus();
        }

        function cancelarVenta() {
            lineItems = [];
            paymentItems = [];
            renderRows();
            renderPaymentRows();
            limpiarCampos();
            updatePaymentSummary();
            showToast('Venta limpiada', 'success');
            $('#codigo_escaner').focus();
        }

        function updateTotals() {
            const subtotalBruto = lineItems.reduce((acc, item) => acc + (Number(item.cantidad) * Number(item
                .precio_unitario)), 0);
            const descuentoTotal = lineItems.reduce((acc, item) => acc + Number(item.descuento), 0);
            const baseImponible = Math.max(0, subtotalBruto - descuentoTotal);
            const igv = lineItems.reduce((acc, item) => {
                const base = Math.max(0, (Number(item.cantidad) * Number(item.precio_unitario)) - Number(item
                    .descuento));
                return acc + (item.afecto_igv ? (base * 0.18) : 0);
            }, 0);
            const total = round(baseImponible + igv);

            $('#subtotal_bruto').text(subtotalBruto.toFixed(2));
            $('#descuento_total').text(descuentoTotal.toFixed(2));
            $('#igv').text(igv.toFixed(2));
            $('#total').text(total.toFixed(2));

            $('#inputSubtotal').val(subtotalBruto.toFixed(2));
            $('#inputDescuentoTotal').val(descuentoTotal.toFixed(2));
            $('#inputIgvTotal').val(igv.toFixed(2));
            $('#inputTotal').val(total.toFixed(2));
            $('#ventaTotalResumen').text(total.toFixed(2));

            updatePaymentSummary();
        }

        function toggleActions() {
            if (lineItems.length === 0) {
                $('#guardar').prop('disabled', true).addClass('disabled');
                $('#cancelar').prop('disabled', true).addClass('disabled');
            } else {
                $('#guardar').prop('disabled', false).removeClass('disabled');
                $('#cancelar').prop('disabled', false).removeClass('disabled');
            }
        }

        function limpiarCampos() {
            $('#variante_id').selectpicker('val', '');
            $('#cantidad').val('1');
            $('#precio_venta').val('');
            $('#descuento').val('0');
            $('#stock').val('');
            $('#variante_resumen').text('Seleccione un producto manual');
        }

        function round(num, decimales = 2) {
            return Number(parseFloat(num).toFixed(decimales));
        }

        function showToast(message, icon = 'error') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                icon: icon,
                title: message
            });
        }

        // ==========================================
        // PAGOS MULTIPLES
        // ==========================================
        function updateMetodoPagoUI() {
            const isMixto = String($('#metodo_pago').val() || '').toUpperCase() === 'MIXTO';
            $('#pagoMultipleSection').toggle(isMixto);
            $('#btnAddPaymentRow').prop('disabled', !isMixto);
            if (!isMixto) $('#paymentRowsContainer').find('input, select, textarea').prop('disabled', false);
        }

        function getPaymentRowsFromDom() {
            const rows = [];
            $('#paymentRowsContainer .payment-row').each(function() {
                const $row = $(this);
                rows.push({
                    metodo_pago: String($row.find('[name*="[metodo_pago]"]').val() || '').toUpperCase(),
                    monto: Number($row.find('[name*="[monto]"]').val() || 0),
                    referencia_operacion: $row.find('[name*="[referencia_operacion]"]').val() || '',
                    observacion: $row.find('[name*="[observacion]"]').val() || '',
                });
            });
            return rows;
        }

        function addPaymentRow(autofocus = true) {
            paymentItems = getPaymentRowsFromDom();
            paymentItems.push({
                metodo_pago: 'EFECTIVO',
                monto: Number($('#inputTotal').val() || 0),
                referencia_operacion: '',
                observacion: ''
            });
            renderPaymentRows();
            if (autofocus) $('#paymentRowsContainer .payment-row:last [name$="[monto]"]').focus();
            updatePaymentSummary();
        }

        function removePaymentRow(index) {
            paymentItems = getPaymentRowsFromDom();
            paymentItems.splice(index, 1);
            renderPaymentRows();
            updatePaymentSummary();
        }

        function renderPaymentRows() {
            const $container = $('#paymentRowsContainer');
            $container.empty();
            if (!paymentItems.length) {
                $container.append(`<div class="text-muted small">No hay pagos adicionales registrados.</div>`);
                return;
            }
            paymentItems.forEach((pago, index) => {
                $container.append(`
                <div class="payment-row border rounded-3 p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="small text-dark">Pago ${index + 1}</strong>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removePaymentRow(${index})">Eliminar</button>
                    </div>
                    <div class="row g-2">
                        <div class="col-12"><label class="form-label small mb-1">Método</label><select name="pagos[${index}][metodo_pago]" class="form-select form-select-sm"><option value="EFECTIVO" ${pago.metodo_pago==='EFECTIVO'?'selected':''}>Efectivo</option><option value="TARJETA" ${pago.metodo_pago==='TARJETA'?'selected':''}>Tarjeta</option><option value="TRANSFERENCIA" ${pago.metodo_pago==='TRANSFERENCIA'?'selected':''}>Transferencia</option><option value="YAPE" ${pago.metodo_pago==='YAPE'?'selected':''}>Yape</option><option value="PLIN" ${pago.metodo_pago==='PLIN'?'selected':''}>Plin</option><option value="OTRO" ${pago.metodo_pago==='OTRO'?'selected':''}>Otro</option></select></div>
                        <div class="col-12"><label class="form-label small mb-1">Monto</label><input type="number" step="0.01" min="0.01" name="pagos[${index}][monto]" class="form-control form-control-sm" value="${Number(pago.monto||0).toFixed(2)}"></div>
                        <div class="col-12"><label class="form-label small mb-1">Referencia</label><input type="text" name="pagos[${index}][referencia_operacion]" class="form-control form-control-sm" maxlength="100" value="${escapeHtml(pago.referencia_operacion||'')}"></div>
                    </div>
                </div>
            `);
            });
        }

        function updatePaymentSummary() {
            const total = Number($('#inputTotal').val() || 0);
            const pagos = getPaymentRowsFromDom();
            const pagado = round(pagos.reduce((acc, pago) => acc + (Number(pago.monto) || 0), 0));
            const pendiente = round(Math.max(0, total - pagado));
            $('#pagoTotalProgramado').text(total.toFixed(2));
            $('#pagoTotalRegistrado').text(pagado.toFixed(2));
            $('#pagoSaldoPendiente').text(pendiente.toFixed(2));
        }

        function escapeHtml(text) {
            return String(text).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"',
                '&quot;').replaceAll("'", '&#039;');
        }

        window.removePaymentRow = removePaymentRow;
        window.updatePaymentSummary = updatePaymentSummary;
    </script>
@endpush