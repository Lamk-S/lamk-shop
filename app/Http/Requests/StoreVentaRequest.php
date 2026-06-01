<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'comprobante_id' => 'nullable|integer|exists:comprobantes,id',
            'fecha_hora' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',

            'metodo_pago' => 'required|in:EFECTIVO,TARJETA,TRANSFERENCIA',
            'monto_recibido' => 'required|numeric|min:0',
            'vuelto_entregado' => 'nullable|numeric|min:0',

            'arrayidproducto' => 'required|array|min:1',
            'arrayidproducto.*' => 'integer|exists:productos,id',
            'arraycantidad' => 'required|array|min:1',
            'arraycantidad.*' => 'integer|min:1',
            'arrayprecioventa' => 'required|array|min:1',
            'arrayprecioventa.*' => 'numeric|min:0',
            'arraydescuento' => 'required|array|min:1',
            'arraydescuento.*' => 'numeric|min:0',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $metodo = strtoupper((string) $this->input('metodo_pago'));
            $total = (float) $this->input('total', 0);
            $montoRecibido = (float) $this->input('monto_recibido', 0);

            if ($metodo === 'EFECTIVO' && $montoRecibido < $total) {
                $validator->errors()->add('monto_recibido', 'En efectivo, el monto recibido debe ser mayor o igual al total.');
            }

            if (in_array($metodo, ['TARJETA', 'TRANSFERENCIA'], true) && abs($montoRecibido - $total) > 0.01) {
                $validator->errors()->add('monto_recibido', 'En tarjeta o transferencia, el monto recibido debe coincidir con el total.');
            }
        });
    }
}