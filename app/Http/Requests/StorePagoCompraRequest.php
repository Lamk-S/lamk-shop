<?php

namespace App\Http\Requests;

use App\Models\CuentaPorPagar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePagoCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('registrar_compras') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'metodo_pago' => $this->filled('metodo_pago')
                ? strtoupper(trim((string) $this->input('metodo_pago')))
                : null,
            'referencia_operacion' => $this->filled('referencia_operacion')
                ? trim((string) $this->input('referencia_operacion'))
                : null,
            'observacion' => $this->filled('observacion')
                ? trim((string) $this->input('observacion'))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'cuenta_por_pagar_id' => [
                'required',
                'integer',
                Rule::exists('cuentas_por_pagar', 'id')->where(function ($query) {
                    $query->whereIn('estado', ['PENDIENTE', 'PARCIAL']);
                }),
            ],
            'metodo_pago' => ['required', Rule::in(['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'])],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'referencia_operacion' => ['nullable', 'string', 'max:100'],
            'observacion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $cuenta = CuentaPorPagar::find($this->input('cuenta_por_pagar_id'));

            if (! $cuenta) {
                return;
            }

            if ($cuenta->estado === 'ANULADA') {
                $validator->errors()->add('cuenta_por_pagar_id', 'No se puede pagar una cuenta anulada.');
                return;
            }

            $monto = (float) $this->input('monto', 0);
            $saldo = (float) $cuenta->saldo_pendiente;

            if ($monto > $saldo) {
                $validator->errors()->add('monto', 'El monto no puede superar el saldo pendiente.');
            }
        });
    }
}