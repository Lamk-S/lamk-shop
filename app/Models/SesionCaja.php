<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesionCaja extends Model
{
    use HasFactory;

    protected $table = 'sesiones_caja';

    protected $fillable = [
        'caja_id',
        'user_id',
        'user_cierre_id',
        'fecha_hora_apertura',
        'fecha_hora_cierre',
        'saldo_inicial',
        'saldo_final_declarado',
        'saldo_final_esperado',
        'diferencia',
        'estado_sesion',
        'observacion_apertura',
        'observacion_cierre',
    ];

    protected $casts = [
        'fecha_hora_apertura' => 'datetime',
        'fecha_hora_cierre' => 'datetime',
        'saldo_inicial' => 'decimal:2',
        'saldo_final_declarado' => 'decimal:2',
        'saldo_final_esperado' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userCierre()
    {
        return $this->belongsTo(User::class, 'user_cierre_id');
    }

    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class, 'sesion_caja_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'sesion_caja_id');
    }

    public function movimientosTesoreria()
    {
        return $this->hasMany(MovimientoTesoreria::class, 'sesion_caja_id');
    }
}