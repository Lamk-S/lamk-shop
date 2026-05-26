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
            $table->string('nombre', 100)->index();
            $table->text('descripcion')->nullable();
            $table->string('img_path', 2048)->nullable();

            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);

            $table->integer('stock')->default(0)->index();
            $table->integer('stock_minimo')->default(5);
            $table->date('fecha_vencimiento')->nullable()->index();

            $table->tinyInteger('estado')->default(1)->index();

            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();
            $table->foreignId('presentacion_id')->nullable()->constrained('presentaciones')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};