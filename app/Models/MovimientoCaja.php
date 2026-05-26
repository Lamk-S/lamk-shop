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
        'descripcion',
        'monto',
    ];

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class);
    }
}