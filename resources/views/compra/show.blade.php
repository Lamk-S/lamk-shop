@extends('layouts.app')

@section('title', 'Detalles de Compra')

@push('css')
<style>
    .table-custom th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; border-bottom: 2px solid #e9ecef; }
    .table-custom td { vertical-align: middle; color: #495057; }
    .summary-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; margin-bottom: 0.2rem; }
    .summary-value { font-size: 1.1rem; color: #212529; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Detalles de la Transacción</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('compras.index') }}" class="text-decoration-none">Compras</a></li>
                <li class="breadcrumb-item active">Ver compra</li>
            </ol>
        </div>
        
        <div class="mt-3 mt-md-0">
            <a href="{{ route('compras.index') }}" class="btn btn-light shadow-sm border px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver al historial
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Tarjeta: Información General -->
        <div class="col-xl-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-file-invoice text-primary me-2"></i>Resumen del Comprobante</h5>
                    <span class="badge {{ $compra->estado == 1 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $compra->estado == 1 ? 'success' : 'danger' }} border px-3 py-2 rounded-pill">
                        {{ $compra->estado == 1 ? 'TRANSACCIÓN COMPLETADA' : 'TRANSACCIÓN ANULADA' }}
                    </span>
                </div>
                <div class="card-body p-4 p-md-5 bg-light bg-opacity-50">
                    <div class="row g-4">
                        
                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label"><i class="fa-solid fa-file me-1"></i> Tipo Comprobante</p>
                            <p class="summary-value">{{ $compra->comprobante->tipo_comprobante }}</p>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label"><i class="fa-solid fa-hashtag me-1"></i> Número</p>
                            <p class="summary-value">{{ $compra->numero_comprobante }}</p>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label"><i class="fa-solid fa-calendar-days me-1"></i> Fecha y Hora</p>
                            <p class="summary-value">{{ \Carbon\Carbon::parse($compra->fecha_hora)->format('d/m/Y - H:i') }} hrs</p>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <p class="summary-label"><i class="fa-solid fa-user-tie me-1"></i> Proveedor</p>
                            <p class="summary-value">{{ $compra->proveedore->persona->razon_social }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Detalle de Productos -->
        <div class="col-xl-12 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-box-open text-primary me-2"></i>Productos Adquiridos</h5>
                </div>
                <div class="card-body p-0"> <!-- p-0 para que la tabla ocupe los bordes -->
                    <div class="table-responsive">
                        <table class="table table-hover table-custom mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">P. Compra (S/)</th>
                                    <th class="text-end">P. Venta Ref. (S/)</th>
                                    <th class="text-end pe-4">Subtotal (S/)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($compra->productos as $item)
                                <tr>
                                    <td class="ps-4 fw-medium text-dark">{{ $item->nombre }}</td>
                                    <td class="text-center">{{ $item->pivot->cantidad }}</td>
                                    <td class="text-end">{{ number_format($item->pivot->precio_compra, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format($item->pivot->precio_venta, 2) }}</td>
                                    <td class="td-subtotal text-end pe-4 text-dark fw-medium">{{ ($item->pivot->cantidad) * ($item->pivot->precio_compra) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light bg-opacity-75">
                                <tr>
                                    <th colspan="4" class="text-end fw-semibold pt-4">Subtotal:</th>
                                    <th class="text-end pe-4 pt-4">S/ <span id="th-suma"></span></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end fw-semibold border-bottom-0 pb-3">IGV (18%):</th>
                                    <th class="text-end pe-4 border-bottom-0 pb-3">
                                        S/ <span id="th-igv"></span>
                                        <input type="hidden" id="input-impuesto" value="{{ $compra->impuesto }}">
                                    </th>
                                </tr>
                                <tr class="bg-white">
                                    <th colspan="4" class="text-end fw-bold text-dark fs-5 py-3 border-bottom-0">TOTAL FINAL:</th>
                                    <th class="text-end fw-bold text-primary fs-5 pe-4 py-3 border-bottom-0">S/ <span id="th-total"></span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        calcularValores();
    });

    function calcularValores() {
        let cont = 0;
        
        // 1. Recorremos cada subtotal usando jQuery
        $('.td-subtotal').each(function() {
            // Leemos el texto de la celda y lo convertimos a número (float)
            let val = parseFloat($(this).text());
            
            // Validamos que sea un número válido antes de sumar
            if (!isNaN(val)) {
                cont += val;
                // Formateamos visualmente a 2 decimales en la tabla
                $(this).text(val.toFixed(2));
            }
        });

        // 2. Capturamos el impuesto de forma segura
        let impuesto = parseFloat($('#input-impuesto').val()) || 0;

        // 3. Imprimimos los totales en los spans correspondientes
        $('#th-suma').html(cont.toFixed(2));
        $('#th-igv').html(impuesto.toFixed(2));
        $('#th-total').html((cont + impuesto).toFixed(2));
    }
</script>
@endpush