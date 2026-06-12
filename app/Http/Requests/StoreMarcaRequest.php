<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMarcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_marcas') ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:60', Rule::unique('marcas', 'nombre')],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}