<?php

namespace App\Enums;

enum EstadoSesion: string
{
    case ABIERTA = 'ABIERTA';
    case CERRADA = 'CERRADA';
    case ANULADA = 'ANULADA';

    public static function opciones(): array
    {
        return [
            self::ABIERTA->value => 'Abierta',
            self::CERRADA->value => 'Cerrada',
            self::ANULADA->value => 'Anulada',
        ];
    }
}