<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('comprobante_id')->nullable()->constrained('comprobantes')->nullOnDelete();
            $table->string('numero_comprobante')->nullable()->index();
            $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA']);
            $table->dateTime('fecha_hora')->index();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2);
            $table->decimal('total', 10, 2);
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['proveedor_id', 'fecha_hora']);
        });

        Schema::create('compra_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad')->unsigned();
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->timestamps();

            $table->unique(['compra_id', 'producto_id']);
        });

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('sesion_caja_id')->constrained('sesiones_caja')->restrictOnDelete();
            $table->foreignId('comprobante_id')->nullable()->constrained('comprobantes')->nullOnDelete();
            $table->string('numero_comprobante')->unique();
            $table->dateTime('fecha_hora')->index();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('monto_recibido', 10, 2)->default(0);
            $table->decimal('vuelto_entregado', 10, 2)->default(0);
            $table->tinyInteger('estado')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sesion_caja_id', 'fecha_hora']);
            $table->index(['user_id', 'fecha_hora']);
        });

        Schema::create('producto_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad')->unsigned();
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['venta_id', 'producto_id']);
        });

        Schema::create('pagos_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->string('metodo_pago', 50);
            $table->decimal('monto', 10, 2);
            $table->string('referencia_operacion', 100)->nullable();
            $table->timestamps();

            $table->index(['venta_id', 'metodo_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_venta');
        Schema::dropIfExists('producto_venta');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('compra_producto');
        Schema::dropIfExists('compras');
    }
};