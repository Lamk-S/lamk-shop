<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categorias = [
            ['nombre' => 'Zapatillas', 'descripcion' => 'Calzado deportivo y casual'],
            ['nombre' => 'Ropa Deportiva', 'descripcion' => 'Polos, shorts, buzos y casacas'],
            ['nombre' => 'Accesorios', 'descripcion' => 'Mochilas, gorras, botellas y complementos'],
            ['nombre' => 'Outlet', 'descripcion' => 'Productos con descuento'],
        ];

        foreach ($categorias as $item) {
            DB::table('categorias')->updateOrInsert(
                ['nombre' => $item['nombre']],
                [
                    'descripcion' => $item['descripcion'],
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $marcas = [
            ['nombre' => 'Nike', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Adidas', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Puma', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Under Armour', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Reebok', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'New Balance', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Asics', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Skechers', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Fila', 'descripcion' => 'Marca deportiva'],
            ['nombre' => 'Champion', 'descripcion' => 'Marca deportiva'],
        ];

        foreach ($marcas as $item) {
            DB::table('marcas')->updateOrInsert(
                ['nombre' => $item['nombre']],
                [
                    'descripcion' => $item['descripcion'],
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $presentaciones = [
            ['nombre' => 'Par', 'sigla' => 'PAR', 'descripcion' => 'Presentación por par'],
            ['nombre' => 'Unidad', 'sigla' => 'UND', 'descripcion' => 'Presentación por unidad'],
            ['nombre' => 'Pack', 'sigla' => 'PK', 'descripcion' => 'Presentación por paquete'],
            ['nombre' => 'Set', 'sigla' => 'SET', 'descripcion' => 'Presentación por conjunto'],
        ];

        foreach ($presentaciones as $item) {
            DB::table('presentaciones')->updateOrInsert(
                ['nombre' => $item['nombre']],
                [
                    'sigla' => $item['sigla'],
                    'descripcion' => $item['descripcion'],
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}