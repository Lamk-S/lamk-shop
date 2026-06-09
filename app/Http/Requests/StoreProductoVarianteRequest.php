<?php

namespace App\Http\Requests;

use App\Models\Producto;
use App\Models\Talla;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductoVarianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'integer', Rule::exists('productos', 'id')],
            'talla_id' => ['required', 'integer', Rule::exists('tallas', 'id')],
            'codigo_barra' => ['nullable', 'string', 'max:80', 'unique:producto_variantes,codigo_barra'],
            'stock_actual' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $producto = Producto::find($this->input('producto_id'));
            $talla = Talla::find($this->input('talla_id'));

            if (!$producto || !$talla) {
                return;
            }

            if (in_array($producto->tipo_producto, ['ZAPATILLA', 'ROPA'], true) && $talla->codigo === 'UNICA') {
                $validator->errors()->add('talla_id', 'Las zapatillas y la ropa deportiva no pueden usar talla única.');
            }

            if ($producto->tipo_producto === 'ACCESORIO' && $talla->codigo !== 'UNICA') {
                $validator->errors()->add('talla_id', 'Los accesorios deben usar talla única.');
            }
        });
    }
}