<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
        ];
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class);
    }

    public function sesionesCaja()
    {
        return $this->hasMany(SesionCaja::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoTesoreria::class);
    }
}