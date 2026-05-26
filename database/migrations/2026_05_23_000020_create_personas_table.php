<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 150);
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable()->index();
            $table->enum('tipo_persona', ['natural', 'juridica']);
            $table->tinyInteger('estado')->default(1)->index();
            $table->foreignId('documento_id')->constrained('documentos')->restrictOnDelete();
            $table->string('numero_documento', 25)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};