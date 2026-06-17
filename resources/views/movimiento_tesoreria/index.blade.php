@extends('layouts.app')
@section('title', 'Movimientos de Tesorería')

@push('css')
<style>
    .table-custom th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.82rem;
        white-space: nowrap;
    }
    .table-custom td {
        vertical-align: middle;
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Movimientos de Tesorería</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Tesorería</li>
            </ol>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fa-solid fa-right-left text-primary me-2"></i>Historial de movimientos
            </h5>
        </div>

        <div class="card-body p-4">
            <form method="GET" action="{{ route('movimientos-tesoreria.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="tesoreria_id" class="form-label fw-medium text-secondary">Tesorería</label>
                    <select name="tesoreria_id" id="tesoreria_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($tesorerias as $tesoreria)
                            <option value="{{ $tesoreria->id }}" @selected(request('tesoreria_id') == $tesoreria->id)>{{ $tesoreria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="tipo" class="form-label fw-medium text-secondary">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="INGRESO" @selected(request('tipo') === 'INGRESO')>Ingreso</option>
                        <option value="EGRESO" @selected(request('tipo') === 'EGRESO')>Egreso</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="origen" class="form-label fw-medium text-secondary">Origen</label>
                    <select name="origen" id="origen" class="form-select">
                        <option value="">Todos</option>
                        @foreach(['CIERRE_CAJA','VENTA_EFECTIVO','VENTA_TARJETA','VENTA_TRANSFERENCIA','COMPRA_PRODUCTO','DEPOSITO','RETIRO','AJUSTE','ANULACION'] as $origen)
                            <option value="{{ $origen }}" @selected(request('origen') === $origen)>{{ $origen }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="per_page" class="form-label fw-medium text-secondary">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('movimientos-tesoreria.index') }}" class="btn btn-light">Limpiar</a>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Tesorería</th>
                            <th>Usuario</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Medio</th>
                            <th class="text-center">Origen</th>
                            <th>Descripción</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Saldo anterior</th>
                            <th class="text-end">Saldo posterior</th>
                            <th class="text-end">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $item->tesoreria?->nombre ?? 'N/A' }}</td>
                                <td>{{ $item->user?->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($item->tipo === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Egreso</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->medio }}</td>
                                <td class="text-center"><span class="badge bg-light text-secondary border">{{ $item->origen }}</span></td>
                                <td>{{ $item->descripcion }}</td>
                                <td class="text-end fw-semibold">S/ {{ number_format((float) $item->monto, 2) }}</td>
                                <td class="text-end">S/ {{ number_format((float) $item->saldo_anterior, 2) }}</td>
                                <td class="text-end">S/ {{ number_format((float) $item->saldo_posterior, 2) }}</td>
                                <td class="text-end">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-vault text-secondary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No hay movimientos de tesorería</h5>
                                        <p class="text-muted mb-0">Todavía no se han registrado movimientos financieros.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4">
                <div class="text-muted small">
                    Mostrando {{ $movimientos->firstItem() ?? 0 }} - {{ $movimientos->lastItem() ?? 0 }} de {{ $movimientos->total() }} registros
                </div>
                <div>
                    {{ $movimientos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection