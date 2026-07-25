<?php

namespace App\Enums;

enum OrigenMovimientoTesoreria: string
{
    case CIERRE_CAJA = 'CIERRE_CAJA';
    case VENTA_EFECTIVO = 'VENTA_EFECTIVO';
    case VENTA_TARJETA = 'VENTA_TARJETA';
    case VENTA_TRANSFERENCIA = 'VENTA_TRANSFERENCIA';
    case COMPRA_PRODUCTO = 'COMPRA_PRODUCTO';
    case DEPOSITO = 'DEPOSITO';
    case RETIRO = 'RETIRO';
    case AJUSTE = 'AJUSTE';
    case ANULACION = 'ANULACION';

    public static function opciones(): array
    {
        return [
            self::CIERRE_CAJA->value => 'Cierre de Caja Fuerte',
            self::VENTA_EFECTIVO->value => 'Venta en Efectivo',
            self::VENTA_TARJETA->value => 'Venta por Tarjeta',
            self::VENTA_TRANSFERENCIA->value => 'Venta por Transferencia',
            self::COMPRA_PRODUCTO->value => 'Pago de Compra',
            self::DEPOSITO->value => 'Depósito Entrante',
            self::RETIRO->value => 'Retiro / Disposición',
            self::AJUSTE->value => 'Ajuste Contable',
            self::ANULACION->value => 'Anulación',
        ];
    }
}