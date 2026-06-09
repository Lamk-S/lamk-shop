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
            $table->string('tipo_comprobante', 50); // BOLETA, FACTURA, NOTA_CREDITO, NOTA_DEBITO, TICKET
            $table->string('serie', 20);
            $table->enum('uso_comprobante', ['COMPRA', 'VENTA'])->default('VENTA')->index();
            $table->integer('correlativo_actual')->default(0);

            $table->boolean('es_electronico')->default(false);
            $table->enum('ambiente', ['SIMULADO', 'PRODUCCION'])->default('SIMULADO');
            $table->tinyInteger('estado')->default(1)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tipo_comprobante', 'serie']);
            $table->index(['uso_comprobante', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};