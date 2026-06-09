<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $fillable = [
        'producto_variante_id',
        'tipo_transaccion',
        'origen_type',
        'origen_id',
        'descripcion',
        'entrada',
        'salida',
        'saldo_anterior',
        'saldo_posterior',
        'costo_unitario',
        'costo_total',
        'user_id',
    ];

    protected $casts = [
        'entrada' => 'integer',
        'salida' => 'integer',
        'saldo_anterior' => 'integer',
        'saldo_posterior' => 'integer',
        'costo_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    public function productoVariante()
    {
        return $this->belongsTo(ProductoVariante::class, 'producto_variante_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function origen()
    {
        return $this->morphTo();
    }
}