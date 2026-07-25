<?php

namespace App\Enums;

enum TipoTransaccionKardex: string
{
    case COMPRA = 'COMPRA';
    case VENTA = 'VENTA';
    case AJUSTE = 'AJUSTE';
    case APERTURA = 'APERTURA';
    case ANULACION = 'ANULACION';
    case DEVOLUCION = 'DEVOLUCION';
    case MERMA = 'MERMA';
    case VENCIDO = 'VENCIDO';
    case TRANSFERENCIA = 'TRANSFERENCIA';

    public static function opciones(): array
    {
        return [
            self::COMPRA->value => 'Compra',
            self::VENTA->value => 'Venta',
            self::AJUSTE->value => 'Ajuste de Inventario',
            self::APERTURA->value => 'Apertura Inicial',
            self::ANULACION->value => 'Anulación',
            self::DEVOLUCION->value => 'Devolución',
            self::MERMA->value => 'Merma / Pérdida',
            self::VENCIDO->value => 'Producto Vencido',
            self::TRANSFERENCIA->value => 'Transferencia',
        ];
    }
}