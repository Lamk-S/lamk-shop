<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMarcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $marca = $this->route('marca');

        return [
            'nombre' => 'required|string|max:60|unique:marcas,nombre,' . $marca->id,
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean',
        ];
    }
}