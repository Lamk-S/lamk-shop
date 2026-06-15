<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaPorPagar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cuentas_por_pagar';

    protected $fillable = [
        'compra_id',
        'proveedor_id',
        'user_id',
        'total',
        'monto_pagado',
        'saldo_pendiente',
        'fecha_emision',
        'fecha_vencimiento',
        'fecha_cancelacion',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_cancelacion' => 'datetime',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoCompra::class, 'cuenta_por_pagar_id');
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereIn('estado', ['PENDIENTE', 'PARCIAL']);
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query
            ->whereIn('estado', ['PENDIENTE', 'PARCIAL'])
            ->whereDate('fecha_vencimiento', '<', today());
    }

    public function scopePagadas(Builder $query): Builder
    {
        return $query->where('estado', 'PAGADA');
    }
}