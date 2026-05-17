<?php

namespace Database\Seeders;

use App\Models\Caracteristica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresentacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presentaciones = [
            ['nombre' => 'Unidad', 'sigla' => 'UND', 'descripcion' => 'Producto vendido por pieza individual.'],
            ['nombre' => 'Kilogramo', 'sigla' => 'KG', 'descripcion' => 'Unidad de medida de peso (1000g).'],
            ['nombre' => 'Litro', 'sigla' => 'LT', 'descripcion' => 'Unidad de medida de volumen para líquidos.'],
            ['nombre' => 'Caja', 'sigla' => 'CAJ', 'descripcion' => 'Empaque de cartón con múltiples unidades.'],
            ['nombre' => 'Paquete', 'sigla' => 'PAQ', 'descripcion' => 'Envoltorio que agrupa varios artículos.'],
            ['nombre' => 'Docena', 'sigla' => 'DOC', 'descripcion' => 'Agrupación de 12 unidades del mismo producto.'],
            ['nombre' => 'Frasco', 'sigla' => 'FCO', 'descripcion' => 'Envase de vidrio o plástico rígido.'],
            ['nombre' => 'Bolsa', 'sigla' => 'BOL', 'descripcion' => 'Empaque flexible de plástico o papel.'],
            ['nombre' => 'Lata', 'sigla' => 'LAT', 'descripcion' => 'Envase metálico para conservas o bebidas.'],
            ['nombre' => 'Botella', 'sigla' => 'BOT', 'descripcion' => 'Envase cilíndrico, generalmente para líquidos.']
        ];

        foreach ($presentaciones as $item) {
            $caracteristica = Caracteristica::create([
                'nombre' => $item['nombre'],
                'descripcion' => $item['descripcion'],
                'estado' => 1
            ]);
            $caracteristica->presentacione()->create([
                'caracteristica_id' => $caracteristica->id,
                'sigla' => $item['sigla']
            ]);
        }
    }
}
