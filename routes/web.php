<?php

use Illuminate\Support\Facades\Route;
// Controladores Core
use App\Http\Controllers\{
    HomeController, LoginController, LogoutController, ProfileController, UserController, RoleController
};
// Controladores de Catálogo e Inventario
use App\Http\Controllers\{
    CategoriaController, MarcaController, TallaController, ProductoController, 
    ProductoVarianteController, KardexController
};
// Controladores de Operaciones (Compras/Ventas)
use App\Http\Controllers\{
    CompraController, VentaController, PagoCompraController, PagoVentaController, ComprobanteController
};
// Controladores de Tesorería y Caja
use App\Http\Controllers\{
    CajaController, SesionCajaController, MovimientoCajaController, TesoreriaController
};
// Controladores de Contactos y Configuración
use App\Http\Controllers\{
    ApiPeruController, ClienteController, ClienteQuickController, ProveedorController, ProveedorQuickController,
    EmpresaConfiguracionController, AuditoriaOperacionController
};

/*
|--------------------------------------------------------------------------
| Ruta Pública del Sistema (POS)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('panel');

/*
|--------------------------------------------------------------------------
| Rutas  de Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas del Sistema (POS)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Panel y Perfil
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Búsqueda de documentos (DNI/RUC) vía API
    Route::get('/api-peru/consultar', [ApiPeruController::class, 'consultar'])->name('api-peru.consultar');

    // MÓDULO: Catálogo Deportivo (Ropa, Calzado, Accesorios)
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('marcas', MarcaController::class)->except(['show']);
    Route::resource('tallas', TallaController::class)->except(['show']);

    // --- DIVISIÓN DE PRODUCTOS Y VARIANTES (Lectura vs Escritura) ---
    // 1. Mutaciones (Solo Almacén y Admin)
    Route::middleware(['permission:gestionar_productos'])->group(function () {
        Route::get('productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::patch('productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::patch('/productos/{producto}/restore', [ProductoController::class, 'restore'])->name('productos.restore');
        Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

        Route::get('producto-variantes/create', [ProductoVarianteController::class, 'create'])->name('producto-variantes.create');
        Route::post('producto-variantes', [ProductoVarianteController::class, 'store'])->name('producto-variantes.store');
        Route::get('producto-variantes/{producto_variante}/edit', [ProductoVarianteController::class, 'edit'])->name('producto-variantes.edit');
        Route::patch('producto-variantes/{producto_variante}', [ProductoVarianteController::class, 'update'])->name('producto-variantes.update');
        Route::delete('producto-variantes/{producto_variante}', [ProductoVarianteController::class, 'destroy'])->name('producto-variantes.destroy');
    });

    // 2. Solo Lectura (Vendedores, Cajeros, Almacén, Admin)
    Route::middleware(['permission:gestionar_productos|ver_productos'])->group(function () {
        Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
        
        Route::get('producto-variantes', [ProductoVarianteController::class, 'index'])->name('producto-variantes.index');
        Route::get('producto-variantes/{producto_variante}', [ProductoVarianteController::class, 'show'])->name('producto-variantes.show');
    });

    Route::resource('kardex', KardexController::class)->only(['index', 'show']);

    // MÓDULO: Ventas y Punto de Venta
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show']);
    // Protección estricta para la anulación de ventas
    Route::delete('ventas/{venta}', [VentaController::class, 'destroy'])
        ->name('ventas.destroy')
        ->middleware('permission:anular_ventas');
    // Rutas de Pagos de Ventas
    Route::get('/pagos-venta', [PagoVentaController::class, 'index'])->name('pagos-venta.index');
    Route::post('/ventas/{venta}/pagos', [PagoVentaController::class, 'store'])->name('ventas.pagos.store');

    // MÓDULO: Abastecimiento y Compras
    Route::resource('compras', CompraController::class)->only(['index', 'create', 'store', 'show']);
    // Protección estricta para la anulación de compras
    Route::delete('compras/{compra}', [CompraController::class, 'destroy'])
        ->name('compras.destroy')
        ->middleware('permission:anular_compras');
    // Rutas de Pagos de Compras
    Route::get('/cuentas-por-pagar', [PagoCompraController::class, 'index'])->name('cuentas-por-pagar.index');
    Route::post('/cuentas-por-pagar/{cuenta_por_pagar}/pagos', [PagoCompraController::class, 'store'])->name('cuentas-por-pagar.pagos.store');

    // MÓDULO: Finanzas (Cajas y Tesorería)
    Route::resource('cajas', CajaController::class)->except(['show']);
    Route::resource('sesiones-caja', SesionCajaController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->parameters(['sesiones-caja' => 'sesion_caja']);
    Route::resource('movimientos-caja', MovimientoCajaController::class)->only(['index', 'create', 'store']);
    Route::resource('tesorerias', TesoreriaController::class)->only(['index']);

    // MÓDULO: Directorio (Clientes y Proveedores)
    Route::resource('clientes', ClienteController::class)->except(['show']);
    Route::post('/clientes/quick-store', [ClienteQuickController::class, 'store'])->name('clientes.quick-store');
    
    Route::resource('proveedores', ProveedorController::class)
        ->except(['show'])->parameters(['proveedores' => 'proveedor']);
    Route::post('/proveedores/quick-store', [ProveedorQuickController::class, 'store'])->name('proveedores.quick-store');

    // MÓDULO: Configuración y Seguridad
    Route::resource('comprobantes', ComprobanteController::class)->except('create', 'store');
    Route::resource('empresa-configuracion', EmpresaConfiguracionController::class)
        ->only(['index', 'show', 'edit', 'update'])
        ->parameters(['empresa-configuracion' => 'empresa_configuracion']);
    Route::resource('auditoria-operaciones', AuditoriaOperacionController::class)
        ->only(['index', 'show'])->parameters(['auditoria-operaciones' => 'auditoriaOperacion']);
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('roles', RoleController::class)->except(['show']);
});