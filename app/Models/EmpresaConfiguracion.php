<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'moneda',
        'igv_porcentaje',
        'modo_emision',
        'estado',
    ];

    protected $casts = [
        'igv_porcentaje' => 'decimal:2',
        'estado' => 'boolean',
    ];
}