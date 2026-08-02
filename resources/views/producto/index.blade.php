@extends('layouts.app')
@section('title', 'Catálogo de Productos')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">Catálogo de Productos</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Productos</li>
            </ol>
        </div>

        @can('gestionar_productos')
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('producto-variantes.index') }}" class="btn btn-light border shadow-sm rounded-3 px-4 fw-medium text-secondary">
                    <i class="fas fa-layer-group me-2"></i>Gestión de Variantes
                </a>
                <a href="{{ route('productos.create') }}" class="btn btn-primary shadow-sm rounded-3 px-4 fw-medium">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </a>
            </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Inventario general</h5>
                        <div class="text-muted small">Búsqueda rápida, filtros y administración de stock base</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('productos.index') }}" id="filtro-productos-form" class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <label for="q" class="form-label fw-bold text-secondary small text-uppercase">Buscar producto</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" id="q" class="form-control border-start-0 ps-0" value="{{ request('q') }}" placeholder="Código, barra o nombre...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="tipo_producto" class="form-label fw-bold text-secondary small text-uppercase">Clasificación</label>
                    <select name="tipo_producto" id="tipo_producto" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        @foreach(\App\Enums\TipoProducto::opciones() as $value => $label)
                            <option value="{{ $value }}" @selected(request('tipo_producto') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="marca_id" class="form-label fw-bold text-secondary small text-uppercase">Marca</label>
                    <select name="marca_id" id="marca_id" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}" @selected((string) request('marca_id') === (string) $marca->id)>{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="estado" class="form-label fw-bold text-secondary small text-uppercase">Disponibilidad</label>
                    <select name="estado" id="estado" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-12 d-flex gap-2 justify-content-end align-items-end">
                    <button type="submit" class="btn btn-primary shadow-sm w-100 fw-medium" title="Aplicar filtros">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <a href="{{ route('productos.index') }}" class="btn btn-light border shadow-sm w-100 fw-medium" title="Limpiar todos los filtros">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </a>
                </div>
            </form>

            <div class="table-responsive bg-white shadow-sm rounded-3 border">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-secondary small text-uppercase fw-bold" style="min-width: 280px;">Identificación de Producto</th>
                            <th class="text-secondary small text-uppercase fw-bold" style="min-width: 200px;">Clasificación</th>
                            <th class="text-center text-secondary small text-uppercase fw-bold">Stock Actual</th>
                            @can('gestionar_productos')
                                <th class="text-end text-secondary small text-uppercase fw-bold">P. Compra</th>
                            @endcan
                            <th class="text-end text-secondary small text-uppercase fw-bold">P. Venta</th>
                            <th class="text-center text-secondary small text-uppercase fw-bold">Estado</th>
                            @canany(['gestionar_productos', 'ver_productos'])
                                <th class="text-center pe-4 text-secondary small text-uppercase fw-bold" style="width: 120px;">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->img_path)
                                            <img src="{{ asset('storage/' . $item->img_path) }}" alt="{{ $item->nombre }}" class="rounded-3 shadow-sm" style="width: 52px; height: 52px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-secondary border" style="width: 52px; height: 52px;">
                                                <i class="fas fa-box-open"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <div class="fw-bold text-dark text-wrap" style="max-width: 250px;">{{ $item->nombre }}</div>
                                            <div class="small mt-1">
                                                <span class="badge bg-light text-secondary border me-1"><i class="fas fa-hashtag me-1"></i>{{ $item->codigo }}</span>
                                                @if($item->codigo_barra)
                                                    <span class="badge bg-light text-secondary border"><i class="fas fa-barcode me-1"></i>{{ $item->codigo_barra }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small mb-1">
                                        <span class="badge bg-light text-secondary border rounded-pill me-1"><i class="fas fa-tag"></i> {{ optional($item->marca)->nombre ?? 'Genérico' }}</span>
                                        <span class="badge bg-light text-secondary border rounded-pill"><i class="fas fa-layer-group"></i> {{ ucfirst(strtolower($item->tipo_producto?->value ?? $item->tipo_producto)) }}</span>
                                    </div>
                                    <div class="mt-1">
                                        @if($item->maneja_tallas)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2">Segmentado por tallas</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2">Talla estándar/única</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2 {{ ($item->stock_total ?? 0) <= ($item->stock_minimo ?? 5) ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' }}">
                                        {{ number_format((float) ($item->stock_total ?? 0), 0) }} unid.
                                    </span>
                                </td>
                                @can('gestionar_productos')
                                    <td class="text-end fw-medium text-secondary">S/ {{ number_format((float) $item->precio_compra, 2) }}</td>
                                @endcan
                                <td class="text-end fw-bold text-success">S/ {{ number_format((float) $item->precio_venta, 2) }}</td>
                                <td class="text-center">
                                    @if(!$item->trashed() && (int) $item->estado === 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Activo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-light border text-info btn-ver" 
                                            data-producto="{{ json_encode([
                                                'id' => $item->id,
                                                'nombre' => $item->nombre,
                                                'codigo' => $item->codigo,
                                                'codigo_barra' => $item->codigo_barra,
                                                'marca' => optional($item->marca)->nombre ?? 'Genérico',
                                                'descripcion' => $item->descripcion ?? 'No hay descripción disponible.',
                                                'img_url' => $item->img_path ? asset('storage/' . $item->img_path) : null,
                                                'tipo_producto' => ucfirst(strtolower($item->tipo_producto?->value ?? $item->tipo_producto)),
                                                'stock_total' => (float) ($item->stock_total ?? 0),
                                                'stock_minimo' => (float) ($item->stock_minimo ?? 5),
                                                'precio_compra' => number_format((float) $item->precio_compra, 2),
                                                'precio_venta' => number_format((float) $item->precio_venta, 2),
                                                'variantes' => isset($item->variantes) ? $item->variantes->map(fn($v) => [
                                                    'nombre' => $v->talla->nombre ?? $v->talla ?? $v->nombre ?? 'N/A',
                                                    'stock_actual' => (float) $v->stock_actual
                                                ])->toArray() : []
                                            ]) }}" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @can('gestionar_productos')
                                            <a href="{{ route('productos.edit', $item) }}" class="btn btn-sm btn-light border text-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                class="btn btn-sm btn-light border btn-confirmar {{ $item->trashed() ? 'text-success' : 'text-danger' }}"
                                                data-nombre="{{ $item->nombre }}"
                                                data-accion="{{ $item->trashed() ? 'restaurar' : 'desactivar' }}"
                                                data-url="{{ $item->trashed() ? route('productos.restore', $item) : route('productos.destroy', $item) }}"
                                                data-metodo="{{ $item->trashed() ? 'PATCH' : 'DELETE' }}"
                                                title="{{ $item->trashed() ? 'Restaurar' : 'Desactivar' }}">
                                                <i class="fas {{ $item->trashed() ? 'fa-trash-restore-alt' : 'fa-trash-alt' }}"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('gestionar_productos') ? 7 : 6 }}" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-search text-secondary fs-2 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">No se encontraron productos</h5>
                                        <p class="text-muted mb-0">Ajusta los filtros de búsqueda o registra un nuevo producto.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ route('productos.index') }}" id="pagination-form" class="d-flex align-items-center gap-2">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page" class="form-label mb-0 small fw-bold text-muted text-uppercase">Mostrar:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                        @foreach([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small fw-medium ms-2">
                        del <span class="fw-bold text-dark">{{ $productos->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $productos->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $productos->total() }}</span>
                    </span>
                </form>
                <div class="pagination-custom">
                    {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles Global -->
<div class="modal fade" id="modalVerGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="fas fa-box-open me-2 text-primary"></i>Ficha de Producto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <img id="verModalImg" src="" alt="" class="img-fluid rounded-3 shadow-sm d-none" style="max-height: 250px; object-fit: contain;">
                        <div id="verModalNoImg" class="bg-light border rounded-3 d-flex align-items-center justify-content-center w-100 shadow-sm" style="height: 250px;">
                            <i class="fas fa-image fa-4x text-secondary opacity-25"></i>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-2 text-dark" id="verModalNombre">--</h4>
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" id="verModalCodigo">--</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" id="verModalMarca">--</span>
                            <span class="badge bg-light text-secondary border" id="verModalCodigoBarraBadge" class="d-none">
                                <i class="fas fa-barcode me-1"></i><span id="verModalCodigoBarra"></span>
                            </span>
                        </div>
                        
                        <p class="text-muted small mb-4" id="verModalDescripcion">--</p>

                        <div class="row g-3">
                            @can('gestionar_productos')
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Clasificación</div>
                                    <div class="fw-medium text-dark" id="verModalClasificacion">--</div>
                                </div>
                            </div>
                            @endcan
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Stock General</div>
                                    <div id="verModalStockBlock" class="fw-bold">
                                        <span id="verModalStockTotal"></span> Unidades
                                    </div>
                                </div>
                            </div>
                            @can('gestionar_productos')
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Precio Compra</div>
                                        <div class="fw-medium text-dark">S/ <span id="verModalPrecioCompra">--</span></div>
                                    </div>
                                </div>
                            @endcan
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 border border-success border-opacity-50">
                                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Precio Venta</div>
                                    <div class="fw-bold text-success fs-5">S/ <span id="verModalPrecioVenta">--</span></div>
                                </div>
                            </div>
                        </div>

                        <div id="verModalVariantesContainer" class="mt-4 pt-3 border-top d-none">
                            <div class="small text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem;">
                                <i class="fas fa-tags me-1"></i> Disponibilidad por Talla
                            </div>
                            <div class="d-flex flex-wrap gap-2" id="verModalVariantesList"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3">
                <button type="button" class="btn btn-light border fw-medium px-4 shadow-sm" data-bs-dismiss="modal">Cerrar</button>
                @can('gestionar_productos')
                    <a href="#" id="verModalBtnEditar" class="btn btn-primary fw-medium px-4 shadow-sm">
                        <i class="fas fa-edit me-2"></i>Editar Producto
                    </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalVerObj = document.getElementById('modalVerGlobal');
        
        const modalVer = modalVerObj ? new bootstrap.Modal(modalVerObj) : null;
        
        document.body.addEventListener('click', function (e) {
            // --- VER DETALLES ---
            const btnVer = e.target.closest('.btn-ver');
            if (btnVer && modalVer) {
                const producto = JSON.parse(btnVer.dataset.producto);
                
                document.getElementById('verModalNombre').textContent = producto.nombre;
                document.getElementById('verModalCodigo').textContent = producto.codigo;
                document.getElementById('verModalMarca').textContent = producto.marca;
                document.getElementById('verModalDescripcion').textContent = producto.descripcion;
                
                const badgeBarras = document.getElementById('verModalCodigoBarraBadge');
                if (producto.codigo_barra) {
                    document.getElementById('verModalCodigoBarra').textContent = producto.codigo_barra;
                    badgeBarras.classList.remove('d-none');
                } else {
                    badgeBarras.classList.add('d-none');
                }

                const imgNode = document.getElementById('verModalImg');
                const noImgNode = document.getElementById('verModalNoImg');
                if (producto.img_url) {
                    imgNode.src = producto.img_url;
                    imgNode.classList.remove('d-none');
                    noImgNode.classList.add('d-none');
                } else {
                    imgNode.src = '';
                    imgNode.classList.add('d-none');
                    noImgNode.classList.remove('d-none');
                }

                const clasificacion = document.getElementById('verModalClasificacion');
                if (clasificacion) clasificacion.textContent = producto.tipo_producto;
                
                const precioCompra = document.getElementById('verModalPrecioCompra');
                if (precioCompra) precioCompra.textContent = producto.precio_compra;
                
                document.getElementById('verModalPrecioVenta').textContent = producto.precio_venta;

                const stockTotalNode = document.getElementById('verModalStockTotal');
                const stockBlockNode = document.getElementById('verModalStockBlock');
                stockTotalNode.textContent = producto.stock_total;
                
                if (producto.stock_total <= producto.stock_minimo) {
                    stockBlockNode.className = 'fw-bold text-danger';
                } else {
                    stockBlockNode.className = 'fw-bold text-success';
                }

                const variantesContainer = document.getElementById('verModalVariantesContainer');
                const variantesList = document.getElementById('verModalVariantesList');
                variantesList.innerHTML = '';

                if (producto.variantes && producto.variantes.length > 0) {
                    const fragment = document.createDocumentFragment();
                    
                    producto.variantes.forEach(v => {
                        const hasStock = v.stock_actual > 0;
                        const div = document.createElement('div');
                        div.className = `border ${hasStock ? 'border-primary bg-primary bg-opacity-10' : 'border-danger bg-danger bg-opacity-10'} rounded-3 px-3 py-2 text-center shadow-sm`;
                        div.style.minWidth = '75px';
                        
                        div.innerHTML = `
                            <span class="d-block fw-bold ${hasStock ? 'text-primary' : 'text-danger'}">${v.nombre}</span>
                            <span class="d-block ${hasStock ? 'text-dark fw-medium' : 'text-danger fw-bold'}" style="font-size: 0.75rem;">
                                ${hasStock ? v.stock_actual + ' ud.' : 'Agotado'}
                            </span>
                        `;
                        fragment.appendChild(div);
                    });
                    variantesList.appendChild(fragment);
                    variantesContainer.classList.remove('d-none');
                } else {
                    variantesContainer.classList.add('d-none');
                }

                const btnEditar = document.getElementById('verModalBtnEditar');
                if (btnEditar) {
                    btnEditar.href = `/productos/${producto.id}/edit`;
                }

                modalVer.show();
            }
        });
    });
</script>
@endpush