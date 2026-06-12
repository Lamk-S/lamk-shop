<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'persona_id');
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class, 'persona_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        if ($this->razon_social) {
            return $this->razon_social;
        }

        return trim(($this->nombres ?? '') . ' ' . ($this->apellidos ?? ''));
    }
}