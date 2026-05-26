<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Gloria', 'descripcion' => 'Marca principal de lácteos y conservas.'],
            ['nombre' => 'Coca-Cola', 'descripcion' => 'Bebidas carbonatadas y refrescos.'],
            ['nombre' => 'Nestlé', 'descripcion' => 'Chocolates, café y productos lácteos.'],
            ['nombre' => 'Alicorp', 'descripcion' => 'Fideos, salsas, aceites y harinas.'],
            ['nombre' => 'San Fernando', 'descripcion' => 'Carnes, pavita y embutidos variados.'],
            ['nombre' => 'Procter & Gamble (P&G)', 'descripcion' => 'Artículos de limpieza y cuidado personal.'],
            ['nombre' => 'Bimbo', 'descripcion' => 'Panes de molde, tortillas y postres.'],
            ['nombre' => 'Ricocan', 'descripcion' => 'Alimento balanceado para mascotas.'],
            ['nombre' => 'Clorox', 'descripcion' => 'Productos de desinfección y limpieza.'],
            ['nombre' => '3M', 'descripcion' => 'Cintas adhesivas, esponjas y limpieza especializada.'],
        ];

        foreach ($marcas as $item) {
            Marca::firstOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'descripcion' => $item['descripcion'],
                    'estado' => 1,
                ]
            );
        }
    }
}