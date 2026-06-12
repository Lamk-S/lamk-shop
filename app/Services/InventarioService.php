<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Kardex;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventarioService
{
    public function obtenerVarianteBloqueada(int $productoVarianteId): ProductoVariante
    {
        return ProductoVariante::query()
            ->with(['producto', 'talla'])
            ->whereKey($productoVarianteId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function validarStockDisponible(ProductoVariante $variante, int $cantidad): void
    {
        if ($variante->stock_actual < $cantidad) {
            throw new RuntimeException("Stock insuficiente para la variante {$variante->codigo_variante}.");
        }
    }

    public function registrarEntrada(
        ProductoVariante $variante,
        int $cantidad,
        float $costoUnitario,
        string $descripcion,
        Model $origen,
        ?User $user = null
    ): Kardex {
        return DB::transaction(function () use ($variante, $cantidad, $costoUnitario, $descripcion, $origen, $user) {
            $locked = ProductoVariante::query()
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $saldoAnterior + $cantidad;

            $locked->update([
                'stock_actual' => $saldoPosterior,
            ]);

            $this->recalcularStockProducto($locked->producto_id);

            return Kardex::create([
                'producto_variante_id' => $locked->id,
                'tipo_transaccion' => 'COMPRA',
                'origen_type' => get_class($origen),
                'origen_id' => $origen->id,
                'descripcion' => $descripcion,
                'entrada' => $cantidad,
                'salida' => 0,
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoPosterior,
                'costo_unitario' => $costoUnitario,
                'costo_total' => round($cantidad * $costoUnitario, 2),
                'user_id' => $user?->id,
            ]);
        });
    }

    public function registrarSalida(
        ProductoVariante $variante,
        int $cantidad,
        float $costoUnitario,
        string $descripcion,
        Model $origen,
        ?User $user = null,
        string $tipoTransaccion = 'VENTA'
    ): Kardex {
        return DB::transaction(function () use ($variante, $cantidad, $costoUnitario, $descripcion, $origen, $user, $tipoTransaccion) {
            $locked = ProductoVariante::query()
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->validarStockDisponible($locked, $cantidad);

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $saldoAnterior - $cantidad;

            $locked->update([
                'stock_actual' => $saldoPosterior,
            ]);

            $this->recalcularStockProducto($locked->producto_id);

            return Kardex::create([
                'producto_variante_id' => $locked->id,
                'tipo_transaccion' => $tipoTransaccion,
                'origen_type' => get_class($origen),
                'origen_id' => $origen->id,
                'descripcion' => $descripcion,
                'entrada' => 0,
                'salida' => $cantidad,
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoPosterior,
                'costo_unitario' => $costoUnitario,
                'costo_total' => round($cantidad * $costoUnitario, 2),
                'user_id' => $user?->id,
            ]);
        });
    }

    public function revertirEntrada(
        ProductoVariante $variante,
        int $cantidad,
        float $costoUnitario,
        string $descripcion,
        Model $origen,
        ?User $user = null
    ): Kardex {
        return DB::transaction(function () use ($variante, $cantidad, $costoUnitario, $descripcion, $origen, $user) {
            $locked = ProductoVariante::query()
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->stock_actual < $cantidad) {
                throw new RuntimeException("No se puede revertir la compra porque la variante {$locked->codigo_variante} no tiene stock suficiente.");
            }

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $saldoAnterior - $cantidad;

            $locked->update([
                'stock_actual' => $saldoPosterior,
            ]);

            $this->recalcularStockProducto($locked->producto_id);

            return Kardex::create([
                'producto_variante_id' => $locked->id,
                'tipo_transaccion' => 'ANULACION',
                'origen_type' => get_class($origen),
                'origen_id' => $origen->id,
                'descripcion' => $descripcion,
                'entrada' => 0,
                'salida' => $cantidad,
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoPosterior,
                'costo_unitario' => $costoUnitario,
                'costo_total' => round($cantidad * $costoUnitario, 2),
                'user_id' => $user?->id,
            ]);
        });
    }

    public function revertirSalida(
        ProductoVariante $variante,
        int $cantidad,
        float $costoUnitario,
        string $descripcion,
        Model $origen,
        ?User $user = null
    ): Kardex {
        return DB::transaction(function () use ($variante, $cantidad, $costoUnitario, $descripcion, $origen, $user) {
            $locked = ProductoVariante::query()
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $saldoAnterior + $cantidad;

            $locked->update([
                'stock_actual' => $saldoPosterior,
            ]);

            $this->recalcularStockProducto($locked->producto_id);

            return Kardex::create([
                'producto_variante_id' => $locked->id,
                'tipo_transaccion' => 'ANULACION',
                'origen_type' => get_class($origen),
                'origen_id' => $origen->id,
                'descripcion' => $descripcion,
                'entrada' => $cantidad,
                'salida' => 0,
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoPosterior,
                'costo_unitario' => $costoUnitario,
                'costo_total' => round($cantidad * $costoUnitario, 2),
                'user_id' => $user?->id,
            ]);
        });
    }

    public function registrarAjuste(
        ProductoVariante $variante,
        int $nuevoStock,
        string $descripcion,
        ?User $user = null
    ): Kardex {
        return DB::transaction(function () use ($variante, $nuevoStock, $descripcion, $user) {
            $locked = ProductoVariante::query()
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $nuevoStock;
            $entrada = max(0, $saldoPosterior - $saldoAnterior);
            $salida = max(0, $saldoAnterior - $saldoPosterior);

            $locked->update([
                'stock_actual' => $saldoPosterior,
            ]);

            $this->recalcularStockProducto($locked->producto_id);

            return Kardex::create([
                'producto_variante_id' => $locked->id,
                'tipo_transaccion' => 'AJUSTE',
                'origen_type' => null,
                'origen_id' => null,
                'descripcion' => $descripcion,
                'entrada' => $entrada,
                'salida' => $salida,
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoPosterior,
                'costo_unitario' => 0,
                'costo_total' => 0,
                'user_id' => $user?->id,
            ]);
        });
    }

    public function recalcularStockProducto(int $productoId): int
    {
        $total = (int) ProductoVariante::query()
            ->where('producto_id', $productoId)
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->sum('stock_actual');

        Producto::query()
            ->whereKey($productoId)
            ->update([
                'updated_at' => now(),
            ]);

        return $total;
    }
}