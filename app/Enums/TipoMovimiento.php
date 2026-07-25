<?php

namespace App\Enums;

enum TipoMovimiento: string
{
    case INGRESO = 'INGRESO';
    case EGRESO = 'EGRESO';

    public static function opciones(): array
    {
        return [
            self::INGRESO->value => 'Ingreso',
            self::EGRESO->value => 'Egreso',
        ];
    }
}