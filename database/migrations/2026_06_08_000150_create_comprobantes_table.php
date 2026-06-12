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
            $table->enum('tipo_comprobante', ['TICKET', 'BOLETA', 'FACTURA', 'NOTA_CREDITO', 'NOTA_DEBITO'])->index();
            $table->string('serie', 20);
            $table->enum('uso_comprobante', ['COMPRA', 'VENTA'])->default('VENTA')->index();
            $table->unsignedInteger('correlativo_actual')->default(0);
            $table->boolean('es_electronico')->default(false);
            $table->enum('ambiente', ['SIMULADO', 'PRODUCCION'])->default('SIMULADO');
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tipo_comprobante', 'serie', 'uso_comprobante']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};