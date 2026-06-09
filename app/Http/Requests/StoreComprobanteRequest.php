<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComprobanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_comprobante' => ['required', 'string', 'max:50'],
            'serie' => ['required', 'string', 'max:20', 'unique:comprobantes,serie'],
            'uso_comprobante' => ['required', 'in:COMPRA,VENTA'],
            'correlativo_actual' => ['nullable', 'integer', 'min:0'],
            'es_electronico' => ['nullable', 'boolean'],
            'ambiente' => ['required', 'in:SIMULADO,PRODUCCION'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}