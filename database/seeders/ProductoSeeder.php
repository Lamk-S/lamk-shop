<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'codigo' => 'PROD-001',
                'nombre' => 'Leche Evaporada Entera',
                'descripcion' => 'Leche evaporada enriquecida con vitaminas A y D.',
                'fecha_vencimiento' => '2027-12-31',
                'marca_id' => 1, // Gloria
                'presentacione_id' => 9, // Lata
                'categorias' => [1, 2] // Abarrotes, Lácteos
            ],
            [
                'codigo' => 'PROD-002',
                'nombre' => 'Gaseosa Sabor Original 3L',
                'descripcion' => 'Bebida carbonatada tamaño familiar.',
                'fecha_vencimiento' => '2026-10-15',
                'marca_id' => 2, // Coca-Cola
                'presentacione_id' => 10, // Botella
                'categorias' => [3] // Bebidas
            ],
            [
                'codigo' => 'PROD-003',
                'nombre' => 'Café Soluble Clásico 200g',
                'descripcion' => 'Café instantáneo de tueste medio.',
                'fecha_vencimiento' => '2028-05-20',
                'marca_id' => 3, // Nestlé
                'presentacione_id' => 7, // Frasco
                'categorias' => [1] // Abarrotes
            ],
            [
                'codigo' => 'PROD-004',
                'nombre' => 'Aceite Vegetal Premium 1L',
                'descripcion' => 'Aceite de soya ideal para freír y cocinar.',
                'fecha_vencimiento' => '2027-08-10',
                'marca_id' => 4, // Alicorp
                'presentacione_id' => 10, // Botella
                'categorias' => [1] // Abarrotes
            ],
            [
                'codigo' => 'PROD-005',
                'nombre' => 'Hot Dog de Pollo 500g',
                'descripcion' => 'Embutido de pollo empacado al vacío.',
                'fecha_vencimiento' => '2026-06-30',
                'marca_id' => 5, // San Fernando
                'presentacione_id' => 5, // Paquete
                'categorias' => [6, 8] // Carnes y Embutidos, Congelados
            ],
            [
                'codigo' => 'PROD-006',
                'nombre' => 'Shampoo Anticaspa 400ml',
                'descripcion' => 'Shampoo con fórmula refrescante para uso diario.',
                'fecha_vencimiento' => '2029-01-01',
                'marca_id' => 6, // P&G
                'presentacione_id' => 10, // Botella
                'categorias' => [5] // Cuidado Personal
            ],
            [
                'codigo' => 'PROD-007',
                'nombre' => 'Pan de Molde Blanco',
                'descripcion' => 'Pan de molde suave, ideal para desayunos.',
                'fecha_vencimiento' => '2026-05-25',
                'marca_id' => 7, // Bimbo
                'presentacione_id' => 8, // Bolsa
                'categorias' => [7] // Panadería
            ],
            [
                'codigo' => 'PROD-008',
                'nombre' => 'Alimento para Perros Adultos 15kg',
                'descripcion' => 'Croquetas con sabor a carne y cereales.',
                'fecha_vencimiento' => '2027-02-15',
                'marca_id' => 8, // Ricocan
                'presentacione_id' => 8, // Bolsa
                'categorias' => [9] // Mascotas
            ],
            [
                'codigo' => 'PROD-009',
                'nombre' => 'Lejía Tradicional 1L',
                'descripcion' => 'Desinfectante y blanqueador para ropa y superficies.',
                'fecha_vencimiento' => '2028-11-20',
                'marca_id' => 9, // Clorox
                'presentacione_id' => 10, // Botella
                'categorias' => [4] // Limpieza
            ],
            [
                'codigo' => 'PROD-010',
                'nombre' => 'Cinta Adhesiva Transparente',
                'descripcion' => 'Cinta de embalaje de alta resistencia.',
                'fecha_vencimiento' => null, // No vence
                'marca_id' => 10, // 3M
                'presentacione_id' => 1, // Unidad
                'categorias' => [10] // Ferretería
            ]
        ];

        foreach ($productos as $item) {
            $producto = Producto::create([
                'codigo' => $item['codigo'],
                'nombre' => $item['nombre'],
                'descripcion' => $item['descripcion'],
                'fecha_vencimiento' => $item['fecha_vencimiento'],
                'marca_id' => $item['marca_id'],
                'presentacione_id' => $item['presentacione_id'],
                'img_path' => null
            ]);

            $producto->categorias()->attach($item['categorias']);
        }
    }
}
