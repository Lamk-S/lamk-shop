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
            $table->string('serie', 20);
            $table->enum('uso_comprobante', ['COMPRA', 'VENTA'])->default('VENTA')->index();
            $table->integer('correlativo_actual')->default(0);
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('serie');
            $table->index(['uso_comprobante', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};