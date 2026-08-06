<?php

namespace Tests\Unit\Services;

use App\Models\ProductoVariante;
use App\Services\InventarioService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class InventarioServiceUnitTest extends TestCase
{
    protected InventarioService $inventarioService;

    protected function setUp(): void
    {
        parent::setUp();
        // Instanciamos el servicio aislando dependencias externas
        $this->inventarioService = new InventarioService();
    }

    public function test_rechaza_salida_si_stock_es_insuficiente(): void
    {
        // 1. Preparar (Arrange): Simulamos una variante con stock de 5
        $variante = new ProductoVariante();
        $variante->stock_actual = 5;
        $variante->codigo_variante = 'VAR-001';

        // 2. Afirmar Excepción (Assert): Esperamos que el sistema lance un error
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stock insuficiente para la variante VAR-001.');

        // 3. Actuar (Act): Intentamos sacar 10 unidades
        $this->inventarioService->validarStockDisponible($variante, 10);
    }

    public function test_permite_salida_si_stock_es_suficiente(): void
    {
        $variante = new ProductoVariante();
        $variante->stock_actual = 15;

        // Si no lanza excepción, la prueba pasa automáticamente
        $this->inventarioService->validarStockDisponible($variante, 5);
        $this->assertTrue(true);
    }
}