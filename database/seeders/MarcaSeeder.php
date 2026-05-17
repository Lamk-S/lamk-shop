<?php

namespace Database\Seeders;

use App\Models\Caracteristica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
            ['nombre' => '3M', 'descripcion' => 'Cintas adhesivas, esponjas y limpieza especializada.']
        ];

        foreach ($marcas as $item) {
            $caracteristica = Caracteristica::create([
                'nombre' => $item['nombre'],
                'descripcion' => $item['descripcion'],
                'estado' => 1
            ]);
            $caracteristica->marca()->create(['caracteristica_id' => $caracteristica->id]);
        }
    }
}
