<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tallas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique(); // 35, 36, 37, S, M, L, XL, UNICA
            $table->string('nombre', 50);           // Talla 35, Small, Única, etc.
            $table->enum('tipo_talla', ['CALZADO', 'ROPA', 'UNICA'])->index();
            $table->integer('orden')->default(0)->index();
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tallas');
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('categorias');
    }
};