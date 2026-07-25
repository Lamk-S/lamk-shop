<?php

namespace App\Enums;

enum EstadoDocumentoVenta: string
{
    case REGISTRADA = 'REGISTRADA';
    case EMITIDA = 'EMITIDA';
    case ANULADA = 'ANULADA';
    case PENDIENTE = 'PENDIENTE';

    public static function opciones(): array
    {
        return [
            self::REGISTRADA->value => 'Registrada en Sistema',
            self::EMITIDA->value => 'Emitida (SUNAT)',
            self::PENDIENTE->value => 'Pendiente de Emisión',
            self::ANULADA->value => 'Anulada',
        ];
    }
}