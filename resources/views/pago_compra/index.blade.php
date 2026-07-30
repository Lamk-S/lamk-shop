@extends('layouts.app')
@section('title', 'Cuentas por pagar')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bolder text-dark mb-0 fs-3">Cuentas por pagar</h2>
    </div>

    @include('layouts.partials.alert')

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('cuentas-por-pagar.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Proveedor</label>
                    <select name="proveedor_id" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" @selected(request('proveedor_id') == $proveedor->id)>
                                {{ $proveedor->persona?->numero_documento }} - {{ $proveedor->persona?->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Estado</label>
                    <select name="estado" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        <option value="PENDIENTE" @selected(request('estado') === 'PENDIENTE')>Pendiente</option>
                        <option value="PARCIAL" @selected(request('estado') === 'PARCIAL')>Parcial</option>
                        <option value="PAGADA" @selected(request('estado') === 'PAGADA')>Pagada</option>
                        <option value="ANULADA" @selected(request('estado') === 'ANULADA')>Anulada</option>
                    </select>
                </div>

                <div class="col-md-3 pb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" value="1" id="vencidas" name="vencidas" @checked(request()->boolean('vencidas'))>
                        <label class="form-check-label fw-medium text-danger" for="vencidas">Solo vencidas</label>
                    </div>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100 shadow-sm fw-medium" type="submit">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive bg-white rounded-4">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">#</th>
                            <th class="border-bottom-0">Proveedor</th>
                            <th class="border-bottom-0">Compra</th>
                            <th class="text-end border-bottom-0">Total</th>
                            <th class="text-end border-bottom-0">Pagado</th>
                            <th class="text-end border-bottom-0">Pendiente</th>
                            <th class="text-center border-bottom-0">Estado</th>
                            <th class="text-center border-bottom-0">Vencimiento</th>
                            <th class="text-center pe-4 border-bottom-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cuentas as $cuenta)
                            <tr>
                                <td class="ps-4 py-3 font-monospace text-muted">{{ $cuenta->id }}</td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                        {{ $cuenta->proveedor?->persona?->nombre_completo }}
                                    </div>
                                    <div class="small text-muted font-monospace">
                                        {{ $cuenta->proveedor?->persona?->documento?->codigo }} {{ $cuenta->proveedor?->persona?->numero_documento }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">Compra #{{ $cuenta->compra_id }}</div>
                                    <div class="small text-muted font-monospace">
                                        {{ $cuenta->compra?->serie }}-{{ $cuenta->compra?->correlativo }}
                                    </div>
                                </td>
                                <td class="text-end font-monospace">S/ {{ number_format($cuenta->total, 2) }}</td>
                                <td class="text-end text-success font-monospace">S/ {{ number_format($cuenta->monto_pagado, 2) }}</td>
                                <td class="text-end fw-bold font-monospace {{ $cuenta->saldo_pendiente > 0 ? 'text-danger' : 'text-muted' }}">
                                    S/ {{ number_format($cuenta->saldo_pendiente, 2) }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $badges = [
                                            'PAGADA' => 'bg-success',
                                            'PARCIAL' => 'bg-warning text-dark',
                                            'PENDIENTE' => 'bg-danger',
                                            'ANULADA' => 'bg-secondary'
                                        ];
                                    @endphp
                                    <span class="badge {{ $badges[$cuenta->estado] ?? 'bg-light text-dark' }} px-3 py-2 rounded-pill shadow-sm">
                                        {{ $cuenta->estado }}
                                    </span>
                                </td>
                                <td class="text-center font-monospace small">
                                    {{ $cuenta->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="text-center pe-4">
                                    @if (in_array($cuenta->estado, ['PENDIENTE', 'PARCIAL'], true))
                                        <button type="button" 
                                                class="btn btn-sm btn-dark shadow-sm rounded-pill px-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#pagarCuentaModal"
                                                data-id="{{ $cuenta->id }}"
                                                data-saldo="{{ number_format($cuenta->saldo_pendiente, 2, '.', '') }}"
                                                data-compra="{{ $cuenta->compra?->serie }}-{{ $cuenta->compra?->correlativo }}">
                                            <i class="fas fa-money-bill-wave me-1"></i> Pagar
                                        </button>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-file-invoice-dollar fs-2 opacity-50"></i></div>
                                    <p class="mb-0">No hay cuentas por pagar con los filtros actuales.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($cuentas->hasPages())
                <div class="pagination-custom">
                    {{ $cuentas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Único de Pagos -->
<div class="modal fade" id="pagarCuentaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formPagarCuenta" method="POST" action="">
                @csrf
                <div class="modal-header bg-light border-bottom-0 pb-0 rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-hand-holding-dollar me-2 text-primary"></i>Registrar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Abono a la compra <strong id="modalInfoCompra" class="text-dark"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Monto pendiente</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold border-end-0">S/</span>
                            <input type="text" id="modalSaldoPendienteShow" class="form-control bg-light border-start-0 fw-bold font-monospace text-danger" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Método de pago <span class="text-danger">*</span></label>
                        <select name="metodo_pago" class="form-select shadow-sm" required>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TARJETA">Tarjeta</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                            <option value="YAPE">Yape</option>
                            <option value="PLIN">Plin</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Monto a pagar <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">S/</span>
                            <input type="number" step="0.01" min="0.01" name="monto" id="modalInputMonto" class="form-control border-start-0 fw-bold font-monospace" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Referencia (Opcional)</label>
                        <input type="text" name="referencia_operacion" class="form-control shadow-sm" maxlength="100" placeholder="N° de operación, voucher...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Observación</label>
                        <textarea name="observacion" class="form-control shadow-sm" rows="2" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 justify-content-between rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-medium" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark px-4 rounded-pill fw-medium"><i class="fas fa-save me-2"></i>Guardar pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pagarModal = document.getElementById('pagarCuentaModal');
        if (pagarModal) {
            pagarModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const cuentaId = button.getAttribute('data-id');
                const saldo = button.getAttribute('data-saldo');
                const compra = button.getAttribute('data-compra');
                
                document.getElementById('formPagarCuenta').action = `/cuentas-por-pagar/${cuentaId}/pagos`;
                document.getElementById('modalInfoCompra').textContent = compra;
                document.getElementById('modalSaldoPendienteShow').value = saldo;
                
                const inputMonto = document.getElementById('modalInputMonto');
                inputMonto.setAttribute('max', saldo);
                inputMonto.value = saldo;
            });
        }
    });
</script>
@endpush