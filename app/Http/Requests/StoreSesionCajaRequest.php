<?php

namespace App\Http\Requests;

use App\Models\SesionCaja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSesionCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('abrir_caja') ?? false;
    }

    public function rules(): array
    {
        return [
            'caja_id' => ['required', 'integer', Rule::exists('cajas', 'id')],
            'saldo_inicial' => ['nullable', 'numeric', 'min:0'],
            'observacion_apertura' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $cajaId = $this->input('caja_id');

            if (!$cajaId) {
                return;
            }

            $cajaYaAbierta = SesionCaja::where('caja_id', $cajaId)
                ->where('estado_sesion', 'ABIERTA')
                ->exists();

            if ($cajaYaAbierta) {
                $validator->errors()->add('caja_id', 'Esta caja ya tiene una sesión abierta.');
            }

            $usuarioYaAbierto = SesionCaja::where('user_id', $this->user()?->id)
                ->where('estado_sesion', 'ABIERTA')
                ->exists();

            if ($usuarioYaAbierto) {
                $validator->errors()->add('caja_id', 'Ya tienes una sesión de caja abierta.');
            }
        });
    }
}