<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePresentacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:60|unique:presentaciones,nombre',
            'sigla' => 'nullable|string|max:10|unique:presentaciones,sigla',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean',
        ];
    }
}