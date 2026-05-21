<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    use HasFactory;

    protected $fillable = ['fecha_hora', 'impuesto', 'numero_comprobante', 'total', 'cliente_id', 'user_id', 'comprobante_id'];

    public function cliente() {
        return $this->belongsTo(Cliente::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comprobante() {
        return $this->belongsTo(Comprobante::class);
    }

    public function productos() {
        return $this->belongsToMany(Producto::class, 'producto_venta', 'venta_id', 'producto_id')
        ->withTimestamps()
        ->withPivot('cantidad', 'precio_venta', 'descuento');
    }
}
