<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'estado' => 'boolean',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}