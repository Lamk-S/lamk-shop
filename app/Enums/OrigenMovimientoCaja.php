<?php

namespace App\Enums;

enum OrigenMovimientoCaja: string
{
    case APERTURA = 'APERTURA';
    case VENTA = 'VENTA';
    case CIERRE = 'CIERRE';
    case AJUSTE = 'AJUSTE';
    case INGRESO_MANUAL = 'INGRESO_MANUAL';
    case EGRESO_MANUAL = 'EGRESO_MANUAL';
    case ANULACION = 'ANULACION';

    public static function opciones(): array
    {
        return [
            self::APERTURA->value => 'Apertura de Caja',
            self::VENTA->value => 'Venta',
            self::CIERRE->value => 'Cierre de Caja',
            self::AJUSTE->value => 'Ajuste',
            self::INGRESO_MANUAL->value => 'Ingreso Manual',
            self::EGRESO_MANUAL->value => 'Egreso Manual',
            self::ANULACION->value => 'Anulación',
        ];
    }
}