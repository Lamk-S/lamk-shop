<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class ClienteGenericoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $dni = Documento::where('codigo', 'DNI')->firstOrFail();

        $persona = Persona::updateOrCreate(
            [
                'documento_id' => $dni->id,
                'numero_documento' => '00000000',
            ],
            [
                'tipo_persona' => 'natural',
                'nombres' => 'CONSUMIDOR',
                'apellidos' => 'FINAL',
                'razon_social' => null,
                'direccion' => null,
                'telefono' => null,
                'email' => null,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        Cliente::updateOrCreate(
            ['persona_id' => $persona->id],
            [
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}