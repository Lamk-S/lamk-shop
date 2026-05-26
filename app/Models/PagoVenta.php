<?php

namespace App\Models;

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
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}