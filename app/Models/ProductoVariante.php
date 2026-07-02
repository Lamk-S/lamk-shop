<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoVariante extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'producto_variantes';

    protected $fillable = [
        'producto_id',
        'talla_id',
        'codigo_variante',
        'stock_actual',
        'stock_minimo',
        'costo_promedio',
        'ultimo_costo_compra',
        'estado',
    ];

    protected $casts = [
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'costo_promedio' => 'decimal:2',
        'ultimo_costo_compra' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class, 'talla_id');
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class, 'producto_variante_id');
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraProducto::class, 'producto_variante_id');
    }

    public function ventaDetalles()
    {
        return $this->hasMany(ProductoVenta::class, 'producto_variante_id');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', true);
    }

    public static function generarCodigoVariante(
        Producto $producto,
        Talla $talla
    ): string {
        return "{$producto->codigo}-{$talla->codigo}";
    }
}