<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talla extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_talla',
        'orden',
        'estado',
    ];

    protected $casts = [
        'orden' => 'integer',
        'estado' => 'boolean',
    ];

    public function productoVariantes()
    {
        return $this->hasMany(ProductoVariante::class, 'talla_id');
    }

    public function scopeActivas(Builder $query) : Builder
    {
        return $query->where('estado', true);
    }
}