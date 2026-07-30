@extends('layouts.app')
@section('title', 'Auditoría de Ingresos por Ventas')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Auditoría de Pagos</h2>
            <p class="text-muted small mb-0 mt-1">Conciliación de ingresos y transferencias de ventas</p>
        </div>
    </div>

    @include('layouts.partials.alert')

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('pagos-venta.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Método de Pago</label>
                    <select name="metodo_pago" class="form-select shadow-sm">
                        <option value="">Todos los métodos</option>
                        <option value="EFECTIVO" @selected(request('metodo_pago') === 'EFECTIVO')>Efectivo</option>
                        <option value="TARJETA" @selected(request('metodo_pago') === 'TARJETA')>Tarjeta (POS)</option>
                        <option value="TRANSFERENCIA" @selected(request('metodo_pago') === 'TRANSFERENCIA')>Transferencia Bancaria</option>
                        <option value="YAPE" @selected(request('metodo_pago') === 'YAPE')>Yape</option>
                        <option value="PLIN" @selected(request('metodo_pago') === 'PLIN')>Plin</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Buscar Venta (ID)</label>
                    <input type="number" name="venta_id" class="form-control shadow-sm" placeholder="Ej: 1045" value="{{ request('venta_id') }}">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100 shadow-sm fw-medium" type="submit">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('pagos-venta.index') }}" class="btn btn-outline-secondary w-100 shadow-sm fw-medium">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Pagos -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive bg-white rounded-4">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">Fecha / Hora</th>
                            <th class="border-bottom-0">Ticket Venta</th>
                            <th class="border-bottom-0">Método</th>
                            <th class="border-bottom-0">Operador / Caja</th>
                            <th class="border-bottom-0">Referencia Operación</th>
                            <th class="text-end pe-4 border-bottom-0">Monto Cobrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pagos as $pago)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-medium text-dark">{{ $pago->created_at->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ $pago->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('ventas.show', $pago->venta_id) }}" class="fw-bold text-decoration-none">
                                        {{ $pago->venta->serie ?? 'TICKET' }}-{{ $pago->venta->correlativo ?? str_pad($pago->venta_id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $methodConfig = match($pago->metodo_pago->value ?? $pago->metodo_pago) {
                                            'EFECTIVO' => ['color' => 'success', 'icon' => 'fa-money-bill-wave'],
                                            'TARJETA' => ['color' => 'primary', 'icon' => 'fa-credit-card'],
                                            'YAPE', 'PLIN' => ['color' => 'info', 'icon' => 'fa-mobile-screen'],
                                            default => ['color' => 'secondary', 'icon' => 'fa-building-columns']
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $methodConfig['color'] }} bg-opacity-10 text-{{ $methodConfig['color'] }} border border-{{ $methodConfig['color'] }} border-opacity-25 px-3 py-1 rounded-pill">
                                        <i class="fas {{ $methodConfig['icon'] }} me-1"></i> {{ $pago->metodo_pago->value ?? $pago->metodo_pago }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark small fw-medium">{{ Str::words($pago->venta?->user?->name, 2, '') }}</div>
                                    <div class="text-muted small" style="font-size: 0.70rem;">{{ $pago->venta?->sesionCaja?->caja?->nombre ?? 'Sin caja' }}</div>
                                </td>
                                <td class="font-monospace text-muted small">
                                    {{ $pago->referencia_operacion ?: '—' }}
                                </td>
                                <td class="text-end pe-4 fw-bold font-monospace text-dark fs-6">
                                    S/ {{ number_format($pago->monto, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-receipt fs-2 opacity-50"></i></div>
                                    <p class="mb-0 text-muted">No se encontraron registros de pagos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($pagos->hasPages())
                <div class="pagination-custom">
                    {{ $pagos->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection