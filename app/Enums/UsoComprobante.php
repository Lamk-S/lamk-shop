<?php

namespace App\Enums;

enum UsoComprobante: string
{
    case COMPRA = 'COMPRA';
    case VENTA = 'VENTA';

    public static function opciones(): array
    {
        return [
            self::COMPRA->value => 'Para Compras',
            self::VENTA->value => 'Para Ventas',
        ];
    }
}