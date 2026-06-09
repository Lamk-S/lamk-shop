<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('codigo_barra', 80)->nullable()->unique();
            $table->string('nombre', 120)->index();
            $table->text('descripcion')->nullable();
            $table->string('img_path', 2048)->nullable();

            $table->enum('tipo_producto', ['ZAPATILLA', 'ROPA', 'ACCESORIO'])->index();
            $table->boolean('maneja_tallas')->default(false)->index();

            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);

            $table->integer('stock_total')->default(0)->index();
            $table->integer('stock_minimo')->default(0)->index();

            $table->boolean('afecto_igv')->default(true);
            $table->tinyInteger('estado')->default(1)->index();

            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categoria_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['producto_id', 'categoria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_producto');
        Schema::dropIfExists('productos');
    }
};