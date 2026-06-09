<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    ];

    protected $casts = [
        'antes' => 'array',
        'despues' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}