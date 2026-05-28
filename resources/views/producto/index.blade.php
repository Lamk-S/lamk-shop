@extends('layouts.app')

@section('title', 'Catálogo de Productos')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
    .table-custom td { vertical-align: middle; color: #495057; }
    .cat-badge { display: inline-block; margin: 2px; font-weight: 500; }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Catálogo de Productos</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active">Productos</li>
            </ol>
        </div>

        @can('crear-producto')
        <div class="mt-3 mt-md-0">
            <a href="{{ route('productos.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4">
                <i class="fas fa-plus me-2"></i>Nuevo Producto
            </a>
        </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <h5 class="mb-0 fw-semibold text-dark">Inventario Actual</h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Clasificación</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">P. Compra</th>
                            <th class="text-center">P. Venta</th>
                            <th>Vencimiento</th>
                            <th class="text-center">Estado</th>
                            @canany(['editar-producto', 'eliminar-producto'])
                            <th class="text-center">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $item)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-secondary border fs-7">{{ $item->codigo }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->nombre }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 250px;" title="{{ $item->descripcion }}">
                                        {{ $item->descripcion ?: 'Sin descripción' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small mb-1">
                                        <i class="fas fa-tag text-muted me-1"></i>{{ optional($item->marca)->nombre ?? 'Sin marca' }}
                                        <span class="mx-1 text-muted">|</span>
                                        <i class="fas fa-box text-muted me-1"></i>{{ optional($item->presentacion)->nombre ?? 'Sin presentación' }}
                                    </div>
                                    <div>
                                        @forelse($item->categorias as $category)
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 cat-badge fs-8">
                                                {{ $category->nombre }}
                                            </span>
                                        @empty
                                            <span class="text-muted small fst-italic">Sin categorías</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-center align-content-center">
                                    <span class="badge bg-primary rounded-pill px-3">{{ $item->stock }} unid.</span>
                                </td>
                                <td class="text-center align-content-center">
                                    <span class="badge bg-danger rounded-pill px-3">S/ {{ number_format($item->precio_compra, 2) }}</span>
                                </td>
                                <td class="text-center align-content-center">
                                    <span class="badge bg-success rounded-pill px-3">S/ {{ number_format($item->precio_venta, 2) }}</span>
                                </td>
                                <td>
                                    @if($item->fecha_vencimiento)
                                        <span class="text-dark">
                                            <i class="far fa-calendar-alt text-muted me-1"></i>
                                            {{ \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted small fst-italic">No aplica</span>
                                    @endif
                                </td>
                                <td class="text-center align-content-center">
                                    @if(!$item->trashed())
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactivo</span>
                                    @endif
                                </td>

                                @canany(['editar-producto', 'eliminar-producto'])
                                <td class="text-center align-content-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary text-info border-light" data-bs-toggle="modal" data-bs-target="#verModal-{{ $item->id }}" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @can('editar-producto')
                                        <a href="{{ route('productos.edit', $item) }}" class="btn btn-sm btn-outline-secondary text-primary border-light" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar-producto')
                                            @if(!$item->trashed())
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-danger border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Desactivar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary text-success border-light" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}" title="Restaurar">
                                                    <i class="fas fa-trash-restore-alt"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>

                            <div class="modal fade" id="verModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-0">
                                            <h5 class="modal-title fw-bold text-dark">
                                                <i class="fas fa-box-open text-primary me-2"></i>{{ $item->nombre }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12 text-center mb-3">
                                                    @if($item->img_path)
                                                        <img src="{{ Storage::url($item->img_path) }}" alt="{{ $item->nombre }}" class="img-fluid rounded-3 shadow-sm border" style="max-height: 200px; object-fit: contain;">
                                                    @else
                                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border" style="height: 150px;">
                                                            <div class="text-muted text-center">
                                                                <i class="fas fa-image fa-3x mb-2 opacity-50"></i>
                                                                <p class="mb-0 small">Sin imagen</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-6">
                                                    <span class="text-muted small d-block mb-1">Código</span>
                                                    <span class="fw-medium text-dark">{{ $item->codigo }}</span>
                                                </div>

                                                <div class="col-6">
                                                    <span class="text-muted small d-block mb-1">Stock Actual</span>
                                                    <span class="badge bg-primary rounded-pill px-3">{{ $item->stock }} unid.</span>
                                                </div>

                                                <div class="col-6">
                                                    <span class="text-muted small d-block mb-1">Marca</span>
                                                    <span class="fw-medium text-dark">{{ optional($item->marca)->nombre ?? 'Sin marca' }}</span>
                                                </div>

                                                <div class="col-6">
                                                    <span class="text-muted small d-block mb-1">Presentación</span>
                                                    <span class="fw-medium text-dark">{{ optional($item->presentacion)->nombre ?? 'Sin presentación' }}</span>
                                                </div>

                                                <div class="col-6">
                                                    <span class="text-muted small d-block mb-1">P. Compra</span>
                                                    <span class="badge bg-danger rounded-pill px-3">S/ {{ number_format($item->precio_compra, 2) }}</span>
                                                </div>

                                                <div class="col-6">
                                                    <span class="text-muted small d-block mb-1">P. Venta</span>
                                                    <span class="badge bg-success rounded-pill px-3">S/ {{ number_format($item->precio_venta, 2) }}</span>
                                                </div>

                                                <div class="col-12">
                                                    <span class="text-muted small d-block mb-1">Descripción</span>
                                                    <p class="text-dark mb-0">{{ $item->descripcion ?: 'Sin descripción detallada.' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 justify-content-center">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cerrar panel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center pb-4">
                                            @if(!$item->trashed())
                                                <div class="text-danger mb-3"><i class="fas fa-box-open fa-4x opacity-75"></i></div>
                                                <h4 class="fw-bold text-dark">¿Desactivar producto?</h4>
                                                <p class="text-muted">El producto "{{ $item->nombre }}" ya no estará disponible para nuevas ventas o compras.</p>
                                            @else
                                                <div class="text-success mb-3"><i class="fas fa-box fa-4x opacity-75"></i></div>
                                                <h4 class="fw-bold text-dark">¿Restaurar producto?</h4>
                                                <p class="text-muted">El producto "{{ $item->nombre }}" volverá a estar activo en el inventario.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-0 pt-0 justify-content-center">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('productos.destroy', $item) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn {{ !$item->trashed() ? 'btn-danger' : 'btn-success' }} px-4">
                                                    Confirmar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush