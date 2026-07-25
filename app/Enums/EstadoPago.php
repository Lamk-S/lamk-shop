<?php

namespace App\Enums;

enum EstadoPago: string
{
    case PENDIENTE = 'PENDIENTE';
    case PARCIAL = 'PARCIAL';
    case PAGADA = 'PAGADA';
    case ANULADA = 'ANULADA';

    public static function opciones(): array
    {
        return [
            self::PENDIENTE->value => 'Pendiente',
            self::PARCIAL->value => 'Pago Parcial',
            self::PAGADA->value => 'Pagada Completamente',
            self::ANULADA->value => 'Anulada',
        ];
    }
}