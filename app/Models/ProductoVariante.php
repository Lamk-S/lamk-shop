<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoVariante extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'producto_variantes';

    protected $fillable = [
        'producto_id',
        'talla_id',
        'codigo_variante',
        'codigo_barra',
        'stock_actual',
        'stock_minimo',
        'estado',
    ];

    protected $casts = [
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'estado' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class);
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class);
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraProducto::class);
    }

    public function ventaDetalles()
    {
        return $this->hasMany(ProductoVenta::class);
    }
}