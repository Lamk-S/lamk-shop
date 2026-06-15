{{-- id="v7c1r2" --}}
@extends('layouts.app')

@section('title', 'Cuentas por pagar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Cuentas por pagar</h1>
    </div>

    @include('layouts.partials.alert')

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('cuentas-por-pagar.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" @selected(request('proveedor_id') == $proveedor->id)>
                                {{ $proveedor->persona?->nombre_completo }} - {{ $proveedor->persona?->numero_documento }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="PENDIENTE" @selected(request('estado') === 'PENDIENTE')>Pendiente</option>
                        <option value="PARCIAL" @selected(request('estado') === 'PARCIAL')>Parcial</option>
                        <option value="PAGADA" @selected(request('estado') === 'PAGADA')>Pagada</option>
                        <option value="ANULADA" @selected(request('estado') === 'ANULADA')>Anulada</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="vencidas" name="vencidas" @checked(request()->boolean('vencidas'))>
                        <label class="form-check-label" for="vencidas">
                            Solo vencidas
                        </label>
                    </div>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100" type="submit">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Proveedor</th>
                        <th>Compra</th>
                        <th>Total</th>
                        <th>Pagado</th>
                        <th>Pendiente</th>
                        <th>Estado</th>
                        <th>Vencimiento</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cuentas as $cuenta)
                        <tr>
                            <td>{{ $cuenta->id }}</td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $cuenta->proveedor?->persona?->nombre_completo }}
                                </div>
                                <small class="text-muted">
                                    {{ $cuenta->proveedor?->persona?->documento?->codigo }}
                                    {{ $cuenta->proveedor?->persona?->numero_documento }}
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold">Compra #{{ $cuenta->compra_id }}</div>
                                <small class="text-muted">
                                    {{ $cuenta->compra?->serie }}-{{ $cuenta->compra?->correlativo }}
                                </small>
                            </td>
                            <td>S/ {{ number_format($cuenta->total, 2) }}</td>
                            <td>S/ {{ number_format($cuenta->monto_pagado, 2) }}</td>
                            <td>S/ {{ number_format($cuenta->saldo_pendiente, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $cuenta->estado === 'PAGADA' ? 'success' : ($cuenta->estado === 'PARCIAL' ? 'warning' : ($cuenta->estado === 'ANULADA' ? 'secondary' : 'danger')) }}">
                                    {{ $cuenta->estado }}
                                </span>
                            </td>
                            <td>
                                {{ $cuenta->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="text-end">
                                @if (in_array($cuenta->estado, ['PENDIENTE', 'PARCIAL'], true))
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pagarCuentaModal{{ $cuenta->id }}"
                                    >
                                        Pagar
                                    </button>
                                @endif
                            </td>
                        </tr>

                        @if (in_array($cuenta->estado, ['PENDIENTE', 'PARCIAL'], true))
                            <div class="modal fade" id="pagarCuentaModal{{ $cuenta->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('cuentas-por-pagar.pagos.store', $cuenta) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Registrar pago</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Monto pendiente</label>
                                                    <input type="text" class="form-control" value="S/ {{ number_format($cuenta->saldo_pendiente, 2) }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Método de pago</label>
                                                    <select name="metodo_pago" class="form-select" required>
                                                        <option value="EFECTIVO">Efectivo</option>
                                                        <option value="TARJETA">Tarjeta</option>
                                                        <option value="TRANSFERENCIA">Transferencia</option>
                                                        <option value="YAPE">Yape</option>
                                                        <option value="PLIN">Plin</option>
                                                        <option value="OTRO">Otro</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Monto</label>
                                                    <input type="number" step="0.01" min="0.01" max="{{ $cuenta->saldo_pendiente }}" name="monto" class="form-control" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Referencia operación</label>
                                                    <input type="text" name="referencia_operacion" class="form-control" maxlength="100">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Observación</label>
                                                    <textarea name="observacion" class="form-control" rows="3" maxlength="255"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar pago</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No hay cuentas por pagar registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection