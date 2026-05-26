<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tesoreria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tesorerias';

    protected $fillable = [
        'nombre',
        'saldo_efectivo',
        'saldo_banco',
        'estado',
    ];

    protected $casts = [
        'saldo_efectivo' => 'decimal:2',
        'saldo_banco' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoTesoreria::class);
    }
}