@extends('layouts.app')
@section('title', 'Comando de Tienda')

@push('css')
<style>
    .dashboard-title { font-weight: 900; letter-spacing: -.03em; color: #0f172a; }
    .dashboard-card { border: 0; border-radius: 1.5rem; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; overflow: hidden; }
    .dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1); }
    .kpi-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; font-weight: 700; margin-bottom: 0.5rem; }
    .kpi-value { font-size: 1.8rem; font-weight: 900; color: #1e293b; font-family: ui-monospace, monospace; letter-spacing: -0.05em; }
    .icon-wrapper { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.25rem; }
    .section-header { font-weight: 800; color: #0f172a; font-size: 1.1rem; }
    .table-stock th { font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; border-bottom: 2px solid #e2e8f0; }
    .table-stock td { font-weight: 600; color: #334155; vertical-align: middle; }
    .pos-shortcut { background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%); color: white; }
    .pos-shortcut:hover { opacity: 0.95; transform: translateY(-4px); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="dashboard-title mb-0">
                @hasrole('administrador') Comando Central @else ¡Hola, {{ auth()->user()->name }}! @endhasrole
            </h2>
            <p class="text-muted mb-0 fs-6">
                @hasrole('administrador') Visión en tiempo real de las operaciones de la tienda. @else Resumen de tus operaciones del día. @endhasrole
            </p>
        </div>
        <div>
            <span class="badge bg-white text-dark border shadow-sm px-4 py-2 fs-6 rounded-pill fw-medium">
                <i class="fas fa-calendar-day text-info me-2"></i> {{ now()->translatedFormat('l d \d\e F, Y') }}
            </span>
        </div>
    </div>

    <!-- ==========================================
         VISTA: ADMINISTRADOR (Visión Total)
    =========================================== -->
    @hasrole('administrador')
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="kpi-title">Ingresos de Hoy</div>
                                <div class="kpi-value text-success">S/ {{ number_format((float) ($kpis['ventas_hoy'] ?? 0), 2) }}</div>
                            </div>
                            <div class="icon-wrapper bg-success bg-opacity-10 text-success">
                                <i class="fas fa-hand-holding-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="kpi-title">Terminales Activas</div>
                                <div class="kpi-value text-dark">{{ $kpis['sesiones_activas'] ?? 0 }} <span class="fs-6 text-muted fw-normal">en curso</span></div>
                            </div>
                            <div class="icon-wrapper bg-info bg-opacity-10 text-info">
                                <i class="fas fa-cash-register"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="kpi-title text-warning">Alertas de Stock</div>
                                <div class="kpi-value text-warning">{{ $kpis['productos_stock_bajo'] ?? 0 }} <span class="fs-6 text-muted fw-normal">artículos</span></div>
                            </div>
                            <div class="icon-wrapper bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="kpi-title">Gastos del Día</div>
                                <div class="kpi-value text-danger">S/ {{ number_format((float) ($kpis['compras_hoy'] ?? 0), 2) }}</div>
                            </div>
                            <div class="icon-wrapper bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="section-header">Fluctuación Comercial (7 Días)</div>
                        <div class="text-muted small">Comparativa de prendas/artículos vendidos vs reabastecimiento.</div>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="ventasComprasChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-card bg-dark text-white h-100 relative overflow-hidden">
                    <div class="position-absolute opacity-10 end-0 bottom-0 mb-n4 me-n4">
                        <i class="fas fa-vault fa-10x"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-center">
                        <h4 class="fw-bold mb-4">Bóveda de Tesorería</h4>
                        
                        <div class="mb-4">
                            <div class="text-uppercase tracking-wider small text-white-50 fw-bold mb-1"><i class="fas fa-money-bill-wave me-2"></i>Efectivo en Caja Fuerte</div>
                            <h2 class="fw-black mb-0 font-monospace">S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0), 2) }}</h2>
                        </div>

                        <div>
                            <div class="text-uppercase tracking-wider small text-white-50 fw-bold mb-1"><i class="fas fa-building-columns me-2"></i>Cuentas Bancarias</div>
                            <h2 class="fw-black mb-0 font-monospace text-info">S/ {{ number_format((float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="section-header"><i class="fas fa-box-open text-warning me-2"></i>Artículos por Agotarse</div>
                            <div class="text-muted small">Zapatillas y accesorios con stock crítico (≤ 10 uds).</div>
                        </div>
                        <a href="{{ route('kardex.index') }}" class="btn btn-sm btn-light fw-bold text-primary">Ver Kardex</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-stock mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Prenda / Modelo</th>
                                        <th class="text-end pe-4">Stock Restante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockBajo as $producto)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center text-secondary" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-tag"></i>
                                                    </div>
                                                    {{ Str::limit($producto->nombre, 45) }}
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fs-6 tabular-nums shadow-sm">
                                                    {{ (int) ($producto->stock_total_calc ?? 0) }} Unds.
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-5">
                                                <div class="text-success mb-2"><i class="fas fa-check-circle fa-2x"></i></div>
                                                <div class="fw-bold">Almacén Abastecido</div>
                                                <div class="text-muted small">No hay productos en riesgo de quiebre de stock.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="section-header">Preferencia de Pago (Ventas)</div>
                        <div class="text-muted small">Cómo están pagando tus clientes.</div>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center pb-4">
                        <canvas id="metodosPagoVentasChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole

    <!-- ==========================================
         VISTA: VENDEDOR (Enfoque Comercial)
    =========================================== -->
    @hasrole('vendedor')
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card dashboard-card pos-shortcut h-100 border-0">
                    <div class="card-body p-5 d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle mb-4 d-flex justify-content-center align-items-center shadow-sm" style="width: 80px; height: 80px;">
                            <i class="fas fa-shopping-bag fa-3x text-white"></i>
                        </div>
                        <h2 class="fw-bold text-white mb-3">Punto de Venta Activo</h2>
                        <p class="text-white-50 mb-4 fs-5">Accede rápidamente al catálogo y registra las compras de los clientes.</p>
                        <a href="{{ route('ventas.create') }}" class="btn btn-light btn-lg px-5 shadow rounded-pill fw-bold text-primary">
                            <i class="fas fa-plus me-2"></i> Iniciar Nueva Venta
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="kpi-title text-success mb-3">Mis Indicadores de Hoy</div>
                        
                        <div class="p-3 bg-success bg-opacity-10 rounded-3 mb-3 border border-success border-opacity-25 flex-grow-1 d-flex flex-column justify-content-center">
                            <div class="small text-success text-uppercase fw-bold mb-1">Total Ingresado</div>
                            <h3 class="fw-bold text-success mb-0">S/ {{ number_format((float) ($kpis['ventas_hoy'] ?? 0), 2) }}</h3>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <a href="{{ route('ventas.index') }}" class="text-decoration-none text-secondary d-flex justify-content-between align-items-center">
                                <span class="fw-medium"><i class="fas fa-history me-2"></i>Ver mi historial de ventas</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole

    <!-- ==========================================
         VISTA: CAJERO (Enfoque Financiero)
    =========================================== -->
    @hasrole('cajero')
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-dark mb-4">Estado de tu Terminal</h4>
                        
                        @if(($kpis['sesiones_activas'] ?? 0) > 0)
                            <div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
                                <i class="fas fa-check-circle fa-3x me-3 opacity-75"></i>
                                <div>
                                    <div class="fw-bold fs-5">Caja Operativa</div>
                                    <div class="small opacity-75">Tu turno está activo y listo para procesar pagos y operaciones.</div>
                                </div>
                            </div>
                            <div class="d-grid gap-3">
                                <a href="{{ route('ventas.index') }}" class="btn btn-success btn-lg fw-medium shadow-sm"><i class="fas fa-file-invoice-dollar me-2"></i>Ir a Cobranzas</a>
                                <a href="{{ route('movimientos-caja.create') }}" class="btn btn-outline-secondary fw-medium"><i class="fas fa-exchange-alt me-2"></i>Registrar Movimiento Extra</a>
                            </div>
                        @else
                            <div class="alert alert-danger d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
                                <i class="fas fa-lock fa-3x me-3 opacity-75"></i>
                                <div>
                                    <div class="fw-bold fs-5">Caja Cerrada</div>
                                    <div class="small opacity-75">Debes aperturar una sesión para comenzar a operar.</div>
                                </div>
                            </div>
                            <div class="d-grid gap-3">
                                <a href="{{ route('sesiones-caja.create') }}" class="btn btn-primary btn-lg fw-medium shadow-sm"><i class="fas fa-key me-2"></i>Abrir Turno de Caja</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <div class="kpi-title text-info">Terminales Generales Activas</div>
                                <div class="kpi-value text-dark">{{ $kpis['sesiones_activas'] ?? 0 }} <span class="fs-6 text-muted fw-normal">en tienda</span></div>
                            </div>
                            <div class="icon-wrapper bg-info bg-opacity-10 text-info">
                                <i class="fas fa-desktop"></i>
                            </div>
                        </div>
                        <hr class="text-muted">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Recuerda declarar el saldo exacto al finalizar tu turno en la opción de Cierre de Caja.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole

    <!-- ==========================================
         VISTA: ENCARGADO DE ALMACÉN (Logística)
    =========================================== -->
    @hasrole('encargado_almacen')
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="kpi-title text-danger">Gastos en Abastecimiento (Hoy)</div>
                                <div class="kpi-value text-danger">S/ {{ number_format((float) ($kpis['compras_hoy'] ?? 0), 2) }}</div>
                            </div>
                            <div class="icon-wrapper bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-boxes-packing"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('compras.create') }}" class="btn btn-outline-danger w-100 fw-medium">
                                <i class="fas fa-plus me-2"></i>Registrar Nueva Compra
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="kpi-title text-warning">Alertas Críticas de Stock</div>
                                <div class="kpi-value text-warning">{{ $kpis['productos_stock_bajo'] ?? 0 }} <span class="fs-6 text-muted fw-normal">artículos</span></div>
                            </div>
                            <div class="icon-wrapper bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('kardex.index') }}" class="btn btn-outline-warning w-100 fw-medium text-dark">
                                <i class="fas fa-clipboard-list me-2"></i>Revisar Kardex
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="section-header"><i class="fas fa-box-open text-warning me-2"></i>Artículos que requieren reabastecimiento</div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-stock mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Código / Prenda</th>
                                        <th class="text-end pe-4">Stock Restante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockBajo as $producto)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center text-secondary fw-bold" style="padding: 0.5rem; font-size: 0.75rem;">
                                                        {{ $producto->codigo }}
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $producto->nombre }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fs-6 tabular-nums shadow-sm">
                                                    {{ (int) ($producto->stock_total_calc ?? 0) }} Unds.
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-5">
                                                <div class="text-success mb-2"><i class="fas fa-check-circle fa-2x"></i></div>
                                                <div class="fw-bold">Almacén Abastecido</div>
                                                <div class="text-muted small">No hay productos en riesgo de quiebre de stock.</div>
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

<!-- Solo cargamos los datos de los gráficos si existen para evitar errores en la consola -->
@if(isset($ventasCompras) && isset($metodosPagoVentas))
    <script id="ventas-compras-data" type="application/json">
    {!! json_encode($ventasCompras, JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script id="metodos-ventas-data" type="application/json">
    {!! json_encode($metodosPagoVentas, JSON_UNESCAPED_UNICODE) !!}
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.font.family = "'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
            Chart.defaults.color = '#64748b';

            const ventasChartEl = document.getElementById('ventasComprasChart');
            const metodosChartEl = document.getElementById('metodosPagoVentasChart');

            if (ventasChartEl) {
                const ventasCompras = JSON.parse(document.getElementById('ventas-compras-data').textContent);
                new Chart(ventasChartEl, {
                    type: 'line',
                    data: {
                        labels: ventasCompras.map(x => x.fecha),
                        datasets: [
                            { 
                                label: 'Ingresos por Ventas (S/)', 
                                data: ventasCompras.map(x => x.ventas),
                                borderColor: '#0dcaf0',
                                backgroundColor: 'rgba(13, 202, 240, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true
                            },
                            { 
                                label: 'Inversión en Compras (S/)', 
                                data: ventasCompras.map(x => x.compras),
                                borderColor: '#dc3545',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#e2e8f0' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            if (metodosChartEl) {
                const metodosPagoVentas = JSON.parse(document.getElementById('metodos-ventas-data').textContent);
                new Chart(metodosChartEl, {
                    type: 'doughnut',
                    data: {
                        labels: metodosPagoVentas.map(x => x.name),
                        datasets: [{
                            data: metodosPagoVentas.map(x => x.value),
                            backgroundColor: ['#0dcaf0', '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#6c757d'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '75%',
                        plugins: {
                            legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 10, padding: 20 } }
                        }
                    }
                });
            }
        });
    </script>
@endif
@endpush