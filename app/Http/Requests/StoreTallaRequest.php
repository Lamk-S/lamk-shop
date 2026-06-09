<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTallaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:20', 'unique:tallas,codigo'],
            'nombre' => ['required', 'string', 'max:50'],
            'tipo_talla' => ['required', 'in:CALZADO,ROPA,UNICA'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}