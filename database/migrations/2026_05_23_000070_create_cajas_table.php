<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->decimal('fondo_fijo', 12, 2)->default(100.00);
            $table->string('nombre', 50)->unique();
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sesiones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_hora_apertura');
            $table->dateTime('fecha_hora_cierre')->nullable();
            $table->decimal('saldo_inicial', 10, 2);
            $table->decimal('saldo_final_declarado', 10, 2)->nullable();
            $table->decimal('saldo_final_esperado', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->nullable();
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();

            $table->index(['caja_id', 'estado']);
            $table->index(['user_id', 'estado']);
        });

        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_caja_id')->constrained('sesiones_caja')->cascadeOnDelete();
            $table->enum('tipo', ['INGRESO', 'EGRESO']);
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->timestamps();

            $table->index(['sesion_caja_id', 'tipo']);
            $table->index(['sesion_caja_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
        Schema::dropIfExists('sesiones_caja');
        Schema::dropIfExists('cajas');
    }
};
