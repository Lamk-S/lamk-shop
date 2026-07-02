<?php

namespace App\Http\Requests;

use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Talla;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductoVarianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_productos') ?? false;
    }

    public function rules(): array
    {
        $variante = $this->route('producto_variante');

        return [
            'producto_id' => ['required', 'integer', Rule::exists('productos', 'id')],
            'talla_id' => ['required', 'integer', Rule::exists('tallas', 'id')],
            'stock_actual' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'estado' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $producto = Producto::find($this->input('producto_id'));
            $talla = Talla::find($this->input('talla_id'));
            $varianteId = $this->route('producto_variante')?->id;

            if (!$producto || !$talla) {
                return;
            }

            if (in_array($producto->tipo_producto, ['ZAPATILLA', 'ROPA'], true) && $talla->codigo === 'UNICA') {
                $validator->errors()->add('talla_id', 'Las zapatillas y la ropa deportiva no pueden usar talla única.');
            }

            if ($producto->tipo_producto === 'ACCESORIO' && $talla->codigo !== 'UNICA') {
                $validator->errors()->add('talla_id', 'Los accesorios deben usar talla única.');
            }

            $duplicate = ProductoVariante::where('producto_id', $producto->id)
                ->where('talla_id', $talla->id)
                ->when($varianteId, fn ($q) => $q->where('id', '!=', $varianteId))
                ->exists();

            if ($duplicate) {
                $validator->errors()->add('talla_id', 'Ya existe otra variante para este producto y esta talla.');
            }
        });
    }
}