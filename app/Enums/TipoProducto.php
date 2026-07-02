<?php

namespace App\Enums;

enum TipoProducto: string
{
    case ZAPATILLA = 'ZAPATILLA';
    case ROPA = 'ROPA';
    case ACCESORIO = 'ACCESORIO';

    public static function opciones(): array
    {
        return [
            self::ZAPATILLA->value => 'Zapatilla',
            self::ROPA->value => 'Ropa',
            self::ACCESORIO->value => 'Accesorio',
        ];
    }
}