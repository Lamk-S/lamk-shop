<?php

namespace App\Services;

use App\Models\Comprobante;
use Illuminate\Support\Facades\DB;

class ComprobanteService
{
    public function obtenerDisponibleParaVenta(?int $comprobanteId = null): ?Comprobante
    {
        if ($comprobanteId) {
            return Comprobante::query()
                ->whereKey($comprobanteId)
                ->where('uso_comprobante', 'VENTA')
                ->where('estado', 1)
                ->first();
        }

        return Comprobante::query()
            ->where('uso_comprobante', 'VENTA')
            ->where('tipo_comprobante', 'TICKET')
            ->where('estado', 1)
            ->orderBy('serie')
            ->first();
    }

    public function obtenerDisponibleParaCompra(?int $comprobanteId = null): ?Comprobante
    {
        if ($comprobanteId) {
            return Comprobante::query()
                ->whereKey($comprobanteId)
                ->where('uso_comprobante', 'COMPRA')
                ->where('estado', 1)
                ->first();
        }

        return null;
    }

    public function reservarCorrelativo(Comprobante $comprobante): array
    {
        return DB::transaction(function () use ($comprobante) {
            $locked = Comprobante::query()
                ->whereKey($comprobante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $nuevoCorrelativo = (int) $locked->correlativo_actual + 1;

            $locked->update([
                'correlativo_actual' => $nuevoCorrelativo,
            ]);

            return [
                'comprobante_id' => $locked->id,
                'tipo_comprobante' => $locked->tipo_comprobante,
                'serie' => $locked->serie,
                'correlativo' => str_pad((string) $nuevoCorrelativo, 8, '0', STR_PAD_LEFT),
            ];
        });
    }
}