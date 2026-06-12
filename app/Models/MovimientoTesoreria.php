<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoTesoreria extends Model
{
    use HasFactory;

    protected $table = 'movimientos_tesoreria';

    protected $fillable = [
        'tesoreria_id',
        'user_id',
        'sesion_caja_id',
        'venta_id',
        'compra_id',
        'tipo',
        'medio',
        'origen',
        'descripcion',
        'monto',
        'saldo_anterior',
        'saldo_posterior',
        'numero_operacion',
        'referencia',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
    ];

    public function tesoreria()
    {
        return $this->belongsTo(Tesoreria::class, 'tesoreria_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }
}