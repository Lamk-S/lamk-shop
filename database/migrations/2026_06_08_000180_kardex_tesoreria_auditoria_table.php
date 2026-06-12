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
            $table->foreignId('producto_variante_id')->constrained('producto_variantes')->restrictOnDelete();
            $table->enum('tipo_transaccion', [
                'COMPRA',
                'VENTA',
                'AJUSTE',
                'APERTURA',
                'ANULACION',
                'DEVOLUCION',
                'MERMA',
                'VENCIDO',
                'TRANSFERENCIA'
            ])->index();
            $table->nullableMorphs('origen');
            $table->string('descripcion', 255);
            $table->unsignedInteger('entrada')->default(0);
            $table->unsignedInteger('salida')->default(0);
            $table->integer('saldo_anterior')->default(0);
            $table->integer('saldo_posterior')->default(0);
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('costo_total', 12, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['producto_variante_id', 'created_at']);
            $table->index(['tipo_transaccion', 'created_at']);
        });

        Schema::create('tesorerias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->enum('tipo_cuenta', ['EFECTIVO', 'BANCO'])->index();
            $table->decimal('saldo_actual', 12, 2)->default(0);
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('movimientos_tesoreria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesoreria_id')->constrained('tesorerias')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sesion_caja_id')->nullable()->constrained('sesiones_caja')->nullOnDelete();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->foreignId('compra_id')->nullable()->constrained('compras')->nullOnDelete();
            $table->enum('tipo', ['INGRESO', 'EGRESO'])->index();
            $table->enum('medio', ['EFECTIVO', 'BANCO'])->index();
            $table->enum('origen', [
                'CIERRE_CAJA',
                'VENTA_EFECTIVO',
                'VENTA_TARJETA',
                'VENTA_TRANSFERENCIA',
                'COMPRA_PRODUCTO',
                'DEPOSITO',
                'RETIRO',
                'AJUSTE',
                'ANULACION'
            ])->index();
            $table->string('descripcion', 255);
            $table->decimal('monto', 12, 2);
            $table->decimal('saldo_anterior', 12, 2);
            $table->decimal('saldo_posterior', 12, 2);
            $table->string('numero_operacion', 100)->nullable();
            $table->string('referencia', 255)->nullable();
            $table->timestamps();

            $table->index(['tesoreria_id', 'created_at']);
            $table->index(['tipo', 'medio']);
        });

        Schema::create('auditoria_operaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('entidad', 100);
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->string('accion', 50);
            $table->json('antes')->nullable();
            $table->json('despues')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('metodo_http', 10)->nullable();
            $table->string('ruta', 255)->nullable();
            $table->timestamps();

            $table->index(['entidad', 'entidad_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_operaciones');
        Schema::dropIfExists('movimientos_tesoreria');
        Schema::dropIfExists('tesorerias');
        Schema::dropIfExists('kardex');
    }
};