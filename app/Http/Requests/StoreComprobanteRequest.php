<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComprobanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_comprobantes') ?? false;
    }

    public function rules(): array
    {
        return [
            'tipo_comprobante' => ['required', Rule::in(['TICKET', 'BOLETA', 'FACTURA', 'NOTA_CREDITO', 'NOTA_DEBITO'])],
            'serie' => [
                'required',
                'string',
                'max:20',
                Rule::unique('comprobantes', 'serie')->where(function ($query) {
                    return $query
                        ->where('tipo_comprobante', $this->input('tipo_comprobante'))
                        ->where('uso_comprobante', $this->input('uso_comprobante'));
                }),
            ],
            'uso_comprobante' => ['required', Rule::in(['COMPRA', 'VENTA'])],
            'correlativo_actual' => ['nullable', 'integer', 'min:0'],
            'es_electronico' => ['nullable', 'boolean'],
            'ambiente' => ['required', Rule::in(['SIMULADO', 'PRODUCCION'])],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}