<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'img_path',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'fecha_vencimiento',
        'estado',
        'marca_id',
        'presentacion_id',
    ];

    public function compras()
    {
        return $this->belongsToMany(
            Compra::class,
            'compra_producto',
            'producto_id',
            'compra_id'
        )->withTimestamps()->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }

    public function ventas()
    {
        return $this->belongsToMany(
            Venta::class,
            'producto_venta',
            'producto_id',
            'venta_id'
        )->withTimestamps()->withPivot('cantidad', 'precio_venta', 'descuento');
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

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacion::class);
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class);
    }

    public function handleUploadImage(UploadedFile $image): string
    {
        $name = time() . '_' . $image->getClientOriginalName();

        $image->storeAs('productos', $name, 'public');

        return 'productos/' . $name;
    }
}