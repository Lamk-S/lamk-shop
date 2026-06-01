<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => 'nullable|integer|exists:proveedores,id',
            'comprobante_id' => 'nullable|integer|exists:comprobantes,id',
            'metodo_pago' => 'required|in:EFECTIVO,TARJETA,TRANSFERENCIA',
            'fecha_hora' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'nullable|boolean',

            'arrayidproducto' => 'required|array|min:1',
            'arrayidproducto.*' => 'integer|exists:productos,id',
            'arraycantidad' => 'required|array|min:1',
            'arraycantidad.*' => 'integer|min:1',
            'arraypreciocompra' => 'required|array|min:1',
            'arraypreciocompra.*' => 'numeric|min:0',
            'arrayprecioventa' => 'required|array|min:1',
            'arrayprecioventa.*' => 'numeric|min:0',
        ];
    }
}