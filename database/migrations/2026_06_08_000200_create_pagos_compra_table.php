<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_compra', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compra_id')
                ->constrained('compras')
                ->cascadeOnDelete();

            $table->foreignId('cuenta_por_pagar_id')
                ->nullable()
                ->constrained('cuentas_por_pagar')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'])
                ->index();

            $table->decimal('monto', 12, 2);
            $table->string('referencia_operacion', 100)->nullable();
            $table->string('moneda', 10)->default('PEN');
            $table->dateTime('fecha_pago')->useCurrent();
            $table->tinyInteger('estado')->default(1)->index();
            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->index(['compra_id', 'fecha_pago']);
            $table->index(['cuenta_por_pagar_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_compra');
    }
};