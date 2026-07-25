<?php

namespace App\Enums;

enum TipoTalla: string
{
    case CALZADO = 'CALZADO';
    case ROPA = 'ROPA';
    case UNICA = 'UNICA';

    public static function opciones(): array
    {
        return [
            self::CALZADO->value => 'Calzado',
            self::ROPA->value => 'Ropa',
            self::UNICA->value => 'Talla Única',
        ];
    }
}