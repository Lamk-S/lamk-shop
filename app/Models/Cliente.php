<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'persona_id',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', true);
    }
}