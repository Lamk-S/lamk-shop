<?php

namespace App\Http\Requests;

use App\Models\Documento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $proveedor = $this->route('proveedor');
        $personaId = $proveedor?->persona_id;

        return [
            'tipo_persona' => ['required', 'in:natural,juridica'],
            'documento_id' => ['required', 'integer', Rule::exists('documentos', 'id')],
            'numero_documento' => [
                'required',
                'string',
                'max:25',
                Rule::unique('personas', 'numero_documento')
                    ->ignore($personaId)
                    ->where(fn ($query) => $query->where('documento_id', $this->input('documento_id'))),
            ],
            'nombres' => ['nullable', 'string', 'max:120'],
            'apellidos' => ['nullable', 'string', 'max:120'],
            'razon_social' => ['nullable', 'string', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $tipo = $this->input('tipo_persona');
            $documento = Documento::find($this->input('documento_id'));

            if ($tipo === 'natural') {
                if (!$this->filled('nombres') || !$this->filled('apellidos')) {
                    $validator->errors()->add('nombres', 'Para una persona natural debes registrar nombres y apellidos.');
                }
            }

            if ($tipo === 'juridica') {
                if (!$this->filled('razon_social')) {
                    $validator->errors()->add('razon_social', 'Para una persona jurídica debes registrar la razón social.');
                }

                if ($documento && $documento->codigo !== 'RUC') {
                    $validator->errors()->add('documento_id', 'Una persona jurídica debe usar RUC.');
                }
            }
        });
    }
}