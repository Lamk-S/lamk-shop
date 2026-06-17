@extends('layouts.app')
@section('title', 'Sesiones de Caja')

@push('css')
<style>
    .table-soft thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: .78rem; letter-spacing: .04em; 
        white-space: nowrap; border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .table-soft td { vertical-align: middle; color: #334155; }
    .card-soft { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .14); }
    .summary-box { border: 1px solid rgba(148, 163, 184, .16); border-radius: 1rem; background: #fff; padding: 1rem; height: 100%; }
    .summary-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .08em; color: #64748b; font-weight: 700; margin-bottom: .3rem; }
    .summary-value { font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .empty-state { padding: 2.5rem 1rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Sesiones de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Sesiones de Caja</li>
            </ol>
        </div>

        @can('abrir_caja')
            <div class="mt-3 mt-md-0">
                <a href="{{ route('sesiones-caja.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                    <i class="fas fa-plus me-2"></i>Abrir Sesión
                </a>
            </div>
        @endcan
    </div>

    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fa-solid fa-circle-info mt-1"></i>
            <div>
                <div class="fw-semibold">Criterio operativo</div>
                <div class="small">
                    La apertura de caja representa el fondo inicial de operación, no un ingreso real de ventas.
                    Los ingresos reales deben venir de ventas en efectivo y otros movimientos autorizados.
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft mb-4">
        <div class="card-header soft-header p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-lock-open"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Historial de sesiones</h5>
                    <div class="text-muted small">Consulta, apertura y cierre de sesiones de caja</div>
                </div>
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
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th>Usuario</th>
                            <th class="text-center">Apertura</th>
                            <th class="text-center">Cierre</th>
                            <th class="text-end">Saldo inicial</th>
                            <th class="text-end">Saldo esperado</th>
                            <th class="text-end">Saldo declarado</th>
                            <th class="text-end">Diferencia</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesiones as $item)
                            @php
                                $esAbierta = $item->estado_sesion === 'ABIERTA';
                                $esCerrada = $item->estado_sesion === 'CERRADA';
                                $esAnulada = $item->estado_sesion === 'ANULADA';
                            @endphp
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
                                        <div class="fw-semibold text-dark">
                                            {{ $item->fecha_hora_apertura ? \Carbon\Carbon::parse($item->fecha_hora_apertura)->format('d/m/Y') : '—' }}
                                        </div>
                                        <div class="text-muted">
                                            {{ $item->fecha_hora_apertura ? \Carbon\Carbon::parse($item->fecha_hora_apertura)->format('H:i') : '—' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($item->fecha_hora_cierre)
                                        <div class="small">
                                            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item->fecha_hora_cierre)->format('d/m/Y') }}</div>
                                            <div class="text-muted">{{ \Carbon\Carbon::parse($item->fecha_hora_cierre)->format('H:i') }}</div>
                                        </div>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            Abierta
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-medium">S/ {{ number_format((float) $item->saldo_inicial, 2) }}</td>
                                <td class="text-end fw-medium">S/ {{ number_format((float) ($item->saldo_final_esperado ?? 0), 2) }}</td>
                                <td class="text-end fw-medium">S/ {{ number_format((float) ($item->saldo_final_declarado ?? 0), 2) }}</td>
                                <td class="text-end fw-bold {{ (float) ($item->diferencia ?? 0) === 0.0 ? 'text-success' : 'text-danger' }}">
                                    S/ {{ number_format((float) ($item->diferencia ?? 0), 2) }}
                                </td>
                                <td class="text-center">
                                    @if($esAbierta)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Abierta</span>
                                    @elseif($esCerrada)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">Cerrada</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Anulada</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        <a href="{{ route('sesiones-caja.show', $item) }}" class="btn btn-sm btn-outline-secondary text-info border-light" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @can('cerrar_caja')
                                            @if($esAbierta)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary text-danger border-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmModal-{{ $item->id }}"
                                                        title="Cerrar sesión">
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="empty-state">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-lock-open text-warning fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No hay sesiones de caja registradas</h5>
                                        <p class="text-muted mb-0">Aún no existen registros para mostrar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4">
                <div class="text-muted small">
                    Mostrando {{ $sesiones->firstItem() ?? 0 }} - {{ $sesiones->lastItem() ?? 0 }} de {{ $sesiones->total() }} registros
                </div>
                <div>
                    {{ $sesiones->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($sesiones as $item)
    @if($item->estado_sesion === 'ABIERTA')
        <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('sesiones-caja.destroy', $item) }}" method="post">
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
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="saldo_final_declarado"
                                           id="saldo_final_declarado_{{ $item->id }}"
                                           class="form-control border-start-0"
                                           placeholder="Monto contado en caja"
                                           value="{{ old('saldo_final_declarado') }}">
                                </div>
                            </div>

                            <div class="alert alert-warning border-0 mb-0">
                                El cierre transferirá el efectivo operativo a tesorería y conservará solo el fondo fijo de la caja.
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