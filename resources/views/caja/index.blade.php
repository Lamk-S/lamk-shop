@extends('layouts.app')
@section('title', 'Gestión de Cajas')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Gestión de Cajas</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Terminales Operativas</li>
            </ol>
        </div>
        @can('gestionar_cajas')
            <div>
                <a href="{{ route('cajas.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-medium">
                    <i class="fas fa-plus me-2"></i>Nueva Caja
                </a>
            </div>
        @endcan
    </div>

    <!-- Tarjeta Principal -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-cash-register fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Cajas Registradas</h5>
                    <div class="text-muted small">Puntos de cobro físicos en tienda.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-secondary small text-uppercase">Identificador</th>
                            <th class="text-end text-secondary small text-uppercase">Fondo Fijo</th>
                            <th class="text-center text-secondary small text-uppercase">Estado</th>
                            <th class="text-end text-secondary small text-uppercase">Registro</th>
                            @can('gestionar_cajas')
                                <th class="text-center text-secondary small text-uppercase pe-4" style="width: 120px;">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $item->nombre }}</div>
                                    <div class="small text-muted font-monospace">Código / ID: {{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="text-end fw-bold font-monospace fs-6">
                                    S/ {{ number_format((float) $item->fondo_fijo, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($item->trashed())
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill">Inhabilitada</span>
                                    @elseif((int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">Operativa</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1 rounded-pill">Suspendida</span>
                                    @endif
                                </td>
                                <td class="text-end text-muted">
                                    <div class="small fw-bold text-dark">{{ optional($item->created_at)->format('d/m/Y') }}</div>
                                    <div class="small" style="font-size: 0.75rem;">{{ optional($item->created_at)->format('H:i') }}</div>
                                </td>
                                @can('gestionar_cajas')
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('cajas.edit', $item) }}" class="btn btn-sm btn-light border text-primary" title="Editar datos">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-light border {{ $item->trashed() ? 'text-success' : 'text-danger' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#dinamicConfirmModal"
                                                data-action="{{ route('cajas.destroy', $item) }}"
                                                data-nombre="{{ $item->nombre }}"
                                                data-trashed="{{ $item->trashed() ? 1 : 0 }}"
                                                title="{{ $item->trashed() ? 'Restaurar' : 'Eliminar' }}">
                                                <i class="fas {{ $item->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_cajas') ? 5 : 4 }}" class="py-5 text-center text-muted">
                                    <i class="fas fa-cash-register fs-1 text-light mb-3"></i>
                                    <h5 class="fw-semibold text-dark">No hay cajas registradas</h5>
                                    <p class="mb-0">Agrega terminales de caja para comenzar a registrar ventas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small fw-medium">
                    Mostrando <strong>{{ $cajas->firstItem() ?? 0 }}</strong> a <strong>{{ $cajas->lastItem() ?? 0 }}</strong> de <strong>{{ $cajas->total() }}</strong>
                </div>
                <div class="pagination-custom">
                    {{ $cajas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dinámico -->
@can('gestionar_cajas')
<div class="modal fade" id="dinamicConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-4">
                <div class="mb-3" id="modalIconContainer">
                    <!-- Icono inyectado por JS -->
                </div>
                <h4 class="fw-bold text-dark" id="modalTitle">¿Cambiar estado?</h4>
                <p class="text-muted mb-0">La caja "<span class="fw-bold text-dark" id="modalBoxName"></span>" cambiará su estado en el sistema.</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" action="" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" id="modalSubmitBtn" class="btn px-4 shadow-sm">
                        Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmModal = document.getElementById('dinamicConfirmModal');
        if (confirmModal) {
            confirmModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const actionUrl = button.getAttribute('data-action');
                const nombre = button.getAttribute('data-nombre');
                const isTrashed = button.getAttribute('data-trashed') === '1';

                const form = document.getElementById('deleteForm');
                const title = document.getElementById('modalTitle');
                const nameSpan = document.getElementById('modalBoxName');
                const iconContainer = document.getElementById('modalIconContainer');
                const submitBtn = document.getElementById('modalSubmitBtn');

                form.action = actionUrl;
                nameSpan.textContent = nombre;

                if (isTrashed) {
                    title.textContent = '¿Restaurar terminal de caja?';
                    iconContainer.innerHTML = '<i class="fas fa-trash-restore-alt fa-4x text-success opacity-75"></i>';
                    submitBtn.className = 'btn btn-success px-4 shadow-sm';
                    submitBtn.textContent = 'Sí, restaurar caja';
                } else {
                    title.textContent = '¿Eliminar terminal de caja?';
                    iconContainer.innerHTML = '<i class="fas fa-exclamation-triangle fa-4x text-danger opacity-75"></i>';
                    submitBtn.className = 'btn btn-danger px-4 shadow-sm';
                    submitBtn.textContent = 'Sí, inhabilitar caja';
                }
            });
        }
    });
</script>
@endpush