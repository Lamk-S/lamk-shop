<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 150);
            $table->string('nombre_comercial', 150)->nullable();
            $table->string('ruc', 11)->unique();
            $table->string('direccion_fiscal', 255);
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('logo_path', 2048)->nullable();
            $table->string('moneda', 10)->default('PEN');
            $table->decimal('igv_porcentaje', 5, 2)->default(18.00);
            $table->enum('modo_emision', ['SIMULADA', 'REAL'])->default('SIMULADA');
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_configuracion');
    }
};