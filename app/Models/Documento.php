<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'tipo_documento',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class, 'documento_id');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', true);
    }
}