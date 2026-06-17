@extends('layouts.app')
@section('title', 'Panel de Control')

@push('css')
<style>
    .dashboard-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08); transition: transform .2s ease, box-shadow .2s ease; }
    .dashboard-card:hover { transform: translateY(-2px); box-shadow: 0 .75rem 1.75rem rgba(15, 23, 42, .12); }
    .dashboard-title { font-weight: 800; letter-spacing: -.02em; }
    .subtle-label { font-size: .78rem; text-transform: uppercase; letter-spacing: .08em; color: #64748b; font-weight: 700; }
    .section-header { font-weight: 700; color: #0f172a; }
    .mini-note { color: #64748b; font-size: .85rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="dashboard-title mb-0">Panel de Control</h2>
            <p class="text-muted mb-0">Resumen general del negocio</p>
        </div>
        <div>
            <span class="text-muted small border bg-white px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-calendar-event me-1"></i> {{ now()->format('d M, Y') }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @can('registrar_ventas')
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="subtle-label mb-2">Ventas hoy</div>
                        <h3 class="fw-bold text-success mb-0">S/ {{ number_format((float) ($kpis['ventas_hoy'] ?? 0), 2) }}</h3>
                    </div>
                </div>
            </div>
        @endcan

        @can('registrar_compras')
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="subtle-label mb-2">Compras hoy</div>
                        <h3 class="fw-bold text-danger mb-0">S/ {{ number_format((float) ($kpis['compras_hoy'] ?? 0), 2) }}</h3>
                    </div>
                </div>
            </div>
        @endcan

        @can('abrir_caja')
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="subtle-label mb-2">Sesiones activas</div>
                        <h3 class="fw-bold mb-0">{{ $kpis['sesiones_activas'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        @endcan

        @can('ver_kardex')
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="subtle-label mb-2">Stock bajo</div>
                        <h3 class="fw-bold text-warning mb-0">{{ $kpis['productos_stock_bajo'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    @can('gestionar_tesoreria')
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <div class="subtle-label mb-2">Tesorería efectivo</div>
                        <h2 class="fw-bold text-success mb-1">S/ {{ number_format((float) ($tesoreriaEfectivo?->saldo_actual ?? 0), 2) }}</h2>
                        <div class="text-muted small">{{ $tesoreriaEfectivo?->nombre ?? 'No registrada' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <div class="subtle-label mb-2">Tesorería banco</div>
                        <h2 class="fw-bold text-primary mb-1">S/ {{ number_format((float) ($tesoreriaBanco?->saldo_actual ?? 0), 2) }}</h2>
                        <div class="text-muted small">{{ $tesoreriaBanco?->nombre ?? 'No registrada' }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="section-header">Ventas vs Compras (Últimos 7 días)</div>
                    <div class="mini-note">Montos totales emitidos por fecha.</div>
                </div>
                <div class="card-body">
                    <canvas id="ventasComprasChart" height="140"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="section-header">Métodos de pago - Ventas</div>
                    <div class="mini-note">Dinero realmente cobrado por método.</div>
                </div>
                <div class="card-body">
                    <canvas id="metodosPagoVentasChart" height="180"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="section-header">Métodos de pago - Compras</div>
                    <div class="mini-note">Pagos realmente registrados por método.</div>
                </div>
                <div class="card-body">
                    <canvas id="metodosPagoComprasChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    @can('ver_kardex')
        <div class="card dashboard-card">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="section-header">Productos con stock bajo</div>
                <div class="mini-note">Se muestran los productos con stock igual o menor a 10 unidades.</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockBajo as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td class="text-end">
                                    <span class="badge bg-warning text-dark">
                                        {{ (int) ($producto->stock_total_calc ?? 0) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">
                                    No existen productos con stock bajo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endcan
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script id="ventas-compras-data" type="application/json">
{!! json_encode($ventasCompras, JSON_UNESCAPED_UNICODE) !!}
</script>
<script id="metodos-ventas-data" type="application/json">
{!! json_encode($metodosPagoVentas, JSON_UNESCAPED_UNICODE) !!}
</script>
<script id="metodos-compras-data" type="application/json">
{!! json_encode($metodosPagoCompras, JSON_UNESCAPED_UNICODE) !!}
</script>

<script>
    const ventasCompras = JSON.parse(document.getElementById('ventas-compras-data').textContent);
    const metodosPagoVentas = JSON.parse(document.getElementById('metodos-ventas-data').textContent);
    const metodosPagoCompras = JSON.parse(document.getElementById('metodos-compras-data').textContent);

    new Chart(document.getElementById('ventasComprasChart'), {
        type: 'bar',
        data: {
            labels: ventasCompras.map(x => x.fecha),
            datasets: [
                { label: 'Ventas', data: ventasCompras.map(x => x.ventas) },
                { label: 'Compras', data: ventasCompras.map(x => x.compras) }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    new Chart(document.getElementById('metodosPagoVentasChart'), {
        type: 'doughnut',
        data: {
            labels: metodosPagoVentas.map(x => x.name),
            datasets: [{
                data: metodosPagoVentas.map(x => x.value)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    new Chart(document.getElementById('metodosPagoComprasChart'), {
        type: 'doughnut',
        data: {
            labels: metodosPagoCompras.map(x => x.name),
            datasets: [{
                data: metodosPagoCompras.map(x => x.value)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endpush