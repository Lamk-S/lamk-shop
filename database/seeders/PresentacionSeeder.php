<?php

namespace Database\Seeders;

use App\Models\Presentacion;
use Illuminate\Database\Seeder;

class PresentacionSeeder extends Seeder
{
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
            ['nombre' => 'Botella', 'sigla' => 'BOT', 'descripcion' => 'Envase cilíndrico, generalmente para líquidos.'],
        ];

        foreach ($presentaciones as $item) {
            Presentacion::firstOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'sigla' => $item['sigla'],
                    'descripcion' => $item['descripcion'],
                    'estado' => 1,
                ]
            );
        }
    }
}