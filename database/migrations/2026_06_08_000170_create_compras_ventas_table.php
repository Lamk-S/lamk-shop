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

            $table->foreignId('proveedor_id')
                ->nullable()
                ->constrained('proveedores')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('comprobante_id')
                ->nullable()
                ->constrained('comprobantes')
                ->nullOnDelete();

            $table->string('tipo_comprobante', 20)->nullable();
            $table->string('serie', 20)->nullable();
            $table->string('correlativo', 20)->nullable();

            $table->string('proveedor_tipo_documento', 20)->nullable();
            $table->string('proveedor_numero_documento', 25)->nullable();
            $table->string('proveedor_nombre', 150)->nullable();
            $table->string('proveedor_direccion', 255)->nullable();
            $table->string('proveedor_telefono', 20)->nullable();
            $table->string('proveedor_email', 100)->nullable();

            $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'CREDITO', 'MIXTO'])
                ->default('EFECTIVO');

            $table->string('moneda', 10)->default('PEN');
            $table->dateTime('fecha_emision')->index();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_total', 12, 2)->default(0);
            $table->decimal('impuesto_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);

            $table->enum('estado_pago', ['PENDIENTE', 'PARCIAL', 'PAGADA', 'ANULADA'])
                ->default('PENDIENTE')
                ->index();

            $table->date('fecha_vencimiento')->nullable()->index();
            $table->dateTime('fecha_pago_total')->nullable();

            $table->enum('estado_documento', ['REGISTRADA', 'RECEPCIONADA', 'ANULADA', 'PENDIENTE'])
                ->default('RECEPCIONADA')
                ->index();

            $table->text('observacion')->nullable();
            $table->text('motivo_anulacion')->nullable();
            $table->timestamp('anulado_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['proveedor_id', 'fecha_emision']);
            $table->index(['estado_pago', 'fecha_vencimiento']);
            $table->unique(['tipo_comprobante', 'serie', 'correlativo']);
        });

        Schema::create('compra_producto', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compra_id')
                ->constrained('compras')
                ->cascadeOnDelete();

            $table->foreignId('producto_variante_id')
                ->constrained('producto_variantes')
                ->restrictOnDelete();

            $table->unsignedInteger('cantidad');
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->string('producto_codigo', 50);
            $table->string('producto_nombre', 120);
            $table->string('talla_codigo', 20);
            $table->string('talla_nombre', 50);

            $table->timestamps();

            $table->unique(['compra_id', 'producto_variante_id']);
            $table->index(['producto_variante_id']);
        });

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('sesion_caja_id')
                ->constrained('sesiones_caja')
                ->restrictOnDelete();

            $table->foreignId('comprobante_id')
                ->nullable()
                ->constrained('comprobantes')
                ->nullOnDelete();

            $table->string('tipo_comprobante', 20)->nullable();
            $table->string('serie', 20)->nullable();
            $table->string('correlativo', 20)->nullable();

            $table->string('cliente_tipo_documento', 20)->nullable();
            $table->string('cliente_numero_documento', 25)->nullable();
            $table->string('cliente_nombre', 150)->nullable();
            $table->string('cliente_direccion', 255)->nullable();
            $table->string('cliente_email', 100)->nullable();

            $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO', 'MIXTO'])
                ->default('EFECTIVO')
                ->index();

            $table->string('moneda', 10)->default('PEN');
            $table->dateTime('fecha_emision')->index();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_total', 12, 2)->default(0);
            $table->decimal('impuesto_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->decimal('monto_recibido', 12, 2)->default(0);
            $table->decimal('vuelto_entregado', 12, 2)->default(0);

            $table->enum('estado_documento', ['REGISTRADA', 'EMITIDA', 'ANULADA', 'PENDIENTE'])
                ->default('EMITIDA')
                ->index();

            $table->text('observacion')->nullable();
            $table->text('motivo_anulacion')->nullable();
            $table->timestamp('anulado_at')->nullable();

            $table->string('sunat_estado', 50)->nullable();
            $table->text('sunat_mensaje')->nullable();
            $table->string('xml_path', 2048)->nullable();
            $table->string('pdf_path', 2048)->nullable();
            $table->text('qr_payload')->nullable();
            $table->string('hash_resumen', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['sesion_caja_id', 'fecha_emision']);
            $table->index(['user_id', 'fecha_emision']);
            $table->unique(['tipo_comprobante', 'serie', 'correlativo']);
        });

        Schema::create('producto_venta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete();

            $table->foreignId('producto_variante_id')
                ->constrained('producto_variantes')
                ->restrictOnDelete();

            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('costo_unitario', 12, 2)->default(0);

            $table->string('producto_codigo', 50);
            $table->string('producto_nombre', 120);
            $table->string('talla_codigo', 20);
            $table->string('talla_nombre', 50);

            $table->timestamps();

            $table->unique(['venta_id', 'producto_variante_id']);
            $table->index(['producto_variante_id']);
        });

        Schema::create('pagos_venta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete();

            $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'])
                ->index();

            $table->decimal('monto', 12, 2);
            $table->string('referencia_operacion', 100)->nullable();
            $table->string('moneda', 10)->default('PEN');
            $table->tinyInteger('estado')->default(1)->index();

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