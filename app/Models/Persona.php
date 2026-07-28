<?php

namespace App\Models;

use App\Enums\TipoPersona;
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
        'tipo_persona' => TipoPersona::class,
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
        $tipo = $this->tipo_persona->value ?? $this->tipo_persona;
        
        if ($tipo === 'juridica') {
            return $this->razon_social ?? 'Sin razón social';
        }
        
        return trim("{$this->nombres} {$this->apellidos}") ?: 'Sin nombre registrado';
    }
}