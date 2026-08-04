<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimiento Kardex #{{ $kardex->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Courier New', Courier, monospace; }
        @media print {
            @page { margin: 0; size: 80mm auto; }
            body { width: 100%; margin: 0; padding: 2mm; }
        }
        body { width: 80mm; margin: 0 auto; color: #000; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .linea { border-bottom: 1px dashed #000; margin: 5px 0; }
        .linea-doble { border-bottom: 2px solid #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
    </style>
</head>
<body>
    <div class="text-center mb-2">
        <h2 class="bold">{{ $empresa->nombre_comercial ?? 'MI EMPRESA' }}</h2>
        <p>VOUCHER DE ALMACÉN</p>
    </div>

    <div class="linea-doble"></div>

    @php
        $tipo = $kardex->tipo_transaccion->value ?? $kardex->tipo_transaccion;
        $variante = $kardex->productoVariante;
        $producto = $variante?->producto;
    @endphp

    <div class="mb-1">
        <p><span class="bold">N° OPERACIÓN:</span> #{{ str_pad($kardex->id, 8, '0', STR_PAD_LEFT) }}</p>
        <p><span class="bold">TIPO:</span> {{ $tipo }}</p>
        <p><span class="bold">FECHA:</span> {{ optional($kardex->created_at)->format('d/m/Y H:i') }}</p>
        <p><span class="bold">OPERADOR:</span> {{ $kardex->user?->name ?? 'Sistema' }}</p>
    </div>

    <div class="linea-doble"></div>
    <div class="text-center bold mb-1">DETALLE DEL ARTÍCULO</div>
    <div class="linea"></div>

    <div class="mb-1">
        <p class="bold">{{ $producto?->nombre ?? 'Artículo Inválido' }}</p>
        <p>SKU: {{ $producto?->codigo ?? 'N/A' }}</p>
        <p>TALLA: {{ $variante?->talla?->nombre ?? 'N/A' }}</p>
        <p>VAR: {{ $variante?->codigo_variante ?? 'N/A' }}</p>
    </div>

    <div class="linea"></div>
    <div class="text-center bold mb-1">MOVIMIENTO</div>
    <div class="linea"></div>

    <table>
        <tr>
            <td>Ingreso (Unidades):</td>
            <td class="text-right">{{ number_format($kardex->entrada, 0) }}</td>
        </tr>
        <tr>
            <td>Salida (Unidades):</td>
            <td class="text-right">{{ number_format($kardex->salida, 0) }}</td>
        </tr>
        <tr>
            <td class="bold fs-14">SALDO FINAL:</td>
            <td class="text-right bold fs-14">{{ number_format($kardex->saldo_posterior, 0) }}</td>
        </tr>
    </table>

    <div class="linea"></div>
    <div class="text-center bold mb-1">GLOSA / MOTIVO</div>
    <div class="linea"></div>
    
    <p style="font-size: 11px; text-align: justify;">
        {{ $kardex->descripcion }}
    </p>

    <br><br><br>
    
    <!-- Espacio para firmas -->
    <div class="text-center" style="margin-top: 20px;">
        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
            Almacén / Logística<br>
            {{ $kardex->user?->name }}
        </div>
    </div>
    <br><br>
    <div class="text-center" style="margin-top: 10px;">
        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
            Auditor / Supervisor<br>
            ____________________
        </div>
    </div>
    
    <br>
    <div class="text-center" style="font-size: 10px;">
        Impreso: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>