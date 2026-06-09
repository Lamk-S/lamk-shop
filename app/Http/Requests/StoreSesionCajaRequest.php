<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSesionCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'caja_id' => ['required', 'integer', Rule::exists('cajas', 'id')],
            'saldo_inicial' => ['nullable', 'numeric', 'min:0'],
            'observacion_apertura' => ['nullable', 'string', 'max:1000'],
        ];
    }
}