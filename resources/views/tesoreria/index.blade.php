@extends('layouts.app')
@section('title', 'Tesorería')

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.82rem; white-space: nowrap; }
    .table-custom td { vertical-align: middle; color: #495057; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Tesorería</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Tesorería</li>
            </ol>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small mb-1">Efectivo</div>
                    <h3 class="fw-bold text-success mb-2">
                        S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small">{{ $tesoreriaEfectivo?->nombre ?? 'No registrado' }}</div>
                    <span class="badge mt-3 {{ ($tesoreriaEfectivo?->estado ?? false) ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' }} px-3 py-2 rounded-pill">
                        {{ ($tesoreriaEfectivo?->estado ?? false) ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small mb-1">Banco</div>
                    <h3 class="fw-bold text-primary mb-2">
                        S/ {{ number_format((float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small">{{ $tesoreriaBanco?->nombre ?? 'No registrado' }}</div>
                    <span class="badge mt-3 {{ ($tesoreriaBanco?->estado ?? false) ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' }} px-3 py-2 rounded-pill">
                        {{ ($tesoreriaBanco?->estado ?? false) ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small mb-1">Total tesorería</div>
                    <h3 class="fw-bold text-dark mb-2">
                        S/ {{ number_format((float) (($tesoreriaEfectivo?->saldo_actual ?? 0) + ($tesoreriaBanco?->saldo_actual ?? 0)), 2) }}
                    </h3>
                    <div class="text-muted small">
                        Última actualización:
                        {{ collect([$tesoreriaEfectivo?->updated_at, $tesoreriaBanco?->updated_at])->filter()->max()?->format('d/m/Y H:i') ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-right-left"></i>
                </div>
                <h5 class="mb-0 fw-semibold text-dark">Movimientos recientes</h5>
            </div>
            <form method="GET" class="d-flex align-items-center gap-2">
                <label for="per_page" class="text-muted small mb-0">Mostrar</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach([5, 10, 15, 25, 50] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tesorería</th>
                            <th>Usuario</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Medio</th>
                            <th class="text-center">Origen</th>
                            <th>Descripción</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Saldo posterior</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $item)
                            <tr>
                                <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
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
                                <td class="text-end">S/ {{ number_format((float) $item->saldo_posterior, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-5 text-center text-muted">
                                    No hay movimientos de tesorería registrados.
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