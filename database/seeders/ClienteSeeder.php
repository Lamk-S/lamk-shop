<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\Persona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'razon_social' => 'Juan Carlos Pérez',
                'direccion' => 'Av. Los Olivos 123, Lima',
                'tipo_persona' => 'Natural',
                'documento_id' => 1, // DNI
                'numero_documento' => '72345678'
            ],
            [
                'razon_social' => 'Michael Smith',
                'direccion' => 'Calle Las Palmeras 456, Arequipa',
                'tipo_persona' => 'Natural',
                'documento_id' => 2, // Pasaporte
                'numero_documento' => 'P1234567'
            ],
            [
                'razon_social' => 'Inversiones ABC S.A.C.',
                'direccion' => 'Av. Argentina 1010, Callao',
                'tipo_persona' => 'Jurídica',
                'documento_id' => 3, // RUC
                'numero_documento' => '20123456789'
            ],
            [
                'razon_social' => 'Carlos Mendoza',
                'direccion' => 'Urb. San Borja Mz A Lt 3',
                'tipo_persona' => 'Natural',
                'documento_id' => 4, // Carnet Extranjería
                'numero_documento' => '001122334'
            ]
        ];

        foreach ($clientes as $item) {
            $persona = Persona::create([
                'razon_social' => $item['razon_social'],
                'direccion' => $item['direccion'],
                'tipo_persona' => $item['tipo_persona'],
                'documento_id' => $item['documento_id'],
                'numero_documento' => $item['numero_documento']
            ]);

            $persona->cliente()->create([
                'persona_id' => $persona->id
            ]);
        }
    }
}
