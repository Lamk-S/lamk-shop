<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_variantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->foreignId('talla_id')
                ->constrained('tallas')
                ->restrictOnDelete();

            $table->string('codigo_variante', 80)->unique();
            $table->string('codigo_barra', 80)->nullable()->unique();

            $table->integer('stock_actual')->default(0)->index();
            $table->integer('stock_minimo')->default(0);

            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['producto_id', 'talla_id']);
            $table->index(['producto_id', 'estado']);
            $table->index(['talla_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_variantes');
    }
};