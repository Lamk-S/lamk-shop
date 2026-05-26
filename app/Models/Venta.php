<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'sesion_caja_id',
        'comprobante_id',
        'numero_comprobante',
        'fecha_hora',
        'subtotal',
        'impuesto',
        'total',
        'monto_recibido',
        'vuelto_entregado',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'producto_venta',
            'venta_id',
            'producto_id'
        )->withTimestamps()->withPivot('cantidad', 'precio_venta', 'descuento');
    }

    public function pagos()
    {
        return $this->hasMany(PagoVenta::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoTesoreria::class);
    }
}