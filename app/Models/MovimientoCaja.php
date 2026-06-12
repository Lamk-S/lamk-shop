<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'sesion_caja_id',
        'tipo',
        'origen',
        'descripcion',
        'monto',
        'referencia_type',
        'referencia_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function referencia()
    {
        return $this->morphTo();
    }
}