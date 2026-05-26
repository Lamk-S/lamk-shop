<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'razon_social' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tipo_persona' => 'required|in:natural,juridica',
            'numero_documento' => 'required|string|max:25|unique:personas,numero_documento',
            'documento_id' => 'required|integer|exists:documentos,id',
            'estado' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'razon_social' => 'razón social',
            'tipo_persona' => 'tipo de persona',
            'documento_id' => 'tipo de documento',
            'numero_documento' => 'número de documento',
        ];
    }
}