@extends('layouts.app')
@section('title', 'Comando de Tienda')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-0 fs-3">
                @hasrole('administrador') Comando Central @else ¡Hola, {{ auth()->user()->name }}! @endhasrole
            </h2>
            <p class="text-muted mb-0 fs-6">
                @hasrole('administrador') Visión en tiempo real de las operaciones de la tienda. @else Resumen de tus operaciones del día. @endhasrole
            </p>
        </div>
        <div>
            <span class="badge bg-white text-dark border shadow-sm px-4 py-2 fs-6 rounded-pill fw-medium">
                <i class="fas fa-calendar-day text-primary me-2"></i> {{ now()->translatedFormat('l d \d\e F, Y') }}
            </span>
        </div>
    </div>

    <!-- ==========================================
         VISTA: ADMINISTRADOR (Visión Total)
    =========================================== -->
    @hasrole('administrador')
        <div class="row g-3 mb-4">
            <!-- KPIs -->
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1 tracking-wide">Ingresos de Hoy</div>
                            <div class="fs-3 fw-bold text-dark font-monospace">S/ {{ number_format((float) ($kpis['ventas_hoy'] ?? 0), 2) }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-hand-holding-dollar fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1 tracking-wide">Terminales Activas</div>
                            <div class="fs-3 fw-bold text-dark font-monospace">{{ $kpis['sesiones_activas'] ?? 0 }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-cash-register fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-warning mb-1 tracking-wide">Alertas de Stock</div>
                            <div class="fs-3 fw-bold text-dark font-monospace">{{ $kpis['productos_stock_bajo'] ?? 0 }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-triangle-exclamation fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-1 tracking-wide">Gastos del Día</div>
                            <div class="fs-3 fw-bold text-dark font-monospace">S/ {{ number_format((float) ($kpis['compras_hoy'] ?? 0), 2) }}</div>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-truck-loading fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Gráfico de Fluctuación -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Fluctuación Comercial (7 Días)</h5>
                        <p class="text-muted small mb-0">Comparativa de prendas vendidas vs reabastecimiento.</p>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="ventasComprasChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bóveda Tesorería -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white h-100 overflow-hidden position-relative">
                    <i class="fas fa-vault position-absolute bottom-0 end-0 mb-n3 me-n3 text-white opacity-10" style="font-size: 8rem;"></i>
                    <div class="card-body p-4 d-flex flex-column justify-content-center position-relative z-1">
                        <h5 class="fw-bold mb-4">Bóveda de Tesorería</h5>
                        
                        <div class="mb-4">
                            <div class="text-uppercase small text-white-50 fw-bold mb-1"><i class="fas fa-money-bill-wave me-2"></i>Efectivo (Caja Fuerte)</div>
                            <h2 class="fw-bold mb-0 font-monospace">S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0), 2) }}</h2>
                        </div>

                        <div>
                            <div class="text-uppercase small text-white-50 fw-bold mb-1"><i class="fas fa-building-columns me-2"></i>Cuentas Bancarias</div>
                            <h2 class="fw-bold mb-0 font-monospace text-info">S/ {{ number_format((float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Artículos por Agotarse -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-box-open text-warning me-2"></i>Artículos Críticos</h5>
                            <p class="text-muted small mb-0">Zapatillas y accesorios con stock ≤ 10 uds.</p>
                        </div>
                        <a href="{{ route('kardex.index') }}" class="btn btn-sm btn-light border fw-medium text-dark shadow-sm">Ver Kardex</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 text-muted small text-uppercase fw-bold">Prenda / Modelo</th>
                                        <th class="text-end pe-4 text-muted small text-uppercase fw-bold">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockBajo as $producto)
                                        <tr>
                                            <td class="ps-4 text-dark fw-medium">{{ Str::limit($producto->nombre, 45) }}</td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1 rounded-pill">
                                                    {{ (int) ($producto->stock_total_calc ?? 0) }} Unds.
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">
                                                <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                                                <div class="fw-bold text-dark">Almacén Abastecido</div>
                                                <div class="small">No hay productos en riesgo.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferencia de Pago -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                        <h5 class="fw-bold text-dark mb-0">Métodos de Pago</h5>
                        <p class="text-muted small mb-0">Distribución de ingresos por ventas.</p>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center p-4">
                        <canvas id="metodosPagoVentasChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endhasrole
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(isset($ventasCompras) && isset($metodosPagoVentas))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ventasCompras = {{ Illuminate\Support\Js::from($ventasCompras) }};
            const metodosPagoVentas = {{ Illuminate\Support\Js::from($metodosPagoVentas) }};

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
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            { 
                                label: 'Compras (S/)', 
                                data: ventasCompras.map(x => x.compras),
                                borderColor: '#dc3545',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f8f9fa' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
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
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '75%',
                        plugins: { legend: { position: 'right', labels: { usePointStyle: true, padding: 15 } } }
                    }
                });
            } else if(metodosChartEl) {
                metodosChartEl.parentElement.innerHTML = '<div class="text-muted small text-center w-100 py-4"><i class="fas fa-chart-pie fs-3 mb-2 opacity-50"></i><br>Aún no hay transacciones para analizar.</div>';
            }
        });
    </script>
@endif
@endpush