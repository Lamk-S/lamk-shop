<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'presentaciones';

    protected $fillable = [
        'nombre',
        'sigla',
        'descripcion',
        'estado',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}