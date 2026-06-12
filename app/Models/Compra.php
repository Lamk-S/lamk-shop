<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'tipo_comprobante',
        'serie',
        'correlativo',
        'proveedor_tipo_documento',
        'proveedor_numero_documento',
        'proveedor_nombre',
        'proveedor_direccion',
        'metodo_pago',
        'moneda',
        'fecha_emision',
        'subtotal',
        'descuento_total',
        'impuesto_total',
        'total',
        'estado_documento',
        'observacion',
        'motivo_anulacion',
        'anulado_at',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto_total' => 'decimal:2',
        'total' => 'decimal:2',
        'anulado_at' => 'datetime',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class, 'comprobante_id');
    }

    public function detalles()
    {
        return $this->hasMany(CompraProducto::class, 'compra_id');
    }

    public function movimientosTesoreria()
    {
        return $this->hasMany(MovimientoTesoreria::class, 'compra_id');
    }

    public function scopeNoAnuladas(Builder $query) : Builder
    {
        return $query->where('estado_documento', '!=', 'ANULADA');
    }
}