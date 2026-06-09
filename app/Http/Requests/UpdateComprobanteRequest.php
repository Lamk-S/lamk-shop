<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComprobanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $comprobante = $this->route('comprobante');

        return [
            'tipo_comprobante' => ['required', 'string', 'max:50'],
            'serie' => [
                'required',
                'string',
                'max:20',
                Rule::unique('comprobantes', 'serie')->ignore($comprobante?->id),
            ],
            'uso_comprobante' => ['required', 'in:COMPRA,VENTA'],
            'correlativo_actual' => ['required', 'integer', 'min:0'],
            'es_electronico' => ['nullable', 'boolean'],
            'ambiente' => ['required', 'in:SIMULADO,PRODUCCION'],
            'estado' => ['required', 'boolean'],
        ];
    }
}