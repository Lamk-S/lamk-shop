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
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 50);
            $table->decimal('fondo_fijo', 12, 2)->default(100.00);
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sesiones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('user_cierre_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_hora_apertura');
            $table->dateTime('fecha_hora_cierre')->nullable();
            $table->decimal('saldo_inicial', 12, 2);
            $table->decimal('saldo_final_declarado', 12, 2)->nullable();
            $table->decimal('saldo_final_esperado', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->enum('estado_sesion', ['ABIERTA', 'CERRADA', 'ANULADA'])->default('ABIERTA')->index();
            $table->text('observacion_apertura')->nullable();
            $table->text('observacion_cierre')->nullable();
            $table->timestamps();

            $table->index(['caja_id', 'estado_sesion']);
            $table->index(['user_id', 'estado_sesion']);
        });

        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_caja_id')->constrained('sesiones_caja')->cascadeOnDelete();
            $table->enum('tipo', ['INGRESO', 'EGRESO'])->index();
            $table->enum('origen', ['APERTURA', 'VENTA', 'CIERRE', 'AJUSTE', 'INGRESO_MANUAL', 'EGRESO_MANUAL', 'ANULACION'])->index();
            $table->string('descripcion', 255);
            $table->decimal('monto', 12, 2);
            $table->nullableMorphs('referencia');
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