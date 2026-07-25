<?php

namespace App\Enums;

enum AmbienteSistema: string
{
    case SIMULADO = 'SIMULADO';
    case PRODUCCION = 'PRODUCCION';

    public static function opciones(): array
    {
        return [
            self::SIMULADO->value => 'Entorno de Pruebas (Simulado)',
            self::PRODUCCION->value => 'Producción (Real)',
        ];
    }
}