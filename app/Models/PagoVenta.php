<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoVenta extends Model
{
    use HasFactory;

    protected $table = 'pagos_venta';

    protected $fillable = [
        'venta_id',
        'metodo_pago',
        'monto',
        'referencia_operacion',
        'moneda',
        'estado',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'estado' => 'integer',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function scopeActivos(Builder $query) : Builder
    {
        return $query->where('estado', 1);
    }
}