<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categoryIds = DB::table('categorias')->pluck('id', 'nombre')->toArray();
        $brandIds = DB::table('marcas')->pluck('id', 'nombre')->toArray();

        $items = $this->items();

        DB::transaction(function () use ($items, $categoryIds, $brandIds, $now) {
            foreach ($items as $item) {
                DB::table('productos')->updateOrInsert(
                    ['codigo' => $item['codigo']],
                    [
                        'codigo_barra' => null,
                        'nombre' => $item['nombre'],
                        'descripcion' => 'Producto inicial para Lamk Sports.',
                        'img_path' => null,
                        'tipo_producto' => $item['tipo_producto'],
                        'maneja_tallas' => $item['maneja_tallas'],
                        'precio_compra' => $item['precio_compra'],
                        'precio_venta' => $item['precio_venta'],
                        'stock_total' => 0,
                        'stock_minimo' => $item['stock_minimo'],
                        'afecto_igv' => true,
                        'marca_id' => $brandIds[$item['marca']] ?? null,
                        'estado' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $productoId = DB::table('productos')->where('codigo', $item['codigo'])->value('id');
                $categoriaId = $categoryIds[$item['categoria']] ?? null;

                if ($productoId && $categoriaId) {
                    DB::table('categoria_producto')->updateOrInsert(
                        [
                            'producto_id' => $productoId,
                            'categoria_id' => $categoriaId,
                        ],
                        [
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );
                }
            }
        });
    }

    private function items(): array
    {
        return [
            // ZAPATILLAS
            ['codigo' => 'LAM-ZAP-001', 'nombre' => 'Nike Zapatilla Running Air Max Alpha', 'marca' => 'Nike', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 240.00, 'precio_venta' => 339.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-002', 'nombre' => 'Nike Zapatilla Training Revolution', 'marca' => 'Nike', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 210.00, 'precio_venta' => 299.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-003', 'nombre' => 'Nike Zapatilla Casual Court Vision', 'marca' => 'Nike', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 225.00, 'precio_venta' => 319.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-004', 'nombre' => 'Adidas Zapatilla Running Duramo', 'marca' => 'Adidas', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 205.00, 'precio_venta' => 289.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-005', 'nombre' => 'Adidas Zapatilla Training Alphabounce', 'marca' => 'Adidas', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 235.00, 'precio_venta' => 329.00, 'stock_minimo' => 2],
            ['codigo' => 'LAM-ZAP-006', 'nombre' => 'Adidas Zapatilla Casual Grand Court', 'marca' => 'Adidas', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 220.00, 'precio_venta' => 309.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-007', 'nombre' => 'Puma Zapatilla Running Flyer Runner', 'marca' => 'Puma', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 190.00, 'precio_venta' => 269.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-008', 'nombre' => 'Puma Zapatilla Casual Smash 3', 'marca' => 'Puma', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 175.00, 'precio_venta' => 249.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-009', 'nombre' => 'Under Armour Zapatilla Training Charged Assert', 'marca' => 'Under Armour', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 230.00, 'precio_venta' => 319.00, 'stock_minimo' => 2],
            ['codigo' => 'LAM-ZAP-010', 'nombre' => 'Reebok Zapatilla Running Glide', 'marca' => 'Reebok', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 185.00, 'precio_venta' => 259.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-011', 'nombre' => 'New Balance Zapatilla Running 500', 'marca' => 'New Balance', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 210.00, 'precio_venta' => 299.00, 'stock_minimo' => 2],
            ['codigo' => 'LAM-ZAP-012', 'nombre' => 'Asics Zapatilla Running Gel Contend', 'marca' => 'Asics', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 245.00, 'precio_venta' => 339.00, 'stock_minimo' => 2],
            ['codigo' => 'LAM-ZAP-013', 'nombre' => 'Skechers Zapatilla Walking Go Walk', 'marca' => 'Skechers', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 200.00, 'precio_venta' => 279.00, 'stock_minimo' => 2],
            ['codigo' => 'LAM-ZAP-014', 'nombre' => 'Fila Zapatilla Casual Memory Workshift', 'marca' => 'Fila', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 170.00, 'precio_venta' => 239.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-015', 'nombre' => 'Nike Zapatilla Basketball Precision', 'marca' => 'Nike', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 255.00, 'precio_venta' => 359.00, 'stock_minimo' => 2],
            ['codigo' => 'LAM-ZAP-016', 'nombre' => 'Adidas Zapatilla Trail Run', 'marca' => 'Adidas', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 230.00, 'precio_venta' => 319.00, 'stock_minimo' => 2],
            ['codigo' => 'LAM-ZAP-017', 'nombre' => 'Puma Zapatilla Training Softride', 'marca' => 'Puma', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 205.00, 'precio_venta' => 289.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ZAP-018', 'nombre' => 'New Balance Zapatilla Lifestyle 373', 'marca' => 'New Balance', 'categoria' => 'Zapatillas', 'tipo_producto' => 'ZAPATILLA', 'maneja_tallas' => true, 'precio_compra' => 215.00, 'precio_venta' => 299.00, 'stock_minimo' => 2],

            // ROPA DEPORTIVA
            ['codigo' => 'LAM-ROP-019', 'nombre' => 'Nike Polo Deportivo Dri-FIT', 'marca' => 'Nike', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 55.00, 'precio_venta' => 89.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ROP-020', 'nombre' => 'Nike Polo Training', 'marca' => 'Nike', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 48.00, 'precio_venta' => 79.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ROP-021', 'nombre' => 'Adidas Polo Essentials', 'marca' => 'Adidas', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 50.00, 'precio_venta' => 85.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ROP-022', 'nombre' => 'Adidas Camiseta Training', 'marca' => 'Adidas', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 42.00, 'precio_venta' => 69.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ROP-023', 'nombre' => 'Puma Camiseta Logo Essentials', 'marca' => 'Puma', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 41.00, 'precio_venta' => 67.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ROP-024', 'nombre' => 'Puma Short Deportivo', 'marca' => 'Puma', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 38.00, 'precio_venta' => 64.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ROP-025', 'nombre' => 'Under Armour Camiseta Tech', 'marca' => 'Under Armour', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 58.00, 'precio_venta' => 95.00, 'stock_minimo' => 4],
            ['codigo' => 'LAM-ROP-026', 'nombre' => 'Under Armour Short Training', 'marca' => 'Under Armour', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 45.00, 'precio_venta' => 74.00, 'stock_minimo' => 4],
            ['codigo' => 'LAM-ROP-027', 'nombre' => 'Reebok Polo Classic', 'marca' => 'Reebok', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 39.00, 'precio_venta' => 66.00, 'stock_minimo' => 4],
            ['codigo' => 'LAM-ROP-028', 'nombre' => 'Fila Camiseta Sport', 'marca' => 'Fila', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 36.00, 'precio_venta' => 59.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ROP-029', 'nombre' => 'Champion Buzo Deportivo', 'marca' => 'Champion', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 72.00, 'precio_venta' => 119.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ROP-030', 'nombre' => 'Nike Buzo Deportivo Club', 'marca' => 'Nike', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 78.00, 'precio_venta' => 129.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ROP-031', 'nombre' => 'Adidas Pantalón Deportivo', 'marca' => 'Adidas', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 60.00, 'precio_venta' => 99.00, 'stock_minimo' => 4],
            ['codigo' => 'LAM-ROP-032', 'nombre' => 'Puma Pantalón Jogger', 'marca' => 'Puma', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 58.00, 'precio_venta' => 96.00, 'stock_minimo' => 4],
            ['codigo' => 'LAM-ROP-033', 'nombre' => 'Under Armour Licra Deportiva', 'marca' => 'Under Armour', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 52.00, 'precio_venta' => 88.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ROP-034', 'nombre' => 'Reebok Casaca Rompeviento', 'marca' => 'Reebok', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 68.00, 'precio_venta' => 112.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ROP-035', 'nombre' => 'Fila Top Deportivo', 'marca' => 'Fila', 'categoria' => 'Ropa Deportiva', 'tipo_producto' => 'ROPA', 'maneja_tallas' => true, 'precio_compra' => 34.00, 'precio_venta' => 56.00, 'stock_minimo' => 5],

            // ACCESORIOS
            ['codigo' => 'LAM-ACC-036', 'nombre' => 'Nike Medias Deportivas Pack x3', 'marca' => 'Nike', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 18.00, 'precio_venta' => 29.00, 'stock_minimo' => 8],
            ['codigo' => 'LAM-ACC-037', 'nombre' => 'Adidas Medias Deportivas Pack x3', 'marca' => 'Adidas', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 18.00, 'precio_venta' => 29.00, 'stock_minimo' => 8],
            ['codigo' => 'LAM-ACC-038', 'nombre' => 'Puma Gorra Deportiva', 'marca' => 'Puma', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 22.00, 'precio_venta' => 35.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ACC-039', 'nombre' => 'Under Armour Mochila Sport', 'marca' => 'Under Armour', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 65.00, 'precio_venta' => 109.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ACC-040', 'nombre' => 'Reebok Botella Deportiva', 'marca' => 'Reebok', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 12.00, 'precio_venta' => 20.00, 'stock_minimo' => 10],
            ['codigo' => 'LAM-ACC-041', 'nombre' => 'New Balance Maleta Gym', 'marca' => 'New Balance', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 58.00, 'precio_venta' => 94.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ACC-042', 'nombre' => 'Asics Banda Elástica Set', 'marca' => 'Asics', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 24.00, 'precio_venta' => 39.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ACC-043', 'nombre' => 'Skechers Cinturón Running', 'marca' => 'Skechers', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 20.00, 'precio_venta' => 33.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ACC-044', 'nombre' => 'Fila Muñequera Deportiva', 'marca' => 'Fila', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 14.00, 'precio_venta' => 24.00, 'stock_minimo' => 6],
            ['codigo' => 'LAM-ACC-045', 'nombre' => 'Champion Toalla Deportiva', 'marca' => 'Champion', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 28.00, 'precio_venta' => 45.00, 'stock_minimo' => 5],
            ['codigo' => 'LAM-ACC-046', 'nombre' => 'Nike Lentes Deportivos', 'marca' => 'Nike', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 42.00, 'precio_venta' => 69.00, 'stock_minimo' => 3],
            ['codigo' => 'LAM-ACC-047', 'nombre' => 'Adidas Guantes Training', 'marca' => 'Adidas', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 26.00, 'precio_venta' => 42.00, 'stock_minimo' => 4],
            ['codigo' => 'LAM-ACC-048', 'nombre' => 'Puma Vincha Deportiva', 'marca' => 'Puma', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 10.00, 'precio_venta' => 18.00, 'stock_minimo' => 10],
            ['codigo' => 'LAM-ACC-049', 'nombre' => 'Under Armour Plantillas Deportivas', 'marca' => 'Under Armour', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 19.00, 'precio_venta' => 31.00, 'stock_minimo' => 6],
            ['codigo' => 'LAM-ACC-050', 'nombre' => 'Reebok Cuerda para Saltar', 'marca' => 'Reebok', 'categoria' => 'Accesorios', 'tipo_producto' => 'ACCESORIO', 'maneja_tallas' => false, 'precio_compra' => 16.00, 'precio_venta' => 27.00, 'stock_minimo' => 8],
        ];
    }
}