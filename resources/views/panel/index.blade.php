@extends('layouts.app')
@section('title', 'Dashboard | Comando de Tienda')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-4">
                @hasrole('administrador') Comando Central @else ¡Hola, {{ auth()->user()->name }}! @endhasrole
            </h2>
            <p class="text-muted mb-0 fs-6">
                @hasrole('administrador') Resumen general de operaciones de la tienda. @else Resumen de tus operaciones de hoy. @endhasrole
            </p>
        </div>
        <div>
            <span class="bg-white text-dark border shadow-sm px-3 py-2 fs-6 rounded-2 fw-medium d-inline-flex align-items-center">
                <i class="fas fa-calendar-day text-primary me-2"></i> 
                {{ now()->translatedFormat('l d \d\e F, Y') }}
            </span>
        </div>
    </div>

    <!-- ==========================================
         VISTA: ADMINISTRADOR
    =========================================== -->
    @hasrole('administrador')
        <div class="row g-3 mb-4">
            <!-- KPIs -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Ingresos de Hoy</div>
                            <div class="fs-4 fw-bold text-dark">S/ {{ number_format((float) ($kpis['ventas_hoy'] ?? 0), 2) }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-hand-holding-dollar fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Terminales Activas</div>
                            <div class="fs-4 fw-bold text-dark">{{ $kpis['sesiones_activas'] ?? 0 }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-cash-register fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Alertas de Stock</div>
                            <div class="fs-4 fw-bold text-danger">{{ $kpis['productos_stock_bajo'] ?? 0 }}</div>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-triangle-exclamation fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Gastos del Día</div>
                            <div class="fs-4 fw-bold text-dark">S/ {{ number_format((float) ($kpis['compras_hoy'] ?? 0), 2) }}</div>
                        </div>
                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-truck-loading fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Gráfico de Fluctuación -->
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-0 pt-3 px-3 pb-0">
                        <h6 class="fw-bold text-dark mb-0">Fluctuación Comercial (Últimos 7 Días)</h6>
                    </div>
                    <div class="card-body p-3">
                        <div style="min-height: 250px;">
                            <canvas id="ventasComprasChart" width="100%" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bóveda Tesorería -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 bg-dark text-white h-100 overflow-hidden position-relative">
                    <!-- Icono de fondo sutil -->
                    <i class="fas fa-vault position-absolute bottom-0 end-0 mb-n3 me-n3 text-white opacity-10" style="font-size: 7rem; pointer-events: none;"></i>
                    
                    <div class="card-body p-4 d-flex flex-column position-relative z-1">
                        <!-- Cabecera y Total General Sumado -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-white-50 mb-1 text-uppercase small tracking-wide">Tesorería General (Total)</h6>
                            <h3 class="fw-bold text-white mb-0">
                                S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0) + (float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}
                            </h3>
                        </div>

                        <!-- Desglose de Cuentas -->
                        <div class="d-flex flex-column gap-3 mt-auto">
                            
                            <!-- Ítem: Efectivo -->
                            <div class="bg-white bg-opacity-10 rounded-3 p-3 d-flex align-items-center border border-white border-opacity-10 shadow-sm">
                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px;">
                                    <i class="fas fa-money-bill-wave text-white"></i>
                                </div>
                                <div>
                                    <div class="small text-white-50 fw-semibold mb-1" style="line-height: 1;">Efectivo (Caja Fuerte)</div>
                                    <div class="fw-bold fs-6">S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0), 2) }}</div>
                                </div>
                            </div>

                            <!-- Ítem: Bancos -->
                            <div class="bg-white bg-opacity-10 rounded-3 p-3 d-flex align-items-center border border-white border-opacity-10 shadow-sm">
                                <div class="bg-info bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px;">
                                    <i class="fas fa-building-columns text-info"></i>
                                </div>
                                <div>
                                    <div class="small text-white-50 fw-semibold mb-1" style="line-height: 1;">Cuentas Bancarias</div>
                                    <div class="fw-bold fs-6 text-info">S/ {{ number_format((float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}</div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Preferencia de Pago -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom pt-3 px-3 pb-2">
                        <h6 class="fw-bold text-dark mb-0">Métodos de Pago Utilizados</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center p-3">
                        <div style="width: 100%; max-width: 300px; margin: 0 auto;">
                            <canvas id="metodosPagoVentasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Artículos por Agotarse -->
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom pt-3 px-3 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-danger mb-0"><i class="fas fa-box-open me-1"></i> Artículos con Stock Crítico</h6>
                        <a href="{{ route('kardex.index') }}" class="btn btn-sm btn-outline-secondary border-0">Ver Kardex <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 285px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3 text-muted small fw-bold">Producto</th>
                                        <th class="text-end pe-3 text-muted small fw-bold">Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockBajo ?? [] as $producto)
                                        <tr>
                                            <td class="ps-3 text-dark">{{ Str::limit($producto->nombre, 55) }}</td>
                                            <td class="text-end pe-3">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                    {{ (int) ($producto->stock_total_calc ?? 0) }} Unds.
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">
                                                <i class="fas fa-check-circle text-success fs-4 mb-2"></i>
                                                <div class="fw-bold">Stock Saludable</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole


    <!-- ==========================================
         VISTA: CAJERO
    =========================================== -->
    @hasrole('cajero')
        @php $cajaActiva = $sesionAbierta ?? null; @endphp
        
        <h5 class="fw-bold text-secondary mb-3 mt-2"><i class="fas fa-cash-register me-2"></i> Terminal de Caja</h5>
        <div class="row g-3 mb-4">
            
            <!-- Estado de Caja -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3">
                        <h6 class="text-uppercase fw-bold text-muted mb-3">Estado del Turno</h6>
                        @if($cajaActiva)
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success px-3 py-2 fs-6 rounded-2"><i class="fas fa-lock-open me-1"></i> ABIERTA</span>
                            </div>
                            <div class="small text-dark fw-bold mb-1">{{ $cajaActiva->caja->nombre ?? 'Caja' }}</div>
                            <div class="small text-muted">Apertura: {{ \Carbon\Carbon::parse($cajaActiva->fecha_hora_apertura)->format('h:i A') }}</div>
                        @else
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-danger px-3 py-2 fs-6 rounded-2"><i class="fas fa-lock me-1"></i> CERRADA</span>
                            </div>
                            <div class="small text-muted mt-2">Apertura requerida para facturar.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- KPIs Cajero -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Cobros de Hoy</div>
                            <div class="fs-3 fw-bold text-dark">S/ {{ number_format((float) ($kpis['ventas_hoy'] ?? 0), 2) }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-coins fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3">
                        <h6 class="text-uppercase fw-bold text-muted mb-3">Operaciones</h6>
                        <div class="d-grid gap-2">
                            @if($cajaActiva)
                                <a href="{{ route('ventas.create') }}" class="btn btn-primary text-start fw-medium"><i class="fas fa-shopping-cart me-2"></i> Ir al Punto de Venta</a>
                            @else
                                <a href="{{ route('sesiones-caja.create') }}" class="btn btn-danger text-start fw-medium"><i class="fas fa-key me-2"></i> Aperturar Caja</a>
                            @endif
                            <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light border text-start fw-medium text-dark"><i class="fas fa-file-invoice me-2"></i> Historial de Turnos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole


    <!-- ==========================================
         VISTA: VENDEDOR
    =========================================== -->
    @hasrole('vendedor')
        @php $cajaActiva = $sesionAbierta ?? null; @endphp

        <h5 class="fw-bold text-secondary mb-3 mt-2"><i class="fas fa-user-tag me-2"></i> Panel de Ventas</h5>
        <div class="row g-3 mb-4">
            
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Mis Ventas Hoy</div>
                            <div class="fs-3 fw-bold text-primary">S/ {{ number_format((float) ($kpis['mis_ventas_hoy'] ?? 0), 2) }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-award fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Clientes Atendidos</div>
                            <div class="fs-3 fw-bold text-info">{{ $kpis['mis_clientes_hoy'] ?? 0 }}</div>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-users fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 {{ $cajaActiva ? 'bg-dark' : 'bg-danger text-white' }}">
                    <div class="card-body p-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-uppercase fw-bold text-white-50 mb-0">Caja de Turno</h6>
                            @if($cajaActiva)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Abierta</span>
                            @else
                                <span class="badge bg-white text-danger"><i class="fas fa-ban me-1"></i> Cerrada</span>
                            @endif
                        </div>
                        
                        <div class="d-grid gap-2">
                            @if($cajaActiva)
                                <a href="{{ route('ventas.create') }}" class="btn btn-light fw-bold text-dark text-start">
                                    <i class="fas fa-plus-circle me-2"></i> Nueva Venta
                                </a>
                            @else
                                <button class="btn btn-light text-danger fw-bold text-start opacity-75" disabled>
                                    <i class="fas fa-lock me-2"></i> Venta Bloqueada
                                </button>
                            @endif
                            <a href="{{ route('productos.index') }}" class="btn btn-outline-light text-start">
                                <i class="fas fa-search me-2"></i> Buscar Productos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole


    <!-- ==========================================
         VISTA: ENCARGADO DE ALMACÉN
    =========================================== -->
    @hasrole('encargado_almacen')
        <h5 class="fw-bold text-secondary mb-3 mt-2"><i class="fas fa-warehouse me-2"></i> Gestión de Inventario</h5>
        <div class="row g-3 mb-4">
            
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Total de Productos</div>
                            <div class="fs-3 fw-bold text-dark">{{ $kpis['total_productos'] ?? 0 }}</div>
                        </div>
                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-boxes fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1">Entradas (Hoy)</div>
                            <div class="fs-3 fw-bold text-dark">{{ $kpis['compras_hoy_cantidad'] ?? 0 }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-arrow-right-to-bracket fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-light">
                    <div class="card-body p-3 d-flex flex-column justify-content-center">
                        <h6 class="text-uppercase fw-bold text-muted mb-3">Accesos Directos</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('compras.create') }}" class="btn btn-success fw-bold text-start">
                                <i class="fas fa-truck-loading me-2"></i> Ingresar Mercadería
                            </a>
                            <a href="{{ route('kardex.index') }}" class="btn btn-outline-secondary bg-white text-start">
                                <i class="fas fa-clipboard-list me-2"></i> Revisar Kardex
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom pt-3 px-3 pb-2">
                        <h6 class="fw-bold text-danger mb-0"><i class="fas fa-exclamation-circle me-1"></i> Alerta de Reabastecimiento (Stock Bajo)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3 text-muted small fw-bold">Producto</th>
                                        <th class="text-center text-muted small fw-bold">Mínimo Permitido</th>
                                        <th class="text-end pe-3 text-muted small fw-bold">Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockBajo ?? [] as $producto)
                                        <tr>
                                            <td class="ps-3 text-dark fw-medium">
                                                {{ Str::limit($producto->nombre, 60) }}
                                                @if($producto->maneja_tallas && isset($producto->talla_nombre))
                                                    <span class="badge bg-secondary ms-1">{{ $producto->talla_nombre }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-muted fw-bold">{{ $producto->stock_minimo ?? 0 }}</td>
                                            <td class="text-end pe-3">
                                                <span class="badge bg-danger text-white px-2 py-1 rounded-2">
                                                    {{ (int) ($producto->stock_total_calc ?? 0) }} Unds.
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                                                <div class="fw-bold">Inventario Saludable</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(isset($ventasCompras) || isset($metodosPagoVentas))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ventasCompras = @json($ventasCompras ?? []);
            const metodosPagoVentas = @json($metodosPagoVentas ?? []);

            Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
            Chart.defaults.color = '#6c757d';

            const ventasChartEl = document.getElementById('ventasComprasChart');
            const metodosChartEl = document.getElementById('metodosPagoVentasChart');

            if (ventasChartEl && ventasCompras.length > 0) {
                new Chart(ventasChartEl, {
                    type: 'line',
                    data: {
                        labels: ventasCompras.map(x => x.fecha),
                        datasets: [
                            { 
                                label: 'Ventas (S/)', 
                                data: ventasCompras.map(x => x.ventas),
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                                borderWidth: 2,
                                tension: 0.2,
                                fill: true,
                                pointRadius: 3
                            },
                            { 
                                label: 'Compras (S/)', 
                                data: ventasCompras.map(x => x.compras),
                                borderColor: '#dc3545',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [4, 4],
                                tension: 0.2,
                                pointRadius: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 6 } } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f8f9fa' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } else if (ventasChartEl) {
                ventasChartEl.parentElement.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-line fs-3 mb-2 opacity-50"></i><br><small>Sin datos de los últimos 7 días</small></div>';
            }

            if (metodosChartEl && metodosPagoVentas.length > 0) {
                const colorMap = {
                    'Efectivo': '#198754',
                    'Tarjeta (Débito/Crédito)': '#0d6efd',
                    'Yape': '#6f42c1',
                    'Plin': '#0dcaf0',
                    'Transferencia Bancaria': '#fd7e14',
                    'Crédito': '#ffc107',
                    'Otro Método': '#adb5bd'
                };
                
                const bgColors = metodosPagoVentas.map(m => colorMap[m.name] || '#6c757d');

                new Chart(metodosChartEl, {
                    type: 'doughnut',
                    data: {
                        labels: metodosPagoVentas.map(x => x.name),
                        datasets: [{
                            data: metodosPagoVentas.map(x => x.value),
                            backgroundColor: bgColors,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '70%',
                        plugins: { legend: { position: 'right', labels: { usePointStyle: true, padding: 12, font: {size: 11} } } }
                    }
                });
            } else if(metodosChartEl) {
                metodosChartEl.parentElement.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-wallet fs-3 mb-2 opacity-50"></i><br><small>Sin pagos registrados</small></div>';
            }
        });
    </script>
@endif
@endpush