<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePresentacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $presentacion = $this->route('presentacion');

        return [
            'nombre' => 'required|string|max:60|unique:presentaciones,nombre,' . $presentacion->id,
            'sigla' => 'nullable|string|max:10|unique:presentaciones,sigla,' . $presentacion->id,
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean',
        ];
    }
}