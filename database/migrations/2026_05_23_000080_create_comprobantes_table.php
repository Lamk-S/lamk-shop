<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_comprobante', 50);
            $table->string('serie', 10);
            $table->integer('correlativo_actual')->default(0);
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tipo_comprobante', 'serie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};