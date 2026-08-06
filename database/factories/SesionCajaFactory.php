<?php

namespace Database\Factories;

use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\User;
use App\Enums\EstadoSesion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SesionCajaFactory extends Factory
{
    protected $model = SesionCaja::class;

    public function definition(): array
    {
        return [
            // Si no se le pasa una caja o usuario en el test, el factory creará unos nuevos por defecto
            'caja_id' => Caja::factory(),
            'user_id' => User::factory(),
            'fecha_hora_apertura' => now(),
            'saldo_inicial' => 100.00,
            'saldo_final_esperado' => 100.00,
            'estado_sesion' => EstadoSesion::ABIERTA,
            'observacion_apertura' => $this->faker->sentence(),
        ];
    }
}