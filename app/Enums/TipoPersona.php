<?php

namespace App\Enums;

enum TipoPersona: string
{
    case NATURAL = 'natural';
    case JURIDICA = 'juridica';

    public static function opciones(): array
    {
        return [
            self::NATURAL->value => 'Persona Natural',
            self::JURIDICA->value => 'Persona Jurídica',
        ];
    }
}