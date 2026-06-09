<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductoVenta extends Model
{
    use HasFactory;

    protected $table = 'producto_venta';

    protected $fillable = [
        'venta_id',
        'producto_variante_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'impuesto',
        'total',
        'costo_unitario',
        'producto_codigo',
        'producto_nombre',
        'talla_codigo',
        'talla_nombre',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function productoVariante()
    {
        return $this->belongsTo(ProductoVariante::class, 'producto_variante_id');
    }
}