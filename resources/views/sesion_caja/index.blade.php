@extends('layouts.app')

@section('title', 'Sesiones de Caja')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
    .table-custom td { vertical-align: middle; color: #495957; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Sesiones de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Sesiones de Caja</li>
            </ol>
        </div>

        @can('abrir-sesion-caja')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('sesiones-caja.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Abrir Sesión
            </a>
        </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-lock-open"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Historial de Sesiones</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th>Usuario</th>
                            <th class="text-center">Apertura</th>
                            <th class="text-center">Cierre</th>
                            <th class="text-end">Saldo Inicial</th>
                            <th class="text-end">Saldo Esperado</th>
                            <th class="text-end">Saldo Declarado</th>
                            <th class="text-end">Diferencia</th>
                            <th class="text-center">Estado</th>
                            @canany(['ver-sesion-caja', 'cerrar-sesion-caja'])
                            <th class="text-center">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesiones as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->caja?->nombre ?? 'N/A' }}</div>
                                    <div class="small text-muted">Sesión #{{ $item->id }}</div>
                                </td>

                                <td>
                                    <div class="small">{{ $item->user?->name ?? 'N/A' }}</div>
                                </td>

                                <td class="text-center">
                                    <div class="small">
                                        <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item->fecha_hora_apertura)->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ \Carbon\Carbon::parse($item->fecha_hora_apertura)->format('H:i') }}</div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    @if ($item->fecha_hora_cierre)
                                        <div class="small">
                                            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item->fecha_hora_cierre)->format('d/m/Y') }}</div>
                                            <div class="text-muted">{{ \Carbon\Carbon::parse($item->fecha_hora_cierre)->format('H:i') }}</div>
                                        </div>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Abierta</span>
                                    @endif
                                </td>

                                <td class="text-end fw-medium">{{ number_format($item->saldo_inicial, 2) }}</td>
                                <td class="text-end fw-medium">{{ number_format($item->saldo_final_esperado ?? 0, 2) }}</td>
                                <td class="text-end fw-medium">{{ number_format($item->saldo_final_declarado ?? 0, 2) }}</td>
                                <td class="text-end fw-bold {{ (float) ($item->diferencia ?? 0) === 0.0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($item->diferencia ?? 0, 2) }}
                                </td>

                                <td class="text-center">
                                    @if((int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Abierta</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill">Cerrada</span>
                                    @endif
                                </td>

                                @canany(['ver-sesion-caja', 'cerrar-sesion-caja'])
                                <td class="text-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        @can('ver-sesion-caja')
                                        <a href="{{ route('sesiones-caja.show', $item->id) }}" class="btn btn-sm btn-outline-secondary text-info border-light" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        @can('cerrar-sesion-caja')
                                            @if((int) $item->estado === 1)
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-danger border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Cerrar sesión">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @else
                                                <span class="btn btn-sm btn-outline-secondary text-secondary border-light disabled" title="Sesión cerrada">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                                            style="width: 90px; height: 90px;">
                                            <i class="fas fa-lock-open text-warning fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">
                                            No hay sesiones de caja registradas.
                                        </h5>
                                        <p class="text-muted mb-0">
                                            Aún no existen registros de sesión de caja para mostrar.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($sesiones as $item)
    @if((int) $item->estado === 1)
        <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('sesiones-caja.destroy', $item->id) }}" method="post">
                        @method('DELETE')
                        @csrf

                        <div class="modal-body pb-4">
                            <div class="text-center mb-3 text-danger">
                                <i class="fas fa-lock fa-4x opacity-75"></i>
                            </div>
                            <h4 class="fw-bold text-dark text-center">¿Cerrar sesión de caja?</h4>
                            <p class="text-muted text-center mb-4">
                                La caja <strong>{{ $item->caja?->nombre }}</strong> será cerrada.
                            </p>

                            <div class="mb-3">
                                <label for="saldo_final_declarado_{{ $item->id }}" class="form-label fw-medium">Saldo final declarado</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">S/</span>
                                    <input type="number" step="0.01" min="0"
                                        name="saldo_final_declarado"
                                        id="saldo_final_declarado_{{ $item->id }}"
                                        class="form-control border-start-0"
                                        placeholder="Monto contado en caja">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 justify-content-center">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger px-4">Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush