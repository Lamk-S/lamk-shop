<?php

namespace App\Enums;

enum EstadoDocumentoCompra: string
{
    case REGISTRADA = 'REGISTRADA';
    case RECEPCIONADA = 'RECEPCIONADA';
    case ANULADA = 'ANULADA';
    case PENDIENTE = 'PENDIENTE';

    public static function opciones(): array
    {
        return [
            self::REGISTRADA->value => 'Registrada',
            self::RECEPCIONADA->value => 'Recepcionada',
            self::PENDIENTE->value => 'Pendiente',
            self::ANULADA->value => 'Anulada',
        ];
    }
}