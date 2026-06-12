<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use App\Models\Comprobante;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('registrar_ventas') ?? false;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', Rule::exists('clientes', 'id')],
            'comprobante_id' => ['nullable', 'integer', Rule::exists('comprobantes', 'id')],
            'fecha_emision' => ['nullable', 'date'],
            'observacion' => ['nullable', 'string', 'max:1000'],
            'moneda' => ['nullable', 'string', 'max:10'],

            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_variante_id' => ['required', 'integer', Rule::exists('producto_variantes', 'id')],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'detalles.*.descuento' => ['nullable', 'numeric', 'min:0'],

            'pagos' => ['nullable', 'array', 'min:1'],
            'pagos.*.metodo_pago' => ['required_with:pagos', Rule::in(['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'])],
            'pagos.*.monto' => ['required_with:pagos', 'numeric', 'min:0.01'],
            'pagos.*.referencia_operacion' => ['nullable', 'string', 'max:100'],

            'metodo_pago' => ['nullable', Rule::in(['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'])],
            'monto_recibido' => ['nullable', 'numeric', 'min:0'],
            'referencia_operacion' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $pagos = $this->input('pagos', []);
            $metodoPago = $this->input('metodo_pago');
            $clienteId = $this->input('cliente_id');
            $comprobanteId = $this->input('comprobante_id');

            if (empty($pagos) && empty($metodoPago)) {
                $validator->errors()->add('metodo_pago', 'Debes registrar al menos un método de pago.');
            }

            if ($comprobanteId) {
                $comprobante = Comprobante::find($comprobanteId);

                if ($comprobante?->tipo_comprobante === 'FACTURA' && !$clienteId) {
                    $validator->errors()->add('cliente_id', 'La factura requiere un cliente identificado.');
                }

                if ($comprobante?->tipo_comprobante === 'FACTURA' && $clienteId) {
                    $cliente = Cliente::with('persona.documento')->find($clienteId);

                    if (!$cliente || !$cliente->persona?->documento || $cliente->persona->documento->codigo !== 'RUC') {
                        $validator->errors()->add('cliente_id', 'La factura solo puede emitirse a un cliente con RUC.');
                    }
                }
            }
        });
    }
}