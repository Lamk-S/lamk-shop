<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProveedorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $proveedore = $this->route('proveedore');

        return [
            'razon_social' => 'required|max:80',
            'direccion' => 'required|max:80',
            'documento_id' => 'required|integer|exists:documentos,id',
            'numero_documento' => 'required|max:20|unique:personas,numero_documento,'.$proveedore->persona->id
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
