<?php

namespace App\Http\Requests;

use App\Enums\EstadoSesion;
use App\Enums\OrigenMovimientoCaja;
use App\Enums\TipoMovimiento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovimientoCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('movimientos_caja') ?? false;
    }

    public function rules(): array
    {
        return [
            'sesion_caja_id' => [
                'required',
                'integer',
                Rule::exists('sesiones_caja', 'id')->where(function ($query) {
                    $query->where('estado_sesion', EstadoSesion::ABIERTA->value);
                }),
            ],
            'tipo' => ['required', Rule::enum(TipoMovimiento::class)],
            'origen' => ['required', Rule::enum(OrigenMovimientoCaja::class)],
            'descripcion' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'referencia_type' => ['nullable', 'string', 'max:100'],
            'referencia_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}