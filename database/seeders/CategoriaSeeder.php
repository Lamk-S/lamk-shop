<?php

namespace Database\Seeders;

use App\Models\Caracteristica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Abarrotes', 'descripcion' => 'Productos de despensa y consumo diario.'],
            ['nombre' => 'Lácteos', 'descripcion' => 'Leche, quesos, mantequillas y derivados.'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Aguas, gaseosas, jugos e infusiones.'],
            ['nombre' => 'Limpieza', 'descripcion' => 'Detergentes, lejías y artículos para el aseo del hogar.'],
            ['nombre' => 'Cuidado Personal', 'descripcion' => 'Shampoo, jabones, desodorantes y cremas.'],
            ['nombre' => 'Carnes y Embutidos', 'descripcion' => 'Pollo, res, cerdo, salchichas y jamones.'],
            ['nombre' => 'Panadería', 'descripcion' => 'Panes, galletas, tostadas y pastelería.'],
            ['nombre' => 'Congelados', 'descripcion' => 'Helados, verduras congeladas y comidas listas.'],
            ['nombre' => 'Mascotas', 'descripcion' => 'Alimento y accesorios para perros y gatos.'],
            ['nombre' => 'Ferretería', 'descripcion' => 'Herramientas básicas, pegamentos y focos.']
        ];

        foreach ($categorias as $item) {
            $caracteristica = Caracteristica::create([
                'nombre' => $item['nombre'],
                'descripcion' => $item['descripcion'],
                'estado' => 1
            ]);
            $caracteristica->categoria()->create(['caracteristica_id' => $caracteristica->id]);
        }
    }
}
