@extends('layouts.app')
@section('title', 'Movimientos de Caja')

@push('css')
<style>
    .table-soft thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .78rem; letter-spacing: .04em; white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .14); }
    .empty-state { padding: 2.5rem 1rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Movimientos de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Movimientos de Caja</li>
            </ol>
        </div>

        @can('movimientos_caja')
            <div class="mt-3 mt-md-0">
                <a href="{{ route('movimientos-caja.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                    <i class="fas fa-plus me-2"></i>Nuevo Movimiento
                </a>
            </div>
        @endcan
    </div>

    <div class="card card-soft">
        <div class="card-header soft-header p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Historial de movimientos</h5>
                    <div class="text-muted small">Movimientos manuales, ajustes y registros operativos de caja</div>
                </div>
            </div>

            <form method="GET" class="d-flex align-items-center gap-2">
                <label for="per_page" class="text-muted small mb-0">Mostrar</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach([10, 15, 25, 50] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th>Sesión</th>
                            <th>Caja</th>
                            <th>Usuario</th>
                            <th class="text-center">Tipo</th>
                            <th>Origen</th>
                            <th>Descripción</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td class="fw-semibold text-dark">#{{ $item->sesion_caja_id }}</td>
                                <td>{{ $item->sesionCaja?->caja?->nombre ?? 'N/A' }}</td>
                                <td>{{ $item->sesionCaja?->user?->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($item->tipo === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Egreso</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                                        {{ $item->origen }}
                                    </span>
                                </td>
                                <td>{{ $item->descripcion }}</td>
                                <td class="text-end fw-semibold">S/ {{ number_format((float) $item->monto, 2) }}</td>
                                <td class="text-end">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-money-bill-wave text-secondary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No hay movimientos de caja</h5>
                                        <p class="text-muted mb-0">Todavía no se han registrado movimientos en las sesiones de caja.</p>
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
                <div>{{ $movimientos->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection