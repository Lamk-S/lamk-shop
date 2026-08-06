<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->ean13(),
            'nombre' => $this->faker->words(3, true),
            'tipo_producto' => 'ROPA',
            'precio_compra' => $this->faker->randomFloat(2, 10, 100),
            'precio_venta' => $this->faker->randomFloat(2, 110, 200), // Agregado por precaución
            'afecto_igv' => true,
            'estado' => 1,
        ];
    }
}