@extends('layouts.app')

@section('title', 'Detalle de Venta')

@push('css')
<style>
    .invoice-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; font-weight: 600; margin-bottom: 0.2rem; }
    .invoice-value { font-size: 1.1rem; color: #212529; font-weight: 500; }
    .table-invoice th { background-color: #f8f9fa; color: #495057; font-weight: 600; }
    .totals-row th { font-size: 1.1rem; }
    .totals-row.grand-total th { font-size: 1.3rem; color: #0d6efd; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Detalle de Venta</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}" class="text-decoration-none">Ventas</a></li>
                <li class="breadcrumb-item active">Ver recibo</li>
            </ol>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn btn-light shadow-sm"><i class="fas fa-arrow-left me-2"></i>Volver</a>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm"><i class="fas fa-print me-2"></i>Imprimir</button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 w-100 mx-auto" style="max-width: 900px;">
        <!-- Cabecera del Documento -->
        <div class="card-body p-4 p-md-5 border-bottom">
            <div class="row align-items-center mb-4">
                <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                    <h3 class="fw-bold text-primary mb-0">Recibo de Venta</h3>
                    <span class="badge bg-{{ $venta->estado == 1 ? 'success' : 'danger' }} mt-2">
                        {{ $venta->estado == 1 ? 'Venta Activa' : 'Venta Anulada' }}
                    </span>
                </div>
                <div class="col-sm-6 text-center text-sm-end">
                    <div class="invoice-label">N° de Comprobante</div>
                    <h4 class="fw-bold text-dark mb-0">{{ $venta->numero_comprobante }}</h4>
                    <div class="text-muted small">{{ $venta->comprobante->tipo_comprobante }}</div>
                </div>
            </div>
            
            <div class="row bg-light p-4 rounded-3 g-4">
                <div class="col-md-6">
                    <div class="invoice-label"><i class="fas fa-user-tie me-1"></i> Cliente</div>
                    <div class="invoice-value">{{ $venta->cliente->persona->razon_social }}</div>
                </div>
                <div class="col-md-3">
                    <div class="invoice-label"><i class="fas fa-calendar-alt me-1"></i> Fecha</div>
                    <div class="invoice-value">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="invoice-label"><i class="fas fa-clock me-1"></i> Hora</div>
                    <div class="invoice-value">{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i') }}</div>
                </div>
                
                <!-- Input oculto necesario para el JS de totales -->
                <input id="input-impuesto" type="hidden" value="{{ $venta->impuesto }}">
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-invoice mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 ps-md-5">Descripción del Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Desc.</th>
                            <th class="text-end pe-4 pe-md-5">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->productos as $item)
                        <tr>
                            <td class="ps-4 ps-md-5 py-3 text-dark fw-medium">{{ $item->nombre }}</td>
                            <td class="text-center py-3">{{ $item->pivot->cantidad }}</td>
                            <td class="text-end py-3 text-muted">{{ number_format($item->pivot->precio_venta, 2) }}</td>
                            <td class="text-end py-3 text-danger">{{ $item->pivot->descuento > 0 ? '-'.number_format($item->pivot->descuento, 2) : '0.00' }}</td>
                            <td class="text-end pe-4 pe-md-5 py-3 fw-bold text-dark td-subtotal">{{ ($item->pivot->cantidad * $item->pivot->precio_venta) - $item->pivot->descuento }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light border-top">
                        <tr class="totals-row">
                            <th colspan="4" class="text-end py-2 text-muted fw-normal">Subtotal sin IGV:</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark" id="th-suma"></th>
                        </tr>
                        <tr class="totals-row">
                            <th colspan="4" class="text-end py-2 text-muted fw-normal">IGV ({{ $venta->impuesto }}):</th>
                            <th class="text-end pe-4 pe-md-5 py-2 text-dark" id="th-igv"></th>
                        </tr>
                        <tr class="totals-row grand-total border-top">
                            <th colspan="4" class="text-end py-3">Total Venta:</th>
                            <th class="text-end pe-4 pe-md-5 py-3" id="th-total"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let filasSubtotal = document.getElementsByClassName('td-subtotal');
        let cont = 0;
        let impuestoStr = document.getElementById('input-impuesto').value;
        
        for (let i = 0; i < filasSubtotal.length; i++) {
            let valorFila = parseFloat(filasSubtotal[i].innerHTML);
            cont += valorFila;
            // Formatear visualmente el subtotal de la fila al cargar
            filasSubtotal[i].innerHTML = valorFila.toFixed(2);
        }

        let igv = parseFloat(impuestoStr);
        let total = cont + igv;

        document.getElementById('th-suma').innerHTML = cont.toFixed(2);
        document.getElementById('th-igv').innerHTML = igv.toFixed(2);
        document.getElementById('th-total').innerHTML = total.toFixed(2);
    });
</script>
@endpush