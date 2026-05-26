<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMarcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:60|unique:marcas,nombre',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean',
        ];
    }
}