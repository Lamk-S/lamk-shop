<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'codigo' => 'PROD-001',
                'nombre' => 'Leche Evaporada Entera',
                'descripcion' => 'Leche evaporada enriquecida con vitaminas A y D.',
                'precio_compra' => 3.20,
                'precio_venta' => 4.50,
                'stock' => 120,
                'stock_minimo' => 20,
                'fecha_vencimiento' => '2027-12-31',
                'marca_id' => 1,
                'presentacion_id' => 9,
                'categorias' => [1, 2],
            ],
            [
                'codigo' => 'PROD-002',
                'nombre' => 'Gaseosa Sabor Original 3L',
                'descripcion' => 'Bebida carbonatada tamaño familiar.',
                'precio_compra' => 6.80,
                'precio_venta' => 9.50,
                'stock' => 90,
                'stock_minimo' => 15,
                'fecha_vencimiento' => '2027-10-15',
                'marca_id' => 2,
                'presentacion_id' => 10,
                'categorias' => [3],
            ],
            [
                'codigo' => 'PROD-003',
                'nombre' => 'Café Soluble Clásico 200g',
                'descripcion' => 'Café instantáneo de tueste medio.',
                'precio_compra' => 10.50,
                'precio_venta' => 14.90,
                'stock' => 60,
                'stock_minimo' => 10,
                'fecha_vencimiento' => '2028-05-20',
                'marca_id' => 3,
                'presentacion_id' => 7,
                'categorias' => [1],
            ],
            [
                'codigo' => 'PROD-004',
                'nombre' => 'Aceite Vegetal Premium 1L',
                'descripcion' => 'Aceite de soya ideal para freír y cocinar.',
                'precio_compra' => 7.10,
                'precio_venta' => 10.20,
                'stock' => 70,
                'stock_minimo' => 12,
                'fecha_vencimiento' => '2027-08-10',
                'marca_id' => 4,
                'presentacion_id' => 10,
                'categorias' => [1],
            ],
            [
                'codigo' => 'PROD-005',
                'nombre' => 'Hot Dog de Pollo 500g',
                'descripcion' => 'Embutido de pollo empacado al vacío.',
                'precio_compra' => 8.90,
                'precio_venta' => 12.50,
                'stock' => 50,
                'stock_minimo' => 8,
                'fecha_vencimiento' => '2027-06-30',
                'marca_id' => 5,
                'presentacion_id' => 5,
                'categorias' => [6, 8],
            ],
            [
                'codigo' => 'PROD-006',
                'nombre' => 'Shampoo Anticaspa 400ml',
                'descripcion' => 'Shampoo con fórmula refrescante para uso diario.',
                'precio_compra' => 9.20,
                'precio_venta' => 13.90,
                'stock' => 40,
                'stock_minimo' => 6,
                'fecha_vencimiento' => '2029-01-01',
                'marca_id' => 6,
                'presentacion_id' => 10,
                'categorias' => [5],
            ],
            [
                'codigo' => 'PROD-007',
                'nombre' => 'Pan de Molde Blanco',
                'descripcion' => 'Pan de molde suave, ideal para desayunos.',
                'precio_compra' => 4.00,
                'precio_venta' => 5.80,
                'stock' => 80,
                'stock_minimo' => 10,
                'fecha_vencimiento' => '2027-05-25',
                'marca_id' => 7,
                'presentacion_id' => 8,
                'categorias' => [7],
            ],
            [
                'codigo' => 'PROD-008',
                'nombre' => 'Alimento para Perros Adultos 15kg',
                'descripcion' => 'Croquetas con sabor a carne y cereales.',
                'precio_compra' => 38.00,
                'precio_venta' => 49.90,
                'stock' => 25,
                'stock_minimo' => 5,
                'fecha_vencimiento' => '2027-02-15',
                'marca_id' => 8,
                'presentacion_id' => 8,
                'categorias' => [9],
            ],
            [
                'codigo' => 'PROD-009',
                'nombre' => 'Lejía Tradicional 1L',
                'descripcion' => 'Desinfectante y blanqueador para ropa y superficies.',
                'precio_compra' => 2.50,
                'precio_venta' => 4.20,
                'stock' => 110,
                'stock_minimo' => 15,
                'fecha_vencimiento' => '2028-11-20',
                'marca_id' => 9,
                'presentacion_id' => 10,
                'categorias' => [4],
            ],
            [
                'codigo' => 'PROD-010',
                'nombre' => 'Cinta Adhesiva Transparente',
                'descripcion' => 'Cinta de embalaje de alta resistencia.',
                'precio_compra' => 1.80,
                'precio_venta' => 3.50,
                'stock' => 200,
                'stock_minimo' => 30,
                'fecha_vencimiento' => null,
                'marca_id' => 10,
                'presentacion_id' => 1,
                'categorias' => [10],
            ],
        ];

        foreach ($productos as $item) {
            $producto = Producto::updateOrCreate(
                ['codigo' => $item['codigo']],
                [
                    'nombre' => $item['nombre'],
                    'descripcion' => $item['descripcion'],
                    'precio_compra' => $item['precio_compra'],
                    'precio_venta' => $item['precio_venta'],
                    'stock' => $item['stock'],
                    'stock_minimo' => $item['stock_minimo'],
                    'fecha_vencimiento' => $item['fecha_vencimiento'],
                    'marca_id' => $item['marca_id'],
                    'presentacion_id' => $item['presentacion_id'],
                    'img_path' => null,
                    'estado' => 1,
                ]
            );

            $producto->categorias()->sync($item['categorias']);
        }
    }
}