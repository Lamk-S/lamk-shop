<?php

namespace App\Enums;

enum MetodoPago: string
{
    case EFECTIVO = 'EFECTIVO';
    case TARJETA = 'TARJETA';
    case TRANSFERENCIA = 'TRANSFERENCIA';
    case YAPE = 'YAPE';
    case PLIN = 'PLIN';
    case CREDITO = 'CREDITO';
    case MIXTO = 'MIXTO';
    case OTRO = 'OTRO';

    public static function opciones(): array
    {
        return [
            self::EFECTIVO->value => 'Efectivo',
            self::TARJETA->value => 'Tarjeta (Débito/Crédito)',
            self::TRANSFERENCIA->value => 'Transferencia Bancaria',
            self::YAPE->value => 'Yape',
            self::PLIN->value => 'Plin',
            self::CREDITO->value => 'Crédito',
            self::MIXTO->value => 'Pago Mixto',
            self::OTRO->value => 'Otro Método',
        ];
    }
}