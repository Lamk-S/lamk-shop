<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->enum('tipo_transaccion', ['COMPRA', 'VENTA', 'AJUSTE', 'APERTURA', 'ANULACION']);
            $table->string('descripcion', 255);
            $table->integer('entrada')->default(0);
            $table->integer('salida')->default(0);
            $table->integer('saldo');
            $table->decimal('costo_unitario', 10, 2);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['producto_id', 'created_at']);
            $table->index(['tipo_transaccion', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};