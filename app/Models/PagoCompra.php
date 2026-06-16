<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoCompra extends Model
{
    use HasFactory;

    protected $table = 'pagos_compra';

    protected $fillable = [
        'compra_id',
        'cuenta_por_pagar_id',
        'user_id',
        'metodo_pago',
        'monto',
        'referencia_operacion',
        'moneda',
        'fecha_pago',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
        'estado' => 'boolean',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function cuentaPorPagar()
    {
        return $this->belongsTo(CuentaPorPagar::class, 'cuenta_por_pagar_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActivos(Builder $query) : Builder
    {
        return $query->where('estado', true);
    }
}