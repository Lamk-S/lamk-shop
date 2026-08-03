<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $venta->serie }}-{{ $venta->correlativo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Courier New', Courier, monospace; }
        
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                width: 100%;
                margin: 0;
                padding: 2mm;
            }
        }

        body { width: 80mm; margin: 0 auto; color: #000; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .linea { border-bottom: 1px dashed #000; margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; vertical-align: top; padding: 2px 0; }
        .td-qty { width: 12%; }
        .td-price { width: 20%; text-align: right; }
        .td-total { width: 25%; text-align: right; }
    </style>
</head>
<body>
    <div class="text-center mb-2">
        <!-- Datos Dinámicos de la Empresa -->
        <h2 class="bold">{{ $empresa->razon_social ?? 'MI EMPRESA' }}</h2>
        <p>{{ $empresa->nombre_comercial ?? '' }}</p>
        <p>RUC: {{ $empresa->ruc ?? '00000000000' }}</p>
        <p>{{ $empresa->direccion_fiscal ?? 'Dirección no registrada' }}</p>
    </div>

    <div class="linea"></div>

    <div class="mb-1">
        <p><span class="bold">Comprobante:</span> {{ $venta->tipo_comprobante?->value ?? 'TICKET' }} {{ $venta->serie }}-{{ $venta->correlativo }}</p>
        <p><span class="bold">Fecha/Hora:</span> {{ $venta->fecha_emision->format('d/m/Y H:i') }}</p>
        <p><span class="bold">Cajero(a):</span> {{ $venta->user?->name ?? 'N/A' }}</p>
        <p><span class="bold">Cliente:</span> {{ $venta->cliente_nombre ?? 'PUBLICO GENERAL' }}</p>
        @if($venta->cliente_numero_documento)
            <p><span class="bold">Doc:</span> {{ $venta->cliente_numero_documento }}</p>
        @endif
    </div>

    <div class="linea"></div>

    <table class="mb-2">
        <thead>
            <tr class="linea">
                <th>CANT</th>
                <th>DESCRIPCIÓN</th>
                <th class="td-total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $item)
            <tr>
                <td class="td-qty">{{ $item->cantidad }}</td>
                <td>
                    {{ substr($item->producto_nombre, 0, 16) }}
                    <br><small>Talla: {{ $item->talla_nombre ?? 'Unica' }}</small>
                </td>
                <td class="td-total">S/ {{ number_format((float)$item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="linea"></div>

    <table>
        <tr>
            <td>SUBTOTAL:</td>
            <td class="text-right">S/ {{ number_format((float)$venta->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>DSCTO:</td>
            <td class="text-right">S/ {{ number_format((float)$venta->descuento_total, 2) }}</td>
        </tr>
        <tr>
            <td>IGV ({{ $empresa->igv_porcentaje ?? 18 }}%):</td>
            <td class="text-right">S/ {{ number_format((float)$venta->impuesto_total, 2) }}</td>
        </tr>
        <tr>
            <td class="bold fs-14">TOTAL A PAGAR:</td>
            <td class="text-right bold fs-14">S/ {{ number_format((float)$venta->total, 2) }}</td>
        </tr>
    </table>

    <div class="linea"></div>
    
    <div class="text-center mb-2">
        <p>Método de pago: {{ $venta->pagos->pluck('metodo_pago')->map(fn($m) => $m->value ?? $m)->unique()->implode(', ') ?: 'Efectivo' }}</p>
        <p>Recibido: S/ {{ number_format((float)$venta->monto_recibido, 2) }} | Vuelto: S/ {{ number_format((float)$venta->vuelto_entregado, 2) }}</p>
    </div>

    <!-- Mensaje dinámico de la base de datos -->
    <div class="text-center">
        <p class="bold">{{ $empresa->mensaje_ticket ?? '¡GRACIAS POR SU COMPRA!' }}</p>
        <p>Conserve este ticket para cualquier reclamo.</p>
    </div>
</body>
</html>