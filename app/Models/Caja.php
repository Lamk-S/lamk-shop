<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'fondo_fijo',
        'estado',
    ];

    protected $casts = [
        'fondo_fijo' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function sesionesCaja()
    {
        return $this->hasMany(SesionCaja::class, 'caja_id');
    }

    public function movimientosCaja()
    {
        return $this->hasManyThrough(
            MovimientoCaja::class,
            SesionCaja::class,
            'caja_id',
            'sesion_caja_id',
            'id',
            'id'
        );
    }
}