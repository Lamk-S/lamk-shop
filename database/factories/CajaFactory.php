<?php

namespace Database\Factories;

use App\Models\Caja;
use Illuminate\Database\Eloquent\Factories\Factory;

class CajaFactory extends Factory
{
    protected $model = Caja::class;

    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->bothify('CAJA-###'), // <-- Campo agregado
            'nombre' => 'Caja ' . $this->faker->unique()->numberBetween(1, 10),
            'fondo_fijo' => 100.00,
            'estado' => 1,
        ];
    }
}