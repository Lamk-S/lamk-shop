<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Arqueo Caja #{{ $sesionCaja->id }}</title>
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
        <p>REPORTE DE CIERRE DE CAJA</p>
    </div>

    <div class="linea-doble"></div>

    <div class="mb-1">
        <p><span class="bold">TURNO:</span> #{{ str_pad($sesionCaja->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p><span class="bold">CAJA:</span> {{ $sesionCaja->caja?->nombre ?? 'N/A' }}</p>
        <p><span class="bold">CAJERO:</span> {{ $sesionCaja->user?->name ?? 'N/A' }}</p>
        <p><span class="bold">APERTURA:</span> {{ $sesionCaja->fecha_hora_apertura ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_apertura)->format('d/m/Y H:i') : '—' }}</p>
        <p><span class="bold">CIERRE:</span> {{ $sesionCaja->fecha_hora_cierre ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_cierre)->format('d/m/Y H:i') : 'EN CURSO' }}</p>
    </div>

    <div class="linea-doble"></div>
    <div class="text-center bold mb-1">1. EFECTIVO EN GAVETA (FÍSICO)</div>
    <div class="linea"></div>

    <table>
        <tr>
            <td>(+) Saldo Inicial:</td>
            <td class="text-right">S/ {{ number_format($sesionCaja->saldo_inicial, 2) }}</td>
        </tr>
        <tr>
            <td>(+) Efectivo Recibido:</td>
            <td class="text-right">S/ {{ number_format($totales['ventas_efectivo_bruto'], 2) }}</td>
        </tr>
        
        @if($totales['vueltos_totales'] > 0)
        <tr>
            <td>(-) Vueltos Entregados:</td>
            <td class="text-right">S/ {{ number_format($totales['vueltos_totales'], 2) }}</td>
        </tr>
        @endif
        
        <tr>
            <td>(+) Ingresos Manuales:</td>
            <td class="text-right">S/ {{ number_format($totales['ingresos_manuales'], 2) }}</td>
        </tr>
        <tr>
            <td>(-) Egresos / Retiros:</td>
            <td class="text-right">S/ {{ number_format($totales['egresos_manuales'], 2) }}</td>
        </tr>
    </table>
    <div class="linea"></div>
    <table>
        <tr>
            <td class="bold fs-14">EFECTIVO ESPERADO:</td>
            <td class="text-right bold fs-14">S/ {{ number_format($sesionCaja->saldo_final_esperado ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>EFECTIVO DECLARADO:</td>
            <td class="text-right">
                @if($sesionCaja->estado_sesion->value === 'ABIERTA')
                    (EN CURSO)
                @else
                    S/ {{ number_format($sesionCaja->saldo_final_declarado ?? 0, 2) }}
                @endif
            </td>
        </tr>
    </table>

    <div class="linea"></div>
    <table>
        <tr>
            <td class="bold">DIFERENCIA (Falt/Sob):</td>
            <td class="text-right bold">
                @if($sesionCaja->estado_sesion->value === 'ABIERTA')
                    ---
                @else
                    @php $dif = $sesionCaja->diferencia ?? 0; @endphp
                    {{ $dif > 0 ? '+' : '' }}S/ {{ number_format($dif, 2) }}
                @endif
            </td>
        </tr>
    </table>

    <br>
    <div class="linea-doble"></div>
    <div class="text-center bold mb-1">2. OTROS MEDIOS (DIGITALES/BANCOS)</div>
    <div class="linea"></div>

    <table>
        @forelse($totales['pagos_digitales'] as $metodo => $monto)
            <tr>
                <td>(•) {{ $metodo }}:</td>
                <td class="text-right">S/ {{ number_format($monto, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center">No hubo pagos por medios digitales</td>
            </tr>
        @endforelse
    </table>
    <div class="linea"></div>
    <table>
        <tr>
            <td class="bold">TOTAL DIGITALES:</td>
            <td class="text-right bold">S/ {{ number_format($totales['total_digital'], 2) }}</td>
        </tr>
    </table>

    <br>
    <div class="linea-doble"></div>
    <div class="text-center bold mb-1">RESUMEN GENERAL</div>
    <div class="linea"></div>
    <table>
        <tr>
            <td class="bold">TOTAL VENTAS GLOBAL:</td>
            <td class="text-right bold">S/ {{ number_format($totales['ventas_global'], 2) }}</td>
        </tr>
    </table>

    <br><br><br>
    
    <!-- Espacio para firmas -->
    <div class="text-center" style="margin-top: 20px;">
        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
            Firma del Cajero<br>
            {{ $sesionCaja->user?->name }}
        </div>
    </div>
    <br><br>
    <div class="text-center" style="margin-top: 10px;">
        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
            Auditoría / Supervisor<br>
            {{ $sesionCaja->userCierre?->name ?? '____________________' }}
        </div>
    </div>
    
    <br>
    <div class="text-center" style="font-size: 10px;">
        Impreso: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>