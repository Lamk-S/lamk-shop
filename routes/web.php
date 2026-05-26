<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\MovimientoTesoreriaController;
use App\Http\Controllers\PagoVentaController;
use App\Http\Controllers\PresentacionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SesionCajaController;
use App\Http\Controllers\TesoreriaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('panel');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/{profile}', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('categorias', CategoriaController::class)->except(['show']);

    Route::resource('presentaciones', PresentacionController::class)
        ->except(['show'])
        ->parameters(['presentaciones' => 'presentacion']);

    Route::resource('marcas', MarcaController::class)->except(['show']);
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::resource('clientes', ClienteController::class)->except(['show']);

    Route::resource('proveedores', ProveedorController::class)
        ->except(['show'])
        ->parameters(['proveedores' => 'proveedor']);

    Route::resource('compras', CompraController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('roles', RoleController::class)->except(['show']);

    Route::resource('cajas', CajaController::class)->except(['show']);

    Route::resource('sesiones-caja', SesionCajaController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->parameters(['sesiones-caja' => 'sesion_caja']);

    Route::resource('movimientos-caja', MovimientoCajaController::class)->only(['index', 'create', 'store']);

    Route::resource('tesoreria', TesoreriaController::class)->only(['index']);
    Route::resource('movimientos-tesoreria', MovimientoTesoreriaController::class)->only(['index']);

    Route::resource('kardex', KardexController::class)->only(['index', 'show']);

    Route::post('/ventas/{venta}/pagos', [PagoVentaController::class, 'store'])->name('ventas.pagos.store');
});

Route::get('/401', fn () => view('pages.401'));
Route::get('/404', fn () => view('pages.404'));
Route::get('/500', fn () => view('pages.500'));