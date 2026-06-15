<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Documento;
use App\Models\ProductoVariante;
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
            'detalles.*.producto_variante_id' => [
                'required',
                'integer',
                Rule::exists('producto_variantes', 'id')->where(function ($query) {
                    $query->where('estado', 1)->whereNull('deleted_at');
                }),
            ],
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
            $detalles = collect($this->input('detalles', []))
                ->filter(fn ($row) => !empty($row['producto_variante_id']))
                ->values();

            if ($detalles->isEmpty()) {
                $validator->errors()->add('detalles', 'Debes agregar al menos un producto al detalle.');
                return;
            }

            $ids = $detalles->pluck('producto_variante_id')->map(fn ($id) => (int) $id);
            if ($ids->count() !== $ids->unique()->count()) {
                $validator->errors()->add('detalles', 'No repitas la misma variante en líneas separadas. El sistema necesita una sola línea por variante.');
            }

            $variantes = ProductoVariante::with(['producto', 'talla'])
                ->whereIn('id', $ids->unique()->values())
                ->get()
                ->keyBy('id');

            $cantidadPorVariante = [];

            foreach ($detalles as $index => $row) {
                $variantId = (int) ($row['producto_variante_id'] ?? 0);
                $cantidad = (int) ($row['cantidad'] ?? 0);
                $precio = (float) ($row['precio_unitario'] ?? 0);

                if ($cantidad < 1) {
                    $validator->errors()->add("detalles.$index.cantidad", 'La cantidad debe ser mayor a cero.');
                    continue;
                }

                if ($precio < 0) {
                    $validator->errors()->add("detalles.$index.precio_unitario", 'El precio unitario no puede ser negativo.');
                    continue;
                }

                $variante = $variantes->get($variantId);

                if (!$variante) {
                    $validator->errors()->add("detalles.$index.producto_variante_id", 'La variante seleccionada no existe o está inactiva.');
                    continue;
                }

                $cantidadPorVariante[$variantId] = ($cantidadPorVariante[$variantId] ?? 0) + $cantidad;
            }

            foreach ($cantidadPorVariante as $variantId => $totalCantidad) {
                $variante = $variantes->get($variantId);

                if (!$variante) {
                    continue;
                }

                if ($totalCantidad > (int) $variante->stock_actual) {
                    $producto = $variante->producto?->nombre ?? 'Producto';
                    $talla = $variante->talla?->nombre ?? 'Sin talla';
                    $validator->errors()->add(
                        'detalles',
                        "Stock insuficiente para {$producto} / {$talla}. Disponible: {$variante->stock_actual}, solicitado: {$totalCantidad}."
                    );
                }
            }

            $comprobanteId = $this->input('comprobante_id');
            $clienteId = $this->input('cliente_id');

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

            $pagos = collect($this->input('pagos', []));
            $metodoPago = $this->input('metodo_pago');

            if ($pagos->isEmpty() && empty($metodoPago)) {
                $validator->errors()->add('metodo_pago', 'Debes registrar al menos un método de pago.');
            }
        });
    }
}