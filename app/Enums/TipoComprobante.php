<?php

namespace App\Enums;

enum TipoComprobante: string
{
    case TICKET = 'TICKET';
    case BOLETA = 'BOLETA';
    case FACTURA = 'FACTURA';
    case NOTA_CREDITO = 'NOTA_CREDITO';
    case NOTA_DEBITO = 'NOTA_DEBITO';

    public static function opciones(): array
    {
        return [
            self::TICKET->value => 'Ticket / Recibo',
            self::BOLETA->value => 'Boleta Electrónica',
            self::FACTURA->value => 'Factura Electrónica',
            self::NOTA_CREDITO->value => 'Nota de Crédito',
            self::NOTA_DEBITO->value => 'Nota de Débito',
        ];
    }
}