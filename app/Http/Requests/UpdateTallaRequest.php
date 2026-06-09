<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTallaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $talla = $this->route('talla');

        return [
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tallas', 'codigo')->ignore($talla?->id),
            ],
            'nombre' => ['required', 'string', 'max:50'],
            'tipo_talla' => ['required', 'in:CALZADO,ROPA,UNICA'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'estado' => ['required', 'boolean'],
        ];
    }
}