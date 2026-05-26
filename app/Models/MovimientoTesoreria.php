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
        'origen',
        'descripcion',
        'monto',
        'saldo_anterior',
        'saldo_posterior',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
    ];

    public function tesoreria()
    {
        return $this->belongsTo(Tesoreria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}