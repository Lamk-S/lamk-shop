<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'user_id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'user_id');
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class, 'user_id');
    }

    public function sesionesCaja()
    {
        return $this->hasMany(SesionCaja::class, 'user_id');
    }

    public function movimientosTesoreria()
    {
        return $this->hasMany(MovimientoTesoreria::class, 'user_id');
    }

    public function auditoriasOperaciones()
    {
        return $this->hasMany(AuditoriaOperacion::class, 'user_id');
    }
}