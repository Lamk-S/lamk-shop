<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentacione extends Model
{
    public function productos() {
        return $this->hasMany(Producto::class);
    }

    public function caracteristica() {
        return $this->belongsTo(Caracteristica::class);
    }
}
