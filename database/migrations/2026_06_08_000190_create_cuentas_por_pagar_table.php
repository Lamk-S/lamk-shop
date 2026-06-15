<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_por_pagar', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compra_id')
                ->constrained('compras')
                ->cascadeOnDelete()
                ->unique();

            $table->foreignId('proveedor_id')
                ->constrained('proveedores')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('total', 12, 2);
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);

            $table->date('fecha_emision')->index();
            $table->date('fecha_vencimiento')->nullable()->index();
            $table->dateTime('fecha_cancelacion')->nullable();

            $table->enum('estado', ['PENDIENTE', 'PARCIAL', 'PAGADA', 'ANULADA'])
                ->default('PENDIENTE')
                ->index();

            $table->text('observacion')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['proveedor_id', 'estado']);
            $table->index(['fecha_vencimiento', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_pagar');
    }
};