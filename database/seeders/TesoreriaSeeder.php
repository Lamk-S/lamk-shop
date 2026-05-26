<?php

namespace Database\Seeders;

use App\Models\Tesoreria;
use Illuminate\Database\Seeder;

class TesoreriaSeeder extends Seeder
{
    public function run(): void
    {
        Tesoreria::firstOrCreate(
            ['nombre' => 'Tesorería Principal'],
            [
                'saldo_efectivo' => 1000,
                'saldo_banco' => 1000,
                'estado' => 1,
            ]
        );
    }
}