<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'tipo_documento',
        'estado',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class);
    }
}