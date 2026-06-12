<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comprobante extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipo_comprobante',
        'serie',
        'uso_comprobante',
        'correlativo_actual',
        'es_electronico',
        'ambiente',
        'estado',
    ];

    protected $casts = [
        'correlativo_actual' => 'integer',
        'es_electronico' => 'boolean',
        'estado' => 'boolean',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'comprobante_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'comprobante_id');
    }

    public function scopeActivos(Builder $query) : Builder
    {
        return $query->where('estado', true);
    }
}