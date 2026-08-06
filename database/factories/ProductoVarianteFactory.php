<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Talla;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoVarianteFactory extends Factory
{
    protected $model = ProductoVariante::class;

    public function definition(): array
    {
        return [
            'producto_id' => Producto::factory(),
            'talla_id' => Talla::factory(),
            'codigo_variante' => $this->faker->unique()->bothify('VAR-####'),
            'stock_actual' => $this->faker->numberBetween(10, 50),
            'estado' => 1,
        ];
    }
}