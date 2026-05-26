<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_documento',
        'estado',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class);
    }
}