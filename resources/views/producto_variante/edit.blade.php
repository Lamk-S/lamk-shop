@extends('layouts.app')

@section('title', 'Editar Variante')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; color: #0f172a; }
    .fs-7 { font-size: 0.875rem; }
    .soft-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .form-label-custom { font-size: .82rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .06em; }
    .helper-box { background: #f8fafc; border: 1px solid rgba(148, 163, 184, .18); border-radius: 1rem; padding: 1rem; }
    .bootstrap-select .dropdown-toggle:focus { outline: none !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important; }
    .bootstrap-select .btn-light { background-color: #fff !important; border-color: #dee2e6 !important; color: #212529 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-0">Editar Variante</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('producto-variantes.index') }}" class="text-decoration-none text-muted">Variantes</a></li>
                <li class="breadcrumb-item active fw-medium text-dark">Registro #{{ $productoVariante->id }}</li>
            </ol>
        </div>

        <span class="badge bg-light text-secondary border px-3 py-2">
            <i class="fas fa-barcode me-1"></i>{{ $productoVariante->codigo_variante }}
        </span>
    </div>

    <div class="card soft-card mx-auto" style="max-width: 980px;">
        <div class="card-header soft-header p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Actualizar variante</h5>
                    <div class="text-muted small">Mantén sincronizada la talla con el tipo de producto.</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('producto-variantes.update', $productoVariante) }}" method="post">
                @method('PATCH')
                @csrf

                <div class="row g-4">
                    <div class="col-lg-6">
                        <label for="producto_id" class="form-label form-label-custom">
                            Producto maestro <span class="text-danger">*</span>
                        </label>
                        <select
                            name="producto_id"
                            id="producto_id"
                            class="selectpicker form-control border shadow-sm @error('producto_id') is-invalid @enderror"
                            data-width="100%"
                            data-live-search="true"
                            data-size="5"
                            title="Busque y seleccione un producto..."
                            data-none-selected-text="Busque y seleccione un producto..."
                        >
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" data-tipo="{{ $producto->tipo_producto }}" @selected(old('producto_id', $productoVariante->producto_id) == $producto->id)>
                                    {{ $producto->codigo }} — {{ $producto->nombre }} ({{ ucfirst(strtolower($producto->tipo_producto)) }})
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="talla_id" class="form-label form-label-custom">
                            Talla compatible <span class="text-danger">*</span>
                        </label>
                        <select
                            name="talla_id"
                            id="talla_id"
                            class="selectpicker form-control border shadow-sm @error('talla_id') is-invalid @enderror"
                            data-width="100%"
                            data-live-search="true"
                            data-size="5"
                            title="Seleccione primero un producto..."
                            data-none-selected-text="Seleccione primero un producto..."
                            disabled
                        ></select>
                        @error('talla_id')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                        <div class="helper-box mt-3 text-muted small" id="talla-helper">
                            <i class="fas fa-info-circle me-1"></i>Selecciona un producto para filtrar las tallas válidas.
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label for="codigo_barra" class="form-label form-label-custom">
                            Código de barra <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                            <input
                                type="text"
                                name="codigo_barra"
                                id="codigo_barra"
                                class="form-control border-start-0 @error('codigo_barra') is-invalid @enderror"
                                value="{{ old('codigo_barra', $productoVariante->codigo_barra) }}"
                            >
                        </div>
                        @error('codigo_barra')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label for="stock_actual" class="form-label form-label-custom">
                            Stock actual <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            min="0"
                            name="stock_actual"
                            id="stock_actual"
                            class="form-control text-center @error('stock_actual') is-invalid @enderror"
                            value="{{ old('stock_actual', $productoVariante->stock_actual) }}"
                        >
                        @error('stock_actual')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label for="stock_minimo" class="form-label form-label-custom">
                            Stock mínimo <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            min="0"
                            name="stock_minimo"
                            id="stock_minimo"
                            class="form-control text-center @error('stock_minimo') is-invalid @enderror"
                            value="{{ old('stock_minimo', $productoVariante->stock_minimo) }}"
                        >
                        @error('stock_minimo')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label for="estado" class="form-label form-label-custom">Estado</label>
                        <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                            <option value="1" @selected(old('estado', $productoVariante->estado) == 1)>Activo</option>
                            <option value="0" @selected(old('estado', $productoVariante->estado) === 0 || old('estado', $productoVariante->estado) === '0')>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 border-top pt-4">
                        <a href="{{ route('producto-variantes.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-warning px-5 shadow-sm fw-bold">
                            <i class="fas fa-sync-alt me-2"></i>Actualizar variante
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script id="tallas-data" type="application/json">
@json($tallas)
</script>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(function () {
        const tallas = JSON.parse(document.getElementById('tallas-data').textContent || '[]');
        const productoSelect = $('#producto_id');
        const tallaSelect = $('#talla_id');
        const helper = $('#talla-helper');
        let selectedTalla = @json(old('talla_id', $productoVariante->talla_id));

        const reglas = {
            ZAPATILLA: ['CALZADO'],
            ROPA: ['ROPA'],
            ACCESORIO: ['UNICA'],
        };

        const labels = {
            CALZADO: 'Calzado',
            ROPA: 'Ropa',
            UNICA: 'Única'
        };

        function setMensaje(tipo) {
            if (tipo === 'ZAPATILLA') {
                helper.html('<i class="fas fa-shoe-prints text-primary me-1"></i>Calzado: solo se permiten tallas de <b>Calzado</b>.');
            } else if (tipo === 'ROPA') {
                helper.html('<i class="fas fa-shirt text-primary me-1"></i>Ropa: solo se permiten tallas de <b>Ropa</b>.');
            } else if (tipo === 'ACCESORIO') {
                helper.html('<i class="fas fa-box text-primary me-1"></i>Accesorio: restringido automáticamente a <b>Talla Única</b>.');
            } else {
                helper.html('<i class="fas fa-info-circle me-1"></i>Selecciona un producto para filtrar las tallas válidas.');
            }
        }

        function destroyTallaPicker() {
            if (tallaSelect.data('selectpicker')) {
                try {
                    tallaSelect.selectpicker('destroy');
                } catch (e) {}
            }
        }

        function initTallaPicker() {
            tallaSelect.selectpicker({
                noneSelectedText: 'Seleccione primero un producto...'
            });
        }

        function rebuildTallas(tipo) {
            const permitidas = reglas[tipo] || [];
            const currentValue = String(selectedTalla || '');
            const filtradas = tallas.filter(t => permitidas.includes(String(t.tipo_talla)));

            destroyTallaPicker();
            tallaSelect.empty();

            if (!tipo) {
                tallaSelect.prop('disabled', true);
                setMensaje('');
                initTallaPicker();
                return;
            }

            tallaSelect.prop('disabled', false);

            if (!filtradas.length) {
                tallaSelect.append('<option value="" disabled>No hay tallas disponibles</option>');
                setMensaje(tipo);
                initTallaPicker();
                return;
            }

            const grupos = {};
            filtradas.forEach(function (t) {
                const key = String(t.tipo_talla);
                if (!grupos[key]) grupos[key] = [];
                grupos[key].push(t);
            });

            Object.keys(grupos).forEach(function (grupo) {
                const groupLabel = labels[grupo] || grupo;
                tallaSelect.append(`<optgroup label="${groupLabel}"></optgroup>`);
                const lastGroup = tallaSelect.find('optgroup').last();

                grupos[grupo].forEach(function (t) {
                    lastGroup.append(
                        `<option value="${t.id}" data-tipo-talla="${t.tipo_talla}">
                            ${t.codigo} — ${t.nombre}
                        </option>`
                    );
                });
            });

            initTallaPicker();
            setMensaje(tipo);

            const validIds = new Set(filtradas.map(t => String(t.id)));
            const finalValue = validIds.has(currentValue) ? currentValue : '';
            selectedTalla = finalValue;
            tallaSelect.selectpicker('val', finalValue);
        }

        function sync() {
            const tipo = String(productoSelect.find('option:selected').data('tipo') || '');
            rebuildTallas(tipo);
        }

        productoSelect.selectpicker();
        initTallaPicker();

        productoSelect.on('changed.bs.select', sync);
        sync();
    });
</script>
@endpush