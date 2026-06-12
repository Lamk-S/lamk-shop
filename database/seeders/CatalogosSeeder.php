<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

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
    }
}