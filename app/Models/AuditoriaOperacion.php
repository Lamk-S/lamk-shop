<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaOperacion extends Model
{
    use HasFactory;

    protected $table = 'auditoria_operaciones';

    protected $fillable = [
        'user_id',
        'entidad',
        'entidad_id',
        'accion',
        'antes',
        'despues',
        'ip',
        'user_agent',
        'metodo_http',
        'ruta',
    ];

    protected $casts = [
        'antes' => 'array',
        'despues' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePorEntidad(Builder $query, string $entidad) : Builder
    {
        return $query->where('entidad', $entidad);
    }

    public function scopePorAccion(Builder $query, string $accion) : Builder
    {
        return $query->where('accion', $accion);
    }
}