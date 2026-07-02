<?php

namespace App\Http\Requests;

use App\Enums\TipoProducto;
use App\Models\Producto;
use App\Models\Talla;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_productos') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => $this->filled('codigo') ? strtoupper(trim($this->input('codigo'))) : null,
            'nombre' => $this->filled('nombre') ? trim($this->input('nombre')) : null,
            'descripcion' => $this->filled('descripcion') ? trim($this->input('descripcion')) : null,
            'maneja_tallas' => filter_var($this->input('maneja_tallas'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'afecto_igv' => filter_var($this->input('afecto_igv', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('productos', 'codigo')],
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string'],
            'img_path' => ['nullable', 'image', 'max:2048'],
            'tipo_producto' => ['required', Rule::enum(TipoProducto::class)],
            'maneja_tallas' => ['required', 'boolean'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'afecto_igv' => ['nullable', 'boolean'],
            'marca_id' => ['nullable', 'integer', Rule::exists('marcas', 'id')],
            'categoria_id' => ['required', 'array', 'min:1'],
            'categoria_id.*' => ['integer', Rule::exists('categorias', 'id')],

            'variantes' => ['nullable', 'array'],
            'variantes.*.talla_id' => ['nullable', 'integer', Rule::exists('tallas', 'id')],
            'variantes.*.codigo_variante' => ['nullable', 'string', 'max:80', Rule::unique('producto_variantes', 'codigo_variante')],
            'variantes.*.stock_actual' => ['nullable', 'integer', 'min:0'],
            'variantes.*.stock_minimo' => ['nullable', 'integer', 'min:0'],
            'variantes.*.estado' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $tipo = TipoProducto::tryFrom($this->input('tipo_producto'));
            $manejaTallas = filter_var($this->input('maneja_tallas'), FILTER_VALIDATE_BOOLEAN);

            if (in_array($tipo, [TipoProducto::ZAPATILLA, TipoProducto::ROPA], true) && !$manejaTallas) {
                $validator->errors()->add('maneja_tallas', 'Las zapatillas y la ropa deportiva deben manejar tallas.');
            }

            if ($tipo === TipoProducto::ACCESORIO && $manejaTallas) {
                $validator->errors()->add('maneja_tallas', 'Los accesorios deben manejar talla única.');
            }

            if ($manejaTallas) {
                $variantes = collect($this->input('variantes', []))
                    ->filter(fn ($row) => !empty($row['talla_id']))
                    ->values();

                if ($variantes->isEmpty()) {
                    $validator->errors()->add('variantes', 'Debes registrar al menos una variante para este producto.');
                }
            }
        });
    }
}