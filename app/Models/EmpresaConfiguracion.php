<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaConfiguracion extends Model
{
    use HasFactory;

    protected $table = 'empresa_configuracion';

    protected $fillable = [
        'razon_social',
        'nombre_comercial',
        'ruc',
        'direccion_fiscal',
        'telefono',
        'email',
        'logo_path',
        'mensaje_ticket',
        'moneda',
        'igv_porcentaje',
        'modo_emision',
        'estado',
    ];

    protected $casts = [
        'igv_porcentaje' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function scopeActiva(Builder $query): Builder
    {
        return $query->where('estado', true);
    }
}