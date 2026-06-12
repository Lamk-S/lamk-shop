<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoVariantesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $productos = DB::table('productos')->orderBy('codigo')->get();
        $tallas = DB::table('tallas')->pluck('id', 'codigo')->toArray();

        $shoeSizes = ['38', '39', '40', '41', '42', '43'];
        $shoeWeights = [1, 2, 3, 3, 2, 1];

        $clothingSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $clothingWeights = [1, 2, 3, 3, 2, 1];

        $uniqueSizes = ['UNICA'];
        $uniqueWeights = [1];

        foreach ($productos as $index => $producto) {
            $sizes = [];
            $weights = [];

            if ($producto->maneja_tallas) {
                if ($producto->tipo_producto === 'ZAPATILLA') {
                    $sizes = $shoeSizes;
                    $weights = $shoeWeights;
                    $totalStock = [12, 14, 16, 18][$index % 4];
                } else {
                    $sizes = $clothingSizes;
                    $weights = $clothingWeights;
                    $totalStock = [18, 21, 24, 27][$index % 4];
                }
            } else {
                $sizes = $uniqueSizes;
                $weights = $uniqueWeights;
                $totalStock = [10, 12, 14, 16][$index % 4];
            }

            $distribution = $this->distribute($totalStock, $weights);

            foreach ($sizes as $i => $sizeCode) {
                $tallaId = $tallas[$sizeCode] ?? null;

                if (! $tallaId) {
                    continue;
                }

                $cantidad = (int) ($distribution[$i] ?? 0);
                $variantCode = $producto->codigo . '-' . $sizeCode;

                DB::table('producto_variantes')->updateOrInsert(
                    [
                        'producto_id' => $producto->id,
                        'talla_id' => $tallaId,
                    ],
                    [
                        'codigo_variante' => $variantCode,
                        'codigo_barra' => $variantCode,
                        'stock_actual' => $cantidad,
                        'stock_minimo' => $producto->tipo_producto === 'ACCESORIO' ? 3 : 1,
                        'estado' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    private function distribute(int $total, array $weights): array
    {
        $weightSum = array_sum($weights);
        $floor = [];
        $remainders = [];

        foreach ($weights as $i => $weight) {
            $value = ($total * $weight) / $weightSum;
            $floor[$i] = (int) floor($value);
            $remainders[$i] = $value - $floor[$i];
        }

        $distributed = array_sum($floor);
        $remaining = $total - $distributed;

        arsort($remainders);
        $keys = array_keys($remainders);
        $countKeys = count($keys);

        for ($i = 0; $i < $remaining; $i++) {
            $floor[$keys[$i % $countKeys]]++;
        }

        ksort($floor);

        return array_values($floor);
    }
}