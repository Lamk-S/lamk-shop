<?php

namespace App\Http\Requests;

use App\Models\Proveedor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCompraRequest extends FormRequest
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
            'moneda' => $this->filled('moneda')
                ? strtoupper(trim((string) $this->input('moneda')))
                : 'PEN',
            'observacion' => $this->filled('observacion')
                ? trim((string) $this->input('observacion'))
                : null,
            'fecha_vencimiento' => $this->filled('fecha_vencimiento')
                ? $this->input('fecha_vencimiento')
                : null,
            'actualizar_precio_venta' => filter_var(
                $this->input('actualizar_precio_venta', false),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ),
            'precio_venta' => $this->filled('precio_venta')
                ? $this->input('precio_venta')
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'integer', Rule::exists('proveedores', 'id')],
            'comprobante_id' => ['nullable', 'integer', Rule::exists('comprobantes', 'id')],
            'fecha_emision' => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_emision'],
            'metodo_pago' => ['required', Rule::in(['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'CREDITO', 'MIXTO'])],
            'moneda' => ['nullable', 'string', 'max:10'],
            'observacion' => ['nullable', 'string', 'max:1000'],

            'actualizar_precio_venta' => ['nullable', 'boolean'],
            'precio_venta' => ['nullable', 'numeric', 'min:0'],

            'pagos' => ['nullable', 'array'],
            'pagos.*.metodo_pago' => ['required_with:pagos', Rule::in(['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'])],
            'pagos.*.monto' => ['required_with:pagos', 'numeric', 'min:0.01'],
            'pagos.*.referencia_operacion' => ['nullable', 'string', 'max:100'],
            'pagos.*.observacion' => ['nullable', 'string', 'max:255'],

            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_variante_id' => ['required', 'integer', Rule::exists('producto_variantes', 'id')],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.costo_unitario' => ['required', 'numeric', 'min:0'],
            'detalles.*.descuento' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $proveedorId = $this->input('proveedor_id');
            $metodoPago = strtoupper((string) $this->input('metodo_pago'));
            $pagos = collect($this->input('pagos', []))->filter(function ($row) {
                return !empty($row['metodo_pago']) && isset($row['monto']) && (float) $row['monto'] > 0;
            });

            if ($proveedorId) {
                $proveedor = Proveedor::with('persona.documento')->find($proveedorId);

                if (! $proveedor) {
                    $validator->errors()->add('proveedor_id', 'El proveedor seleccionado no existe.');
                    return;
                }

                if (! $proveedor->persona || ! $proveedor->persona->documento) {
                    $validator->errors()->add('proveedor_id', 'El proveedor debe estar correctamente identificado.');
                }

                if ($proveedor->persona?->tipo_persona === 'juridica' && $proveedor->persona?->documento?->codigo !== 'RUC') {
                    $validator->errors()->add('proveedor_id', 'El proveedor jurídico debe tener RUC.');
                }
            }

            if ($metodoPago === 'CREDITO' && ! $this->filled('fecha_vencimiento')) {
                $validator->errors()->add('fecha_vencimiento', 'La fecha de vencimiento es obligatoria para compras a crédito.');
            }

            if ($metodoPago === 'MIXTO' && $pagos->isEmpty()) {
                $validator->errors()->add('pagos', 'Para una compra mixta debes registrar al menos un pago.');
            }

            if (in_array($metodoPago, ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA'], true) && $pagos->isNotEmpty()) {
                $validator->errors()->add('pagos', 'Si eliges un método de pago simple no debes enviar pagos múltiples.');
            }

            $totalPagos = round((float) $pagos->sum(fn ($row) => (float) $row['monto']), 2);
            if ($metodoPago === 'MIXTO' && $totalPagos <= 0) {
                $validator->errors()->add('pagos', 'El total de pagos debe ser mayor a cero.');
            }

            if ($this->filled('precio_venta') && ! $this->boolean('actualizar_precio_venta')) {
                $validator->errors()->add('precio_venta', 'Si deseas cambiar el precio de venta debes activar la opción correspondiente.');
            }

            foreach ((array) $this->input('detalles', []) as $index => $detalle) {
                if (!empty($detalle['descuento']) && (float) $detalle['descuento'] < 0) {
                    $validator->errors()->add("detalles.$index.descuento", 'El descuento no puede ser negativo.');
                }
            }
        });
    }
}