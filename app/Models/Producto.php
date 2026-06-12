<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'codigo_barra',
        'nombre',
        'descripcion',
        'img_path',
        'tipo_producto',
        'maneja_tallas',
        'precio_compra',
        'precio_venta',
        'stock_minimo',
        'afecto_igv',
        'marca_id',
        'estado',
    ];

    protected $casts = [
        'maneja_tallas' => 'boolean',
        'afecto_igv' => 'boolean',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'estado' => 'boolean',
    ];

    protected $appends = [
        'stock_total',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,
            'categoria_producto',
            'producto_id',
            'categoria_id'
        )->withTimestamps();
    }

    public function variantes()
    {
        return $this->hasMany(ProductoVariante::class, 'producto_id');
    }

    public function kardex()
    {
        return $this->hasManyThrough(
            Kardex::class,
            ProductoVariante::class,
            'producto_id',
            'producto_variante_id',
            'id',
            'id'
        );
    }

    public function getStockTotalAttribute(): int
    {
        return (int) $this->variantes()
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->sum('stock_actual');
    }

    public function handleUploadImage(UploadedFile $image): string
    {
        $name = time() . '_' . $image->getClientOriginalName();
        $image->storeAs('productos', $name, 'public');

        return 'productos/' . $name;
    }

    public function scopeActivos(Builder $query) : Builder
    {
        return $query->where('estado', true);
    }
}