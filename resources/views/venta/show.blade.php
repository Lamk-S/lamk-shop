@extends('template')

@section('title', 'Ver venta')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Ver Venta</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
        <li class="breadcrumb-item active">Ver venta</li>
    </ol>
</div>

<div class="container-fluid w-100">

    <div class="card p-2 mb-4">
        <!-- Tipo comprobante -->
        <div class="row mb-2">
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-file"></i></span>
                    <input disabled type="text" class="form-control" value="Tipo de comprobante:">
                </div>
            </div>
            <div class="col-sm-7">
                <input disabled type="text" class="form-control" value="{{ $venta->comprobante->tipo_comprobante }}">
            </div>
        </div>
    
        <!-- Número comprobante -->
        <div class="row mb-2">
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                    <input disabled type="text" class="form-control" value="Número de comprobante:">
                </div>
            </div>
            <div class="col-sm-7">
                <input disabled type="text" class="form-control" value="{{ $venta->numero_comprobante }}">
            </div>
        </div>
    
        <!-- Cliente -->
        <div class="row mb-2">
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-user-tie"></i></span>
                    <input disabled type="text" class="form-control" value="Cliente:">
                </div>
            </div>
            <div class="col-sm-7">
                <input disabled type="text" class="form-control" value="{{ $venta->cliente->persona->razon_social }}">
            </div>
        </div>
    
        <!-- Fecha -->
        <div class="row mb-2">
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                    <input disabled type="text" class="form-control" value="Fecha:">
                </div>
            </div>
            <div class="col-sm-7">
                <input disabled type="text" class="form-control" value="{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d-m-Y') }}">
            </div>
        </div>
    
        <!-- Hora -->
        <div class="row mb-2">
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                    <input disabled type="text" class="form-control" value="Hora:">
                </div>
            </div>
            <div class="col-sm-7">
                <input disabled type="text" class="form-control" value="{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i') }}">
            </div>
        </div>
    
        <!-- Impuesto -->
        <div class="row mb-2">
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-percent"></i></span>
                    <input disabled type="text" class="form-control" value="Impuesto:">
                </div>
            </div>
            <div class="col-sm-7">
                <input id="input-impuesto" disabled type="text" class="form-control" value="{{ $venta->impuesto }}">
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla de detalle de la venta
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-white">Producto</th>
                        <th class="text-white">Cantidad</th>
                        <th class="text-white">Precio de venta</th>
                        <th class="text-white">Descuento</th>
                        <th class="text-white">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta->productos as $item)
                    <tr>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ $item->pivot->cantidad }}</td>
                        <td>{{ $item->pivot->precio_venta }}</td>
                        <td>{{ $item->pivot->descuento }}</td>
                        <td class="td-subtotal">{{ ($item->pivot->cantidad) * ($item->pivot->precio_venta) - ($item->pivot->descuento) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5"></th>
                    </tr>
                    <tr>
                        <th colspan="4">Suma:</th>
                        <th id="th-suma"></th>
                    </tr>
                    <tr>
                        <th colspan="4">IGV:</th>
                        <th id="th-igv"></th>
                    </tr>
                    <tr>
                        <th colspan="4">Total:</th>
                        <th id="th-total"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Variables
    let filasSubtotal = document.getElementsByClassName('td-subtotal');
    let cont = 0;
    let impuesto = $('#input-impuesto').val();

    $(document).ready(function() {
        calcularValores();
    });

    function calcularValores() {
        for (let i = 0; i < filasSubtotal.length; i++) {
            cont += parseFloat(filasSubtotal[i].innerHTML);
        }

        let igv = parseFloat(impuesto);

        $('#th-suma').html(cont.toFixed(2));
        $('#th-igv').html(igv.toFixed(2));
        $('#th-total').html((cont + igv).toFixed(2));
    }
</script>
@endpush