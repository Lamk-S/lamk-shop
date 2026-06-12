<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tesoreria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_cuenta',
        'saldo_actual',
        'estado',
    ];

    protected $casts = [
        'saldo_actual' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoTesoreria::class, 'tesoreria_id');
    }

    public function scopeActivas(Builder $query) : Builder
    {
        return $query->where('estado', true);
    }
}