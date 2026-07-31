@extends('layouts.app')
@section('title', 'Auditoría de Cajas')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bolder text-dark mb-0 fs-3">Auditoría de Cajas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Turnos y Sesiones</li>
            </ol>
        </div>

        @can('abrir_caja')
            <div>
                <a href="{{ route('sesiones-caja.create') }}" class="btn btn-info text-dark fw-bold shadow-sm rounded-pill px-4">
                    <i class="fas fa-lock-open me-2"></i>Iniciar Turno
                </a>
            </div>
        @endcan
    </div>

    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 shadow-sm rounded-4 mb-4 d-flex align-items-start gap-3 p-3">
        <div class="bg-white rounded-circle p-2 shadow-sm text-primary">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <div>
            <div class="fw-bold text-primary">Auditoría de Efectivo Operativo</div>
            <div class="small text-dark text-opacity-75">
                Vigila los cuadres y descuadres diarios. El cierre transfiere el efectivo producto de las ventas hacia la <strong>Tesorería Principal</strong>.
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-opacity-50 border-bottom">
            <form method="GET" action="{{ route('sesiones-caja.index') }}" id="filtro-sesiones-form" class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Cajero / Operador</label>
                    <select name="user_id" class="form-select shadow-sm">
                        <option value="">Todos los cajeros</option>
                        @foreach ($cajeros as $cajero)
                            <option value="{{ $cajero->id }}" @selected((string) request('user_id') === (string) $cajero->id)>
                                {{ $cajero->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Estado de la Sesión</label>
                    <select name="estado_sesion" class="form-select shadow-sm">
                        <option value="">Cualquier estado</option>
                        <option value="ABIERTA" @selected(request('estado_sesion') === 'ABIERTA')>En Curso (Abierta)</option>
                        <option value="CERRADA" @selected(request('estado_sesion') === 'CERRADA')>Turno Finalizado</option>
                        <option value="ANULADA" @selected(request('estado_sesion') === 'ANULADA')>Anuladas</option>
                    </select>
                </div>

                <div class="col-lg-4 col-md-8">
                    <div class="d-flex gap-2">
                        <div class="w-50">
                            <label class="form-label text-muted small fw-bold text-uppercase">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control shadow-sm fs-7" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="w-50">
                            <label class="form-label text-muted small fw-bold text-uppercase">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control shadow-sm fs-7" value="{{ request('fecha_hasta') }}">
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 d-flex justify-content-end align-items-end">
                    <a href="{{ route('sesiones-caja.index') }}" class="btn btn-outline-secondary w-100 fw-medium bg-white shadow-sm" title="Limpiar filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive bg-white">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">Terminal</th>
                            <th class="border-bottom-0">Operador</th>
                            <th class="text-center border-bottom-0">Apertura</th>
                            <th class="text-center border-bottom-0">Cierre</th>
                            <th class="text-end border-bottom-0">Base / Sencillo</th>
                            <th class="text-end border-bottom-0" title="Saldo que el sistema calculó">Sistema (S/)</th>
                            <th class="text-end border-bottom-0" title="Saldo que el cajero contó y declaró">Declarado (S/)</th>
                            <th class="text-center border-bottom-0">Cuadre</th>
                            <th class="text-center border-bottom-0">Estado</th>
                            <th class="text-center pe-4 border-bottom-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesiones as $item)
                            @php
                                $esAbierta = $item->estado_sesion->value === 'ABIERTA';
                                $diferencia = (float) ($item->diferencia ?? 0);
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $item->caja?->nombre ?? 'N/A' }}</div>
                                    <div class="small text-muted font-monospace">Sesión #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center text-secondary me-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <span class="fw-medium text-dark">{{ explode(' ', $item->user?->name ?? 'N/A')[0] }}</span>
                                    </div>
                                </td>
                                <td class="text-center font-monospace fs-7">
                                    <div class="fw-bold text-dark">{{ $item->fecha_hora_apertura ? \Carbon\Carbon::parse($item->fecha_hora_apertura)->format('d/m/Y') : '—' }}</div>
                                    <div class="text-muted">{{ $item->fecha_hora_apertura ? \Carbon\Carbon::parse($item->fecha_hora_apertura)->format('H:i') : '—' }}</div>
                                </td>
                                <td class="text-center font-monospace fs-7">
                                    @if($item->fecha_hora_cierre)
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->fecha_hora_cierre)->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ \Carbon\Carbon::parse($item->fecha_hora_cierre)->format('H:i') }}</div>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1"><i class="fas fa-spinner fa-spin me-1"></i>En curso</span>
                                    @endif
                                </td>
                                <td class="text-end fw-medium text-muted font-monospace fs-7">{{ number_format((float) $item->saldo_inicial, 2) }}</td>
                                <td class="text-end fw-medium text-primary font-monospace fs-7">{{ number_format((float) ($item->saldo_final_esperado ?? 0), 2) }}</td>
                                <td class="text-end fw-bold text-dark font-monospace fs-7">{{ number_format((float) ($item->saldo_final_declarado ?? 0), 2) }}</td>
                                <td class="text-center font-monospace">
                                    @if($esAbierta)
                                        <span class="text-muted">—</span>
                                    @else
                                        @if($diferencia === 0.0)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded">Ok (0.00)</span>
                                        @elseif($diferencia > 0)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded" title="Sobrante">+{{ number_format($diferencia, 2) }}</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded" title="Faltante">{{ number_format($diferencia, 2) }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($esAbierta)
                                        <span class="badge bg-success text-white px-3 py-1 rounded-pill shadow-sm"><i class="fas fa-circle ms-n1 me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>Abierta</span>
                                    @elseif($item->estado_sesion->value === 'CERRADA')
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill">Cerrada</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Anulada</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group shadow-sm rounded-2" role="group">
                                        <a href="{{ route('sesiones-caja.show', $item) }}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Ver auditoría de caja">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a>
                                        @can('cerrar_caja')
                                            @if($esAbierta)
                                                <button type="button" 
                                                        class="btn btn-sm btn-dark" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalCierreCaja" 
                                                        data-id="{{ $item->id }}"
                                                        data-caja="{{ $item->caja?->nombre }}"
                                                        data-cajero="{{ explode(' ', $item->user?->name ?? '')[0] }}"
                                                        title="Realizar corte/cierre">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @else
                                                <span class="btn btn-sm btn-light border text-muted disabled"><i class="fas fa-lock"></i></span>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-cash-register text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Sin historial de cajas</h5>
                                        <p class="text-muted mb-0">No se encontraron sesiones con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <form method="GET" action="{{ route('sesiones-caja.index') }}" id="pagination-form" class="d-flex align-items-center gap-2">
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="form-label mb-0 small fw-bold text-muted text-uppercase">Filas:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm shadow-sm" style="width: auto;">
                    @foreach([5, 10, 15, 25, 50] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 10) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <span class="text-muted small fw-medium ms-2">
                    Viendo {{ $sesiones->firstItem() ?? 0 }} a {{ $sesiones->lastItem() ?? 0 }} de {{ $sesiones->total() }}
                </span>
            </form>
            <div>
                {{ $sesiones->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Unificado de Cierre -->
<div class="modal fade" id="modalCierreCaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-bottom-0 pb-0 rounded-top-4">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCierreCaja" action="" method="post">
                @method('DELETE')
                @csrf
                <div class="modal-body p-4 text-center">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-lock fa-3x"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Corte de Caja Diario</h4>
                    <p class="text-muted mb-4 small">
                        Vas a finalizar el turno de <strong id="modalCajaNombre" class="text-dark"></strong> operada por <strong id="modalCajeroNombre" class="text-dark"></strong>.
                    </p>
                    
                    <div class="bg-light p-3 rounded-4 border text-start">
                        <label for="saldo_final_declarado" class="form-label fw-bold text-dark text-uppercase fs-7">Efectivo total en gaveta</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0 fw-bold text-muted">S/</span>
                            <input type="number" step="0.01" min="0" required name="saldo_final_declarado" id="saldo_final_declarado" class="form-control border-start-0 fw-bold font-monospace" placeholder="0.00">
                        </div>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Suma el fondo fijo más las ventas en efectivo cobradas.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 justify-content-center rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-medium shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark px-4 rounded-pill fw-medium shadow-sm"><i class="fas fa-check-circle me-2"></i>Ejecutar Cierre</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filtro-sesiones-form');
        const paginationForm = document.getElementById('pagination-form');
        
        const selectsAndDates = filterForm.querySelectorAll('select, input[type="date"]');
        selectsAndDates.forEach(element => {
            element.addEventListener('change', () => filterForm.submit());
        });

        document.getElementById('per_page').addEventListener('change', () => {
            paginationForm.submit();
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const modalCierre = document.getElementById('modalCierreCaja');
        if (modalCierre) {
            modalCierre.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const caja = button.getAttribute('data-caja');
                const cajero = button.getAttribute('data-cajero');
                
                document.getElementById('formCierreCaja').action = `/sesiones-caja/${id}`;
                document.getElementById('modalCajaNombre').textContent = caja;
                document.getElementById('modalCajeroNombre').textContent = cajero;
                document.getElementById('saldo_final_declarado').value = '';
            });
        }
    });
</script>
@endpush