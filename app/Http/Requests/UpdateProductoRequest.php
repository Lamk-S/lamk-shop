<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $producto = $this->route('producto');

        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('productos', 'codigo')->ignore($producto?->id),
            ],
            'codigo_barra' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('productos', 'codigo_barra')->ignore($producto?->id),
            ],
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string'],
            'img_path' => ['nullable', 'image', 'max:2048'],
            'tipo_producto' => ['required', 'in:ZAPATILLA,ROPA,ACCESORIO'],
            'maneja_tallas' => ['required', 'boolean'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock_total' => ['nullable', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'afecto_igv' => ['nullable', 'boolean'],
            'marca_id' => ['nullable', 'integer', Rule::exists('marcas', 'id')],
            'categoria_id' => ['required', 'array', 'min:1'],
            'categoria_id.*' => ['integer', Rule::exists('categorias', 'id')],
            'variantes' => ['nullable', 'array'],
            'variantes.*.id' => ['nullable', 'integer'],
            'variantes.*.talla_id' => ['nullable', 'integer', Rule::exists('tallas', 'id')],
            'variantes.*.codigo_barra' => ['nullable', 'string', 'max:80'],
            'variantes.*.stock_actual' => ['nullable', 'integer', 'min:0'],
            'variantes.*.stock_minimo' => ['nullable', 'integer', 'min:0'],
            'variantes.*.estado' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $tipo = $this->input('tipo_producto');
            $manejaTallas = filter_var($this->input('maneja_tallas'), FILTER_VALIDATE_BOOLEAN);

            if (in_array($tipo, ['ZAPATILLA', 'ROPA'], true) && !$manejaTallas) {
                $validator->errors()->add('maneja_tallas', 'Las zapatillas y la ropa deportiva deben manejar tallas.');
            }

            if ($tipo === 'ACCESORIO' && $manejaTallas) {
                $validator->errors()->add('maneja_tallas', 'Los accesorios deben manejar talla única.');
            }
        });
    }
}