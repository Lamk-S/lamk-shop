<?php

namespace App\Http\Requests;

use App\Models\Documento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreQuickProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_proveedores') || $this->user()?->can('registrar_compras');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'numero_documento' => $this->filled('numero_documento')
                ? preg_replace('/\s+/', '', strtoupper(trim($this->input('numero_documento'))))
                : null,
            'nombres' => $this->filled('nombres') ? trim($this->input('nombres')) : null,
            'apellidos' => $this->filled('apellidos') ? trim($this->input('apellidos')) : null,
            'razon_social' => $this->filled('razon_social') ? trim($this->input('razon_social')) : null,
            'email' => $this->filled('email') ? strtolower(trim($this->input('email'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'tipo_persona' => ['required', Rule::in(['natural', 'juridica'])],
            'documento_id' => ['required', 'integer', Rule::exists('documentos', 'id')],
            'numero_documento' => [
                'required',
                'string',
                'max:25',
                Rule::unique('personas', 'numero_documento')
                    ->where(fn ($query) => $query
                        ->where('documento_id', $this->input('documento_id'))
                        ->whereNull('deleted_at')),
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

                if ($documento && $documento->codigo === 'RUC') {
                    $validator->errors()->add('documento_id', 'Una persona natural no debe usar RUC.');
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