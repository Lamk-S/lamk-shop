<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesionCaja extends Model
{
    use HasFactory;

    protected $table = 'sesiones_caja';

    protected $fillable = [
        'caja_id',
        'user_id',
        'fecha_hora_apertura',
        'fecha_hora_cierre',
        'saldo_inicial',
        'saldo_final_declarado',
        'saldo_final_esperado',
        'diferencia',
        'estado',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoTesoreria::class);
    }
}