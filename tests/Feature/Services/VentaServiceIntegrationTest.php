<?php

namespace Tests\Feature\Services;

use App\Enums\EstadoSesion;
use App\Enums\MetodoPago;
use App\Models\Caja;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\SesionCaja;
use App\Models\Talla;
use App\Models\User;
use App\Services\VentaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaServiceIntegrationTest extends TestCase
{
    use RefreshDatabase; // Resetea la BD después de la prueba

    protected VentaService $ventaService;
    protected User $user;
    protected SesionCaja $sesionCaja;
    protected ProductoVariante $variante;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ventaService = app(VentaService::class);
        $this->user = User::factory()->create();

        // Preparamos el entorno: Caja abierta y Producto con stock
        $caja = Caja::factory()->create(['estado' => 1]);
        $this->sesionCaja = SesionCaja::factory()->create([
            'caja_id' => $caja->id,
            'user_id' => $this->user->id,
            'estado_sesion' => EstadoSesion::ABIERTA,
            'saldo_inicial' => 100.00
        ]);

        $producto = Producto::factory()->create(['afecto_igv' => true]);
        $talla = Talla::factory()->create();
        
        $this->variante = ProductoVariante::factory()->create([
            'producto_id' => $producto->id,
            'talla_id' => $talla->id,
            'stock_actual' => 20, // Stock inicial
        ]);
    }

    public function test_registro_de_venta_exitosa_afecta_kardex_y_caja(): void
    {
        // 1. Preparar los datos del request de venta
        $datosVenta = [
            'cliente_id' => null, // Consumidor final
            'comprobante_id' => null, // Usará ticket por defecto
            'moneda' => 'PEN',
            'metodo_pago' => MetodoPago::EFECTIVO->value,
            'monto_recibido' => 50.00,
            'detalles' => [
                [
                    'producto_variante_id' => $this->variante->id,
                    'cantidad' => 2,
                    'precio_unitario' => 25.00,
                    'descuento' => 0,
                ]
            ]
        ];

        // 2. Actuar: Ejecutamos el servicio de venta
        $venta = $this->ventaService->registrar($datosVenta, $this->user);

        // 3. Afirmar (Asserts): Validamos la integridad de los datos
        
        // A. Validar que la venta se creó correctamente
        $this->assertDatabaseHas('ventas', [
            'id' => $venta->id,
            'total' => 50.00,
            'estado_documento' => 'EMITIDA'
        ]);

        // B. Validar que el inventario bajó de 20 a 18 (Kardex)
        $this->assertDatabaseHas('producto_variantes', [
            'id' => $this->variante->id,
            'stock_actual' => 18
        ]);

        // C. Validar que el dinero ingresó a los movimientos de la caja
        $this->assertDatabaseHas('movimientos_caja', [
            'sesion_caja_id' => $this->sesionCaja->id,
            'monto' => 50.00,
            'tipo' => 'INGRESO',
            'origen' => 'VENTA'
        ]);

        // D. Validar que la auditoría registró la acción del usuario
        $this->assertDatabaseHas('auditoria_operaciones', [
            'user_id' => $this->user->id,
            'entidad' => 'Venta',
            'accion' => 'CREAR'
        ]);
    }
}