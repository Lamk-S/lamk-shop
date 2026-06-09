<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompraProducto extends Model
{
    use HasFactory;

    protected $table = 'compra_producto';

    protected $fillable = [
        'compra_id',
        'producto_variante_id',
        'cantidad',
        'costo_unitario',
        'descuento',
        'subtotal',
        'impuesto',
        'total',
        'producto_codigo',
        'producto_nombre',
        'talla_codigo',
        'talla_nombre',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function productoVariante()
    {
        return $this->belongsTo(ProductoVariante::class, 'producto_variante_id');
    }
}