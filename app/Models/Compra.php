<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras';

    protected $fillable = [
        'proveedor_id',
        'user_id',
        'comprobante_id',
        'numero_comprobante',
        'metodo_pago',
        'fecha_hora',
        'subtotal',
        'impuesto',
        'total',
        'estado',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'compra_producto',
            'compra_id',
            'producto_id'
        )->withTimestamps()->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoTesoreria::class);
    }
}