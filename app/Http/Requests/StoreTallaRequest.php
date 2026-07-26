<?php

namespace App\Http\Requests;

use App\Enums\TipoTalla;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTallaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_tallas') ?? false;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('tallas', 'codigo')],
            'nombre' => ['required', 'string', 'max:50'],
            'tipo_talla' => ['required', Rule::enum(TipoTalla::class)],
            'orden' => ['nullable', 'integer', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}