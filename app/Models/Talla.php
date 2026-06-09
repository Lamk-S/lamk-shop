<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talla extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_talla',
        'orden',
        'estado',
    ];

    public function productoVariantes()
    {
        return $this->hasMany(ProductoVariante::class);
    }
}