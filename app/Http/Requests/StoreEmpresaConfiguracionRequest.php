<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmpresaConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_configuracion') ?? false;
    }

    public function rules(): array
    {
        return [
            'razon_social' => ['required', 'string', 'max:150'],
            'nombre_comercial' => ['nullable', 'string', 'max:150'],
            'ruc' => [
                'required',
                'digits:11',
                Rule::unique('empresa_configuracion', 'ruc'),
            ],
            'direccion_fiscal' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'mensaje_ticket' => ['nullable', 'string', 'max:1000'],
            'moneda' => ['required', 'string', 'max:10'],
            'igv_porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'modo_emision' => [
                'required',
                Rule::in(['SIMULADA', 'REAL']),
            ],
            'estado' => ['required', 'boolean'],
        ];
    }
}