<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'integer', Rule::exists('proveedores', 'id')],
            'comprobante_id' => ['nullable', 'integer', Rule::exists('comprobantes', 'id')],
            'fecha_emision' => ['nullable', 'date'],
            'metodo_pago' => ['required', 'in:EFECTIVO,TARJETA,TRANSFERENCIA,CREDITO'],
            'moneda' => ['nullable', 'string', 'max:10'],
            'observacion' => ['nullable', 'string', 'max:1000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_variante_id' => ['required', 'integer', Rule::exists('producto_variantes', 'id')],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.costo_unitario' => ['required', 'numeric', 'min:0'],
            'detalles.*.precio_venta' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.descuento' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}