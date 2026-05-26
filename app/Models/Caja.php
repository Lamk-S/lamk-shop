<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'fondo_fijo',
        'estado',
    ];

    public function sesionesCaja()
    {
        return $this->hasMany(SesionCaja::class);
    }
}