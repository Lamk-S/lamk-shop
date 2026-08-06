<?php

namespace Database\Factories;

use App\Models\Talla;
use Illuminate\Database\Eloquent\Factories\Factory;

class TallaFactory extends Factory
{
    protected $model = Talla::class;

    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->lexify('T-???'),
            'nombre' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'Unica', '40', '42']),
            'tipo_talla' => 'ROPA',
            'estado' => 1,
        ];
    }
}