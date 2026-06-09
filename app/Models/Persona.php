<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipo_persona',
        'documento_id',
        'numero_documento',
        'nombres',
        'apellidos',
        'razon_social',
        'direccion',
        'telefono',
        'email',
        'estado',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class);
    }
}