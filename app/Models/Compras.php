<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;

    protected $fillable = ['fecha_hora', 'impuesto', 'numero_comprobante', 'total', 'comprobante_id', 'proveedore_id'];

    public function proveedore() {
        return $this->belongsTo(Proveedore::class);
    }

    public function comprobante() {
        return $this->belongsTo(Comprobante::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'compra_producto', 'compra_id', 'producto_id')
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }
}
