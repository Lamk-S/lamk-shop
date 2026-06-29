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
    ClienteController, ClienteQuickController, ProveedorController, ProveedorQuickController,
    EmpresaConfiguracionController, AuditoriaOperacionController
};

/*
|--------------------------------------------------------------------------
| Ruta Púclica del Sistema (POS)
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

    // MÓDULO: Catálogo Deportivo (Ropa, Calzado, Accesorios)
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('marcas', MarcaController::class)->except(['show']);
    Route::resource('tallas', TallaController::class)->except(['show']);
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::resource('producto-variantes', ProductoVarianteController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::resource('kardex', KardexController::class)->only(['index', 'show']);

    // MÓDULO: Ventas y Punto de Venta
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('/ventas/{venta}/pagos', [PagoVentaController::class, 'store'])->name('ventas.pagos.store');

    // MÓDULO: Abastecimiento y Compras
    Route::resource('compras', CompraController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
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

/*
|--------------------------------------------------------------------------
| Páginas de Error Personalizadas
|--------------------------------------------------------------------------
*/
Route::view('/401', 'pages.401')->name('error.401');
Route::view('/404', 'pages.404')->name('error.404');
Route::view('/500', 'pages.500')->name('error.500');