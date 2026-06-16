<?php

namespace App\Services;

use App\Models\Kardex;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\User;
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

    public function obtenerCostoSalida(ProductoVariante $variante): float
    {
        $costo = (float) ($variante->costo_promedio
            ?: $variante->costo_ultimo_compra
            ?: $variante->producto?->precio_compra
            ?: 0);

        return round($costo, 2);
    }

    protected function obtenerCostoBaseEntrada(ProductoVariante $variante): float
    {
        $costo = (float) ($variante->costo_promedio
            ?: $variante->costo_ultimo_compra
            ?: $variante->producto?->precio_compra
            ?: 0);

        return round($costo, 2);
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
                ->with('producto')
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $saldoAnterior + $cantidad;

            $costoAnteriorPromedio = $this->obtenerCostoBaseEntrada($locked);

            $nuevoCostoPromedio = $saldoPosterior > 0
                ? round((($saldoAnterior * $costoAnteriorPromedio) + ($cantidad * $costoUnitario)) / $saldoPosterior, 2)
                : round($costoUnitario, 2);

            $locked->update([
                'stock_actual' => $saldoPosterior,
                'costo_ultimo_compra' => round($costoUnitario, 2),
                'costo_promedio' => $nuevoCostoPromedio,
                'ultima_compra_at' => now(),
            ]);

            Producto::query()
                ->whereKey($locked->producto_id)
                ->update([
                    'precio_compra' => round($costoUnitario, 2),
                    'updated_at' => now(),
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
                'costo_unitario' => round($costoUnitario, 2),
                'costo_total' => round($cantidad * $costoUnitario, 2),
                'user_id' => $user?->id,
            ]);
        });
    }

    public function registrarSalida(
        ProductoVariante $variante,
        int $cantidad,
        ?float $costoUnitario,
        string $descripcion,
        Model $origen,
        ?User $user = null,
        string $tipoTransaccion = 'VENTA'
    ): Kardex {
        return DB::transaction(function () use ($variante, $cantidad, $costoUnitario, $descripcion, $origen, $user, $tipoTransaccion) {
            $locked = ProductoVariante::query()
                ->with('producto')
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->validarStockDisponible($locked, $cantidad);

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $saldoAnterior - $cantidad;

            $costoSalida = $costoUnitario !== null && $costoUnitario > 0
                ? round($costoUnitario, 2)
                : $this->obtenerCostoSalida($locked);

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
                'costo_unitario' => $costoSalida,
                'costo_total' => round($cantidad * $costoSalida, 2),
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
                ->with('producto')
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $saldoAnterior = (int) $locked->stock_actual;

            if ($saldoAnterior < $cantidad) {
                throw new RuntimeException("No se puede revertir la compra porque la variante {$locked->codigo_variante} no tiene stock suficiente.");
            }

            $saldoPosterior = $saldoAnterior - $cantidad;

            $locked->update([
                'stock_actual' => $saldoPosterior,
                'costo_promedio' => $saldoPosterior > 0 ? $locked->costo_promedio : 0,
                'costo_ultimo_compra' => $saldoPosterior > 0 ? $locked->costo_ultimo_compra : 0,
                'ultima_compra_at' => $saldoPosterior > 0 ? $locked->ultima_compra_at : null,
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
                'costo_unitario' => round($costoUnitario, 2),
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
                ->with('producto')
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
                'costo_unitario' => round($costoUnitario, 2),
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
                ->with('producto')
                ->whereKey($variante->id)
                ->lockForUpdate()
                ->firstOrFail();

            $saldoAnterior = (int) $locked->stock_actual;
            $saldoPosterior = $nuevoStock;

            $entrada = max(0, $saldoPosterior - $saldoAnterior);
            $salida = max(0, $saldoAnterior - $saldoPosterior);

            $locked->update([
                'stock_actual' => $saldoPosterior,
                'costo_promedio' => $saldoPosterior > 0 ? $locked->costo_promedio : 0,
                'costo_ultimo_compra' => $saldoPosterior > 0 ? $locked->costo_ultimo_compra : 0,
                'ultima_compra_at' => $saldoPosterior > 0 ? $locked->ultima_compra_at : null,
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