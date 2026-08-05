@extends('layouts.app')
@section('title', 'Realizar Venta')

@push('css')
<style>
    .scan-success { animation: flashGreen 0.6s ease-out; }
    @keyframes flashGreen {
        0% { background-color: #d1e7dd; }
        100% { background-color: transparent; }
    }
    .scanner-bg-icon i { font-size: 4rem; }
    .scanner-input { font-size: 1.75rem; }
    @media (max-width: 992px) {
        .scanner-bg-icon i { font-size: 3rem; }
        .scanner-input { font-size: 1.35rem; }
    }
    @media (max-width: 576px) {
        .scanner-input { font-size: 1rem; padding: 0.75rem; }
        .scanner-bg-icon i { font-size: 2rem; }
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
                    'codigo_producto' => $v->producto->codigo,
                    'codigo_variante' => $v->codigo_variante,
                    'talla' => $v->talla?->nombre ?? 'Sin talla',
                    'afecto_igv' => (bool) ($v->producto->afecto_igv ?? true),
                ];
            })
            ->values();
    @endphp

    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="mb-4">
            <h2 class="fw-bolder text-dark mb-0 fs-3">Registrar Venta</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" class="text-decoration-none text-muted">Ventas</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Nueva Venta de Mostrador</li>
            </ol>
        </div>

        @include('layouts.partials.alert')

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-4 mb-4 border-0 border-start border-4 border-danger">
                <div class="d-flex align-items-start gap-3">
                    <div class="fs-4 text-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <div class="fw-bold mb-1 text-danger">No se pudo procesar la venta:</div>
                        <ul class="mb-0 ps-3 text-dark">
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
                                <div class="accordion-body bg-light border-top p-0">
                                    @include('venta.partials.buscador_producto')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-0">
                            @include('venta.partials.detalle')
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    @include('venta.partials.pagos')
                </div>
            </div>
        </form>

        <!-- Modal Confirmación de Cancelar Venta -->
        <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center pb-4">
                        <div class="text-danger mb-3"><i class="fas fa-trash-alt fa-4x opacity-75"></i></div>
                        <h4 class="fw-bold text-dark">¿Cancelar la venta?</h4>
                        <p class="text-muted">Se vaciará la lista de productos y se perderá la información ingresada.</p>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border" data-bs-dismiss="modal">Volver</button>
                        <button id="btnCancelarVenta" type="button" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Sí, cancelar todo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('cliente.partials.quick-create-modal')
@endsection

@push('js')
    <script>
        const variantData = @json($variantData, JSON_UNESCAPED_UNICODE);
        const oldDetails = @json(old('detalles', []), JSON_UNESCAPED_UNICODE);

        let lineItems = [];

        $(document).ready(function() {
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
                        codigo_variante: meta.codigo_variante ?? '',
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
            const scannerContainer = document.getElementById('scanner-container');
            const scannerStatus = document.getElementById('scanner-status-text');

            $inputEscaner.on('focus', function() {
                if(scannerContainer && scannerStatus) {
                    scannerContainer.style.backgroundColor = '#e0f2fe';
                    scannerContainer.style.borderColor = '#3b82f6';
                    scannerStatus.innerText = "Escáner Activo y Escuchando";
                    scannerStatus.parentElement.classList.replace('text-danger', 'text-success');
                }
            }).on('blur', function() {
                 if(scannerContainer && scannerStatus) {
                    scannerContainer.style.backgroundColor = '#f8fafc';
                    scannerStatus.innerText = "Clic aquí para volver a escanear";
                    scannerStatus.parentElement.classList.replace('text-success', 'text-danger');
                 }
            });

            $(document).on('keypress', function(e) {
                const activeEl = document.activeElement;
                if (activeEl && (
                    activeEl.tagName === 'INPUT' || 
                    activeEl.tagName === 'TEXTAREA' || 
                    activeEl.classList.contains('bs-searchbox') ||
                    activeEl.type === 'number'
                ) && activeEl.id !== 'codigo_escaner' && activeEl.type !== 'hidden') {
                    return; 
                }
                
                if (activeEl !== $inputEscaner[0] && e.key.length === 1) {
                    $inputEscaner.focus();
                }
            });

            function recibirCodigo(codigo) {
                codigo = String(codigo || '').trim();
                if (!codigo) return;

                $inputEscaner.val(codigo);
                procesarCodigoEscaneado(codigo);
                $inputEscaner.val('').focus();
            }

            $inputEscaner.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    recibirCodigo($(this).val());
                }
            });

            if (window.Echo) {
                window.Echo.channel('pos-scanner-channel')
                    .listen('BarcodeScanned', (e) => {
                        $inputEscaner.addClass('scan-success');
                        setTimeout(() => $inputEscaner.removeClass('scan-success'), 600);
                        recibirCodigo(e.codigo);
                    });
            }

            $(document).on('click', function(e) {
                const noForzarFoco = $(e.target).closest(
                    'input, select, textarea, button, a, .bootstrap-select, .modal').length;
                if (!noForzarFoco) {
                    $inputEscaner.focus();
                }
            });

            $('#variante_id').on('change', mostrarValores);
            $('#btn_agregar').on('click', agregarProducto);
            $('#btnCancelarVenta').on('click', cancelarVenta);
            $(document).on('click', '.btn-eliminar', function () {
                eliminarProducto($(this).data('index'));
            });

            $(document).on('click', '.btn-plus', function () {
                const idx = $(this).data('index');
                const item = lineItems[idx];
                
                if (item.cantidad < item.stock) {
                    item.cantidad++;
                    renderRows();
                } else {
                    showToast(`Stock máximo alcanzado (${item.stock} disp.)`, 'warning');
                }
            });

            $(document).on('click', '.btn-minus', function () {
                const idx = $(this).data('index');
                const item = lineItems[idx];
                
                if (item.cantidad > 1) {
                    item.cantidad--;
                    renderRows();
                }
            });

            // ==========================================
            // CREACIÓN RÁPIDA DE CLIENTE AJAX
            // ==========================================
            const $quickClienteForm = $('#quickClienteModal form');
            if ($quickClienteForm.length > 0) {
                $quickClienteForm.on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const $btnSubmit = $form.find('button[type="submit"]');
                    const originalBtnText = $btnSubmit.html();

                    $btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        headers: { 'Accept': 'application/json' },
                        success: function(response) {
                            const data = response.cliente;
                            const $select = $('#cliente_id');
                            const text = `${data.label} — ${data.documento || 'DOC'} ${data.numero_documento}`;

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

                            setTimeout(() => { $('#codigo_escaner').focus(); }, 300);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors || {};
                                let errorMsg = Object.values(errors).map(err => err.join('<br>')).join('<br>');
                                if (!errorMsg && xhr.responseJSON.error) errorMsg = xhr.responseJSON.error;
                                Swal.fire({ icon: 'error', title: 'Verifica los datos', html: errorMsg });
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

            // ==========================================
            //  NÚCLEO DE PROCESAMIENTO DEL ESCÁNER
            // ==========================================
            function procesarCodigoEscaneado(codigo) {
                const codigoBuscado = String(codigo).trim().toUpperCase();
                const meta = variantData.find(v => String(v.codigo_variante).toUpperCase() === codigoBuscado);

                if (!meta) {
                    showToast('El producto no está registrado en el sistema.', 'error');
                    reproducirSonido('error');
                    return;
                }

                const idVariante = meta.id;
                const stock = meta.stock;
                const precioUnitario = meta.precio_venta;

                if (stock <= 0) {
                    showToast('Stock agotado para: ' + meta.producto, 'error');
                    reproducirSonido('error');
                    return;
                }

                const existingIndex = lineItems.findIndex(item => Number(item.producto_variante_id) === idVariante);

                if (existingIndex !== -1) {
                    const current = lineItems[existingIndex];
                    if ((current.cantidad + 1) > stock) {
                        showToast(`Solo quedan ${stock} unidades de este producto.`, 'error');
                        reproducirSonido('error');
                        return;
                    }
                    current.cantidad += 1;
                    showToast('+1 agregado (' + meta.producto + ')', 'success');
                } else {
                    lineItems.push({
                        producto_variante_id: idVariante,
                        cantidad: 1,
                        precio_unitario: precioUnitario,
                        descuento: 0,
                        producto: meta.producto,
                        codigo_variante: meta.codigo_variante,
                        codigo_producto: meta.codigo_producto,
                        talla: meta.talla,
                        stock: stock,
                        afecto_igv: meta.afecto_igv
                    });
                    showToast('Producto agregado (' + meta.talla + ')', 'success');
                }

                reproducirSonido('success');
                renderRows();

                const $filaAfectada = existingIndex !== -1 ? $(`#tabla_detalle tbody tr:eq(${existingIndex})`) : $('#tabla_detalle tbody tr:last');
                $filaAfectada.addClass('scan-success');
                setTimeout(() => $filaAfectada.removeClass('scan-success'), 600);
            }

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
                    // Fallback silencioso si el navegador bloquea audio automático
                }
            }

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
                    $('#btn_agregar').prop('disabled', true); 
                    return;
                }

                const stock = Number($option.data('stock') ?? meta?.stock ?? 0);
                const precioVenta = Number($option.data('precio') ?? meta?.precio_venta ?? 0);

                $('#stock').val(stock);
                $('#precio_venta').val(precioVenta.toFixed(2));
                $('#variante_resumen').html(`<strong>${meta?.producto ?? $option.data('producto')}</strong> · ${meta?.talla ?? $option.data('talla')}`);
                $('#btn_agregar').prop('disabled', false); 
            }

            function agregarProducto() {
                const idVariante = Number($('#variante_id').val());
                if (!idVariante) {
                    showToast('Seleccione un producto manual', 'error');
                    return;
                }

                const $option = $('#variante_id option:selected');
                const meta = getVariantMeta(idVariante);
                const stock = Number($option.data('stock') ?? meta?.stock ?? 0);
                const precioUnitario = Number($('#precio_venta').val()) || Number($option.data('precio') ?? meta?.precio_venta ?? 0);
                const producto = meta?.producto || $option.data('producto') || 'Producto';
                const codigoProducto = meta?.codigo_producto || $option.data('codigo-producto') || '';
                const codigoVariante = meta?.codigo_variante || $option.data('codigo-variante') || '';
                const talla = meta?.talla || $option.data('talla') || 'Sin talla';
                const afectoIgv = parseBoolean(meta?.afecto_igv ?? true);
                const cantidad = Number($('#cantidad').val());
                const descuento = Number($('#descuento').val()) || 0;

                if (!Number.isInteger(cantidad) || cantidad <= 0) { showToast('Cantidad inválida', 'error'); return; }
                if (cantidad > stock) { showToast('Supera stock disponible', 'error'); return; }
                if (precioUnitario <= 0) { showToast('Precio inválido', 'error'); return; }
                if (descuento < 0) { showToast('Descuento inválido', 'error'); return; }

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
                        producto_variante_id: idVariante, cantidad, precio_unitario: precioUnitario,
                        descuento, producto, codigo_producto: codigoProducto, codigo_variante: codigoVariante,
                        talla, stock, afecto_igv: afectoIgv
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
                    $tbody.html(`
                        <tr id="empty-cart-row">
                            <td colspan="5" class="py-5">
                                <div class="text-center p-4">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-cart-shopping text-secondary opacity-25" style="font-size:4rem"></i>
                                    </div>
                                    <h5 class="fw-semibold text-secondary mb-1">El carrito está vacío</h5>
                                    <p class="text-muted mb-0 small">Escanea un código de barras o utiliza el buscador para comenzar la venta.</p>
                                </div>
                            </td>
                        </tr>
                    `);
                    updateTotals();
                    toggleActions();
                    return;
                }

                lineItems.forEach((item, index) => {
                    const totalLinea = Math.max(0, round((item.cantidad * item.precio_unitario) - item.descuento));
                    
                    const row = `
                    <tr>
                        <td class="align-middle">
                            <input type="hidden" name="detalles[${index}][producto_variante_id]" value="${item.producto_variante_id}">
                            <input type="hidden" name="detalles[${index}][cantidad]" value="${item.cantidad}">
                            <input type="hidden" name="detalles[${index}][precio_unitario]" value="${item.precio_unitario}">
                            <input type="hidden" name="detalles[${index}][descuento]" value="${item.descuento}">
                            
                            <div class="fw-bold text-dark mb-1">${item.producto}</div>
                            <div class="d-flex align-items-center gap-2 small text-muted">
                                <span class="badge bg-light text-dark border">${item.talla}</span>
                                <span class="font-monospace">${item.codigo_producto}</span>
                            </div>
                        </td>
                        <td class="align-middle text-center">
                            <div class="input-group input-group-sm mx-auto shadow-sm" style="max-width: 110px;">
                                <button class="btn btn-light border text-secondary btn-minus" type="button" data-index="${index}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="text" class="form-control text-center bg-white border-light fw-bold px-0" value="${item.cantidad}" readonly>
                                <button class="btn btn-light border text-secondary btn-plus" type="button" data-index="${index}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </td>
                        <td class="align-middle text-end">
                            <div class="font-monospace text-dark">S/ ${item.precio_unitario.toFixed(2)}</div>
                            ${item.descuento > 0 ? `<div class="small text-danger font-monospace" title="Descuento aplicado">-S/ ${item.descuento.toFixed(2)}</div>` : ''}
                        </td>
                        <td class="align-middle text-end fw-bold text-primary font-monospace fs-6">
                            S/ ${totalLinea.toFixed(2)}
                        </td>
                        <td class="align-middle text-center">
                            <button class="btn btn-sm btn-outline-danger border-0 btn-eliminar" data-index="${index}" type="button" title="Quitar del carrito">
                                <i class="fa-solid fa-trash-alt"></i>
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
                renderRows();
                limpiarCampos();
                
                if (typeof window.syncPaymentTotals === 'function') {
                    window.syncPaymentTotals();
                }

                showToast('Venta limpiada', 'success');
                $('#codigo_escaner').focus();
            }

            function updateTotals() {
                let baseImponibleTotal = 0;
                let igvTotal = 0;
                let descuentoTotal = 0;
                let totalFinalPagar = 0;

                lineItems.forEach(item => {
                    const totalLinea = Math.max(0, (Number(item.cantidad) * Number(item.precio_unitario)) - Number(item.descuento));
                    descuentoTotal += Number(item.descuento);
                    totalFinalPagar += totalLinea;

                    if (item.afecto_igv) {
                        const base = totalLinea / 1.18;
                        baseImponibleTotal += base;
                        igvTotal += (totalLinea - base);
                    } else {
                        baseImponibleTotal += totalLinea;
                    }
                });

                $('#subtotal_bruto').text(baseImponibleTotal.toFixed(2));
                $('#descuento_total').text(descuentoTotal.toFixed(2));
                $('#igv').text(igvTotal.toFixed(2));
                $('#total').text(totalFinalPagar.toFixed(2));

                $('#inputSubtotal').val(baseImponibleTotal.toFixed(2));
                $('#inputDescuentoTotal').val(descuentoTotal.toFixed(2));
                $('#inputIgvTotal').val(igvTotal.toFixed(2));
                $('#inputTotal').val(totalFinalPagar.toFixed(2));
                $('#ventaTotalResumen').text(totalFinalPagar.toFixed(2));
                $('#cantidadItems').text(lineItems.length);

                if (typeof window.syncPaymentTotals === 'function') {
                    window.syncPaymentTotals();
                }
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
                $('#btn_agregar').prop('disabled', true); 
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
        });
    </script>
@endpush