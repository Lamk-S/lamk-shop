@extends('layouts.app')
@section('title', 'Detalle de Sesión de Caja')

@push('css')
<style>
    .page-title { font-weight: 800; letter-spacing: -.02em; }
    .glass-card { border: 0; border-radius: 1.25rem; box-shadow: 0 0.5rem 1.5rem rgba(15, 23, 42, .08); overflow: hidden; }
    .soft-header { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid rgba(148, 163, 184, .18); }
    .summary-box { background: #f8fafc; border: 1px solid rgba(148, 163, 184, .16); border-radius: 1rem; padding: 1rem; height: 100%; transition: transform .15s ease, box-shadow .15s ease; }
    .summary-box:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(15, 23, 42, .06); }
    .section-title { font-size: .9rem; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .06em; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
        <div>
            <h2 class="page-title text-dark mb-0">Detalle de Sesión de Caja</h2>
            <ol class="breadcrumb mb-0 mt-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sesiones-caja.index') }}" class="text-decoration-none">Sesiones de Caja</a></li>
                <li class="breadcrumb-item active">Ver detalle</li>
            </ol>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('sesiones-caja.index') }}" class="btn btn-light shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    @php
        $saldoInicial = (float) $sesionCaja->saldo_inicial;
        $saldoEsperado = (float) ($sesionCaja->saldo_final_esperado ?? 0);
        $saldoDeclarado = (float) ($sesionCaja->saldo_final_declarado ?? 0);
        $diferencia = (float) ($sesionCaja->diferencia ?? 0);
        $totalIngresos = (float) $sesionCaja->movimientosCaja->where('tipo', 'INGRESO')->where('origen', '!=', 'APERTURA')->sum('monto');
        $totalEgresos = (float) $sesionCaja->movimientosCaja->where('tipo', 'EGRESO')->sum('monto');
        $totalVentas = (float) $sesionCaja->ventas->where('estado_documento', '!=', 'ANULADA')->sum('total');
        $totalMovimientos = $sesionCaja->movimientosCaja->count();
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card glass-card h-100 border-0">
                <div class="card-header soft-header p-0">
                    <div class="p-4 p-md-5 bg-white">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 58px; height: 58px;">
                                    <i class="fa-solid fa-lock-open fs-4"></i>
                                </div>

                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h4 class="mb-0 fw-bold text-dark">Sesión de Caja #{{ $sesionCaja->id }}</h4>
                                        @if($sesionCaja->estado_sesion === 'ABIERTA')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                                Abierta
                                            </span>
                                        @elseif($sesionCaja->estado_sesion === 'CERRADA')
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">
                                                Cerrada
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                                Anulada
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-muted small">
                                        <i class="fa-solid fa-cash-register me-1"></i>
                                        {{ $sesionCaja->caja?->nombre ?? 'Caja no disponible' }}
                                        <span class="mx-1">·</span>
                                        <i class="fa-solid fa-user me-1"></i>
                                        {{ $sesionCaja->user?->name ?? 'Usuario no disponible' }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                                    Apertura: {{ $sesionCaja->fecha_hora_apertura ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_apertura)->format('d/m/Y H:i') : '—' }}
                                </span>
                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                                    Cierre: {{ $sesionCaja->fecha_hora_cierre ? \Carbon\Carbon::parse($sesionCaja->fecha_hora_cierre)->format('d/m/Y H:i') : 'Abierta' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 px-md-5 pb-4 pb-md-5">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-4 rounded-4 border h-100 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="text-muted small text-uppercase fw-semibold">Fondo fijo</div>
                                        <i class="fa-solid fa-vault text-primary"></i>
                                    </div>
                                    <div class="fs-4 fw-bold text-dark">
                                        S/ {{ number_format((float) ($sesionCaja->caja?->fondo_fijo ?? 0), 2) }}
                                    </div>
                                    <div class="text-muted small mt-1">Capital inicial de la caja</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-4 rounded-4 border h-100 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="text-muted small text-uppercase fw-semibold">Saldo inicial</div>
                                        <i class="fa-solid fa-hand-holding-dollar text-primary"></i>
                                    </div>
                                    <div class="fs-4 fw-bold text-primary">
                                        S/ {{ number_format($saldoInicial, 2) }}
                                    </div>
                                    <div class="text-muted small mt-1">Monto con el que inicia la sesión</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-4 rounded-4 border h-100 bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="text-muted small text-uppercase fw-semibold">Diferencia</div>
                                        <i class="fa-solid fa-scale-balanced {{ $diferencia === 0.0 ? 'text-success' : 'text-danger' }}"></i>
                                    </div>
                                    <div class="fs-4 fw-bold {{ $diferencia === 0.0 ? 'text-success' : 'text-danger' }}">
                                        S/ {{ number_format($diferencia, 2) }}
                                    </div>
                                    <div class="text-muted small mt-1">
                                        {{ $diferencia === 0.0 ? 'Cuadre exacto' : 'Revisar descuadre' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-3 col-6">
                                <div class="summary-box shadow-sm">
                                    <div class="summary-title">Saldo esperado</div>
                                    <div class="summary-value">S/ {{ number_format($saldoEsperado, 2) }}</div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <div class="summary-box shadow-sm">
                                    <div class="summary-title">Saldo declarado</div>
                                    <div class="summary-value">S/ {{ number_format($saldoDeclarado, 2) }}</div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <div class="summary-box shadow-sm">
                                    <div class="summary-title">Ingresos operativos</div>
                                    <div class="summary-value text-success">S/ {{ number_format($totalIngresos, 2) }}</div>
                                    <div class="text-muted small mt-1">Sin contar apertura</div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <div class="summary-box shadow-sm">
                                    <div class="summary-title">Egresos operativos</div>
                                    <div class="summary-value text-danger">S/ {{ number_format($totalEgresos, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <div class="p-4 rounded-4 border bg-white">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="section-title mb-0">Resumen operativo</div>
                                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                                            {{ $totalMovimientos }} movimientos
                                        </span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 44px; height: 44px;">
                                                    <i class="fa-solid fa-receipt"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small fw-semibold text-uppercase">Ventas de la sesión</div>
                                                    <div class="fw-bold text-dark">S/ {{ number_format($totalVentas, 2) }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light">
                                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 44px; height: 44px;">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small fw-semibold text-uppercase">Estado operativo</div>
                                                    <div class="fw-bold text-dark">
                                                        {{ $sesionCaja->estado_sesion === 'ABIERTA' ? 'Activa' : ($sesionCaja->estado_sesion === 'CERRADA' ? 'Cerrada' : 'Anulada') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light">
                                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 44px; height: 44px;">
                                                    <i class="fa-solid fa-coins"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small fw-semibold text-uppercase">Movimientos</div>
                                                    <div class="fw-bold text-dark">{{ $totalMovimientos }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($sesionCaja->observacion_apertura || $sesionCaja->observacion_cierre)
                            <div class="row g-3 mt-1">
                                @if($sesionCaja->observacion_apertura)
                                    <div class="col-lg-6">
                                        <div class="p-4 rounded-4 border bg-light h-100">
                                            <div class="section-title mb-2">
                                                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Observación de apertura
                                            </div>
                                            <div class="text-muted">{{ $sesionCaja->observacion_apertura }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($sesionCaja->observacion_cierre)
                                    <div class="col-lg-6">
                                        <div class="p-4 rounded-4 border bg-light h-100">
                                            <div class="section-title mb-2">
                                                <i class="fa-solid fa-pen-to-square me-2 text-danger"></i>Observación de cierre
                                            </div>
                                            <div class="text-muted">{{ $sesionCaja->observacion_cierre }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card glass-card mb-4">
                <div class="card-body p-4">
                    <h6 class="section-title mb-3">Resumen rápido</h6>
                    <div class="d-grid gap-3">
                        <div class="summary-box">
                            <div class="summary-title">Movimientos</div>
                            <div class="summary-value">{{ $totalMovimientos }}</div>
                        </div>
                        <div class="summary-box">
                            <div class="summary-title">Ventas registradas</div>
                            <div class="summary-value">{{ $sesionCaja->ventas->count() }}</div>
                        </div>
                        <div class="summary-box">
                            <div class="summary-title">Estado operativo</div>
                            <div class="summary-value">
                                @if($sesionCaja->estado_sesion === 'ABIERTA')
                                    Activa
                                @elseif($sesionCaja->estado_sesion === 'CERRADA')
                                    Cerrada
                                @else
                                    Anulada
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card glass-card">
                <div class="card-body p-4">
                    <h6 class="section-title mb-3">Nota operativa</h6>
                    <p class="mb-0 text-muted">
                        La apertura de caja solo representa el fondo inicial. Los ingresos reales de venta deben reflejarse aparte,
                        y los pagos electrónicos no deben inflar el efectivo de caja.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card mb-4">
        <div class="card-header soft-header p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Movimientos de caja</h5>
                    <div class="text-muted small">Detalle de ingresos, egresos y apertura de sesión</div>
                </div>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                {{ $totalMovimientos }} registros
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Movimiento</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Origen</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end pe-4">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesionCaja->movimientosCaja as $item)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-semibold text-dark">{{ $item->descripcion }}</div>
                                    <div class="small text-muted">
                                        @if($item->origen === 'APERTURA')
                                            Movimiento inicial de la sesión
                                        @else
                                            ID movimiento #{{ $item->id }}
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center py-3">
                                    @if($item->origen === 'APERTURA')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">Apertura</span>
                                    @elseif($item->tipo === 'INGRESO')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Egreso</span>
                                    @endif
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-secondary border">{{ $item->origen }}</span>
                                </td>
                                <td class="text-end py-3 fw-bold text-dark">
                                    S/ {{ number_format((float) $item->monto, 2) }}
                                </td>
                                <td class="text-end pe-4 py-3 text-muted">
                                    {{ optional($item->created_at)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-money-bill-wave text-warning fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">Sin movimientos de caja en esta sesión</h5>
                                        <p class="text-muted mb-0">No hay registros de movimientos de caja asociados a la sesión actual.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card glass-card">
        <div class="card-header soft-header p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Ventas asociadas</h5>
                    <div class="text-muted small">Documentos emitidos durante esta sesión</div>
                </div>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                {{ $sesionCaja->ventas->count() }} ventas
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-soft mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Comprobante</th>
                            <th>Cliente</th>
                            <th class="text-center">Métodos de pago</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesionCaja->ventas as $venta)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">
                                        {{ trim(($venta->serie ?? '') . '-' . ($venta->correlativo ?? '')) ?: '—' }}
                                    </div>
                                    <div class="small text-muted">{{ $venta->comprobante?->tipo_comprobante ?? 'Sin comprobante' }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-medium text-dark">
                                        {{ $venta->cliente?->persona?->razon_social
                                            ?? trim(($venta->cliente?->persona?->nombres ?? '') . ' ' . ($venta->cliente?->persona?->apellidos ?? ''))
                                            ?: 'Consumidor final' }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $venta->cliente_tipo_documento ?? '—' }} {{ $venta->cliente_numero_documento ?? '' }}
                                    </div>
                                </td>
                                <td class="text-center py-3">
                                    @php
                                        $metodos = $venta->pagos->pluck('metodo_pago')->unique()->implode(', ');
                                    @endphp
                                    <span class="badge bg-light text-secondary border">{{ $metodos ?: 'N/A' }}</span>
                                </td>
                                <td class="text-end py-3 fw-bold text-primary">
                                    S/ {{ number_format((float) $venta->total, 2) }}
                                </td>
                                <td class="text-center py-3">
                                    @if($venta->estado_documento === 'ANULADA')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Anulada</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Emitida</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 py-3 text-muted">
                                    {{ optional($venta->fecha_emision)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 90px; height: 90px;">
                                            <i class="fas fa-shopping-cart text-secondary fs-1"></i>
                                        </div>
                                        <h5 class="fw-semibold text-dark mb-1">Sin ventas en esta sesión</h5>
                                        <p class="text-muted mb-0">No hay ventas asociadas a la sesión actual.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection