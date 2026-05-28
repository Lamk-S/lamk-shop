@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Panel de Control</h2>
            <p class="text-muted mb-0">Resumen general del negocio</p>
        </div>
        <div>
            <span class="text-muted small border bg-white px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-calendar-event me-1"></i> {{ date('d M, Y') }}
            </span>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-4 mb-4">

        @can('ver-venta')
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">
                        Ventas Hoy
                    </small>

                    <h3 class="fw-bold text-success mb-0">
                        S/ {{ number_format($kpis['ventas_hoy'], 2) }}
                    </h3>
                </div>
            </div>
        </div>
        @endcan

        @can('ver-compra')
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">
                        Compras Hoy
                    </small>

                    <h3 class="fw-bold text-danger mb-0">
                        S/ {{ number_format($kpis['compras_hoy'], 2) }}
                    </h3>
                </div>
            </div>
        </div>
        @endcan

        @can('ver-sesion-caja')
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">
                        Sesiones Activas
                    </small>

                    <h3 class="fw-bold mb-0">
                        {{ $kpis['sesiones_activas'] }}
                    </h3>
                </div>
            </div>
        </div>
        @endcan

        @can('ver-kardex')
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">
                        Stock Bajo
                    </small>

                    <h3 class="fw-bold text-warning mb-0">
                        {{ $kpis['productos_stock_bajo'] }}
                    </h3>
                </div>
            </div>
        </div>
        @endcan

    </div>

    {{-- Tesorería --}}
    @can('ver-tesoreria')
    @if($tesoreria)
    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Tesorería Efectivo
                    </small>

                    <h2 class="fw-bold text-success">
                        S/ {{ number_format($tesoreria->saldo_efectivo, 2) }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Tesorería Banco
                    </small>

                    <h2 class="fw-bold text-primary">
                        S/ {{ number_format($tesoreria->saldo_banco, 2) }}
                    </h2>
                </div>
            </div>
        </div>

    </div>
    @endif
    @endcan

    {{-- Gráficos --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">
                    Ventas vs Compras (Últimos 7 días)
                </div>

                <div class="card-body">
                    <canvas id="ventasComprasChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">
                    Métodos de Pago - Ventas
                </div>
                <div class="card-body">
                    <canvas id="metodosPagoVentasChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">
                    Métodos de Pago - Compras
                </div>
                <div class="card-body">
                    <canvas id="metodosPagoComprasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock bajo --}}
    @can('ver-kardex')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
            Productos con Stock Bajo
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
                            <span class="badge bg-warning">
                                {{ $producto->stock }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-4">
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
        }
    });

    new Chart(document.getElementById('metodosPagoVentasChart'), {
        type: 'doughnut',
        data: {
            labels: metodosPagoVentas.map(x => x.name),
            datasets: [{
                data: metodosPagoVentas.map(x => x.value)
            }]
        }
    });

    new Chart(document.getElementById('metodosPagoComprasChart'), {
        type: 'doughnut',
        data: {
            labels: metodosPagoCompras.map(x => x.name),
            datasets: [{
                data: metodosPagoCompras.map(x => x.value)
            }]
        }
    });
</script>
@endpush