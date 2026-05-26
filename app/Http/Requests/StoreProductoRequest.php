<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'img_path' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'nullable|boolean',
            'marca_id' => 'nullable|integer|exists:marcas,id',
            'presentacion_id' => 'required|integer|exists:presentaciones,id',
            'categoria_id' => 'required|array|min:1',
            'categoria_id.*' => 'integer|exists:categorias,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'presentacion_id' => 'presentación',
            'categoria_id' => 'categorías',
        ];
    }
}