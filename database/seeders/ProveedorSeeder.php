<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            [
                'razon_social' => 'Distribuidora Lima S.A.C.',
                'direccion' => 'Av. Industrial 123, Lima',
                'tipo_persona' => 'Jurídica',
                'documento_id' => 3, // RUC
                'numero_documento' => '20111111111',
            ],
            [
                'razon_social' => 'Inversiones Global Perú S.A.C.',
                'direccion' => 'Av. Arequipa 456, Lima',
                'tipo_persona' => 'Jurídica',
                'documento_id' => 3, // RUC
                'numero_documento' => '20122222222',
            ],
            [
                'razon_social' => 'Comercial Andina E.I.R.L.',
                'direccion' => 'Jr. Amazonas 789, Huancayo',
                'tipo_persona' => 'Jurídica',
                'documento_id' => 3, // RUC
                'numero_documento' => '20133333333',
            ],
            [
                'razon_social' => 'Distribuciones Norte S.A.C.',
                'direccion' => 'Av. España 101, Trujillo',
                'tipo_persona' => 'Jurídica',
                'documento_id' => 3,
                'numero_documento' => '20144444444',
            ],
            [
                'razon_social' => 'Mayorista Sur Perú S.A.C.',
                'direccion' => 'Av. Los Incas 202, Arequipa',
                'tipo_persona' => 'Jurídica',
                'documento_id' => 3,
                'numero_documento' => '20155555555',
            ],
        ];

        foreach ($proveedores as $item) {

            $persona = Persona::create([
                'razon_social' => $item['razon_social'],
                'direccion' => $item['direccion'],
                'tipo_persona' => $item['tipo_persona'],
                'documento_id' => $item['documento_id'],
                'numero_documento' => $item['numero_documento']
            ]);

            $persona->proveedore()->create();
        }
    }
}
