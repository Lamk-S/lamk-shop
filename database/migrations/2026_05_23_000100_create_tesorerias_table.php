<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tesorerias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->decimal('saldo_efectivo', 12, 2)->default(0);
            $table->decimal('saldo_banco', 12, 2)->default(0);
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('movimientos_tesoreria', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tesoreria_id')
                ->constrained('tesorerias')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('sesion_caja_id')
                ->nullable()
                ->constrained('sesiones_caja')
                ->nullOnDelete();

            $table->foreignId('venta_id')
                ->nullable()
                ->constrained('ventas')
                ->nullOnDelete();

            $table->foreignId('compra_id')
                ->nullable()
                ->constrained('compras')
                ->nullOnDelete();

            $table->enum('tipo', ['INGRESO', 'EGRESO']);
            $table->enum('medio', ['EFECTIVO', 'BANCO']);
            $table->enum('origen', [
                'CIERRE_CAJA',
                'VENTA_EFECTIVO',
                'VENTA_TARJETA',
                'VENTA_TRANSFERENCIA',
                'COMPRA_PRODUCTO',
                'AJUSTE'
            ]);

            $table->string('descripcion', 255);
            $table->decimal('monto', 12, 2);
            $table->decimal('saldo_anterior', 12, 2);
            $table->decimal('saldo_posterior', 12, 2);

            $table->timestamps();

            $table->index(['tesoreria_id', 'created_at']);
            $table->index(['tipo', 'medio']);
            $table->index(['origen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_tesoreria');
        Schema::dropIfExists('tesorerias');
    }
};