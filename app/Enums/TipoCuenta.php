<?php

namespace App\Enums;

enum TipoCuenta: string
{
    case EFECTIVO = 'EFECTIVO';
    case BANCO = 'BANCO';

    public static function opciones(): array
    {
        return [
            self::EFECTIVO->value => 'Caja Efectivo',
            self::BANCO->value => 'Cuenta Bancaria',
        ];
    }
}