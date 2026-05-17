<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'codigo' => 'required|unique:productos,codigo|max:50',
            'nombre' => 'required|unique:productos,nombre|max:80',
            'descripcion' => 'nullable|max:255',
            'fecha_vencimiento' => 'nullable|date',
            'img_path' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'marca_id' => 'nullable|integer|exists:marcas,id',
            'presentacione_id' => 'required|integer|exists:presentaciones,id',
            'categoria_id' => 'required|array',
            'categoria_id.*' => 'exists:categorias,id',
        ];
    }

    public function attributes()
    {
        return [
            'marca_id' => 'marca',
            'presentacione_id' => 'presentación'
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
