<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\PedidosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('home');

    // return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // PRODUCTO
    Route::prefix('/producto')->group(function () {
        Route::get('/listado', [App\Http\Controllers\ProductoController::class, 'listado'])->name('producto.listado');
        Route::post('/ajaxListado', [App\Http\Controllers\ProductoController::class, 'ajaxListado'])->name('producto.ajaxListado');
        Route::post('/guardarProducto', [App\Http\Controllers\ProductoController::class, 'guardarProducto'])->name('producto.guardarProducto');
        Route::post('/eliminarProducto', [App\Http\Controllers\ProductoController::class, 'eliminarProducto'])->name('producto.eliminarProducto');
        Route::post('/ajaxStockSucursal', [App\Http\Controllers\ProductoController::class, 'ajaxStockSucursal'])->name('producto.ajaxStockSucursal');

    });
    // PROVEEDOR
    Route::prefix('/proveedor')->group(function () {
        Route::get('/listado', [App\Http\Controllers\ProveedorController::class, 'listado'])->name('proveedor.listado');
        Route::post('/ajaxListado', [App\Http\Controllers\ProveedorController::class, 'ajaxListado'])->name('proveedor.ajaxListado');
        Route::post('/guardarProveedor', [App\Http\Controllers\ProveedorController::class, 'guardarProveedor'])->name('proveedor.guardarProveedor');
        Route::post('/eliminarProveedor', [App\Http\Controllers\ProveedorController::class, 'eliminarProveedor'])->name('proveedor.eliminarProveedor');
    });
    // USUARIO
    Route::prefix('/usuario')->group(function () {
        Route::get('/listado', [App\Http\Controllers\UserController::class, 'listado'])->name('usuario.listado');
        Route::post('/ajaxListado', [App\Http\Controllers\UserController::class, 'ajaxListado'])->name('usuario.ajaxListado');
        Route::post('/guardarUsuario', [App\Http\Controllers\UserController::class, 'guardarUsuario'])->name('usuario.guardarUsuario');
        Route::post('/eliminarUsuario', [App\Http\Controllers\UserController::class, 'eliminarUsuario'])->name('usuario.eliminarUsuario');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

    // ROL
    Route::prefix('/rol')->group(function () {
        Route::get('/listado', [RolController::class, 'listado'])->name('rol.listado');
        Route::post('/ajaxListado', [RolController::class, 'ajaxListado'])->name('rol.ajaxListado');
        Route::post('/guardarRol', [RolController::class, 'guardarRol'])->name('rol.guardarRol');
        Route::post('/eliminarRol', [RolController::class, 'eliminarRol'])->name('rol.eliminarRol');
    });

    // SUCURSAL
    Route::prefix('/sucursal')->group(function () {
        Route::get('/listado', [SucursalController::class, 'listado'])->name('sucursal.listado');
        Route::post('/ajaxListado', [SucursalController::class, 'ajaxListado'])->name('sucursal.ajaxListado');
        Route::post('/guardarSucursal', [SucursalController::class, 'guardarSucursal'])->name('sucursal.guardarSucursal');
        Route::post('/eliminarSucursal', [SucursalController::class, 'eliminarSucursal'])->name('sucursal.eliminarSucursal');
    });

    // FACTURA
    Route::prefix('/factura')->group(function () {
        Route::get('/formulario', [FacturaController::class, 'formulario'])->name('factura.formulario');
        Route::post('/ajaxListadoClientesBusqueda', [FacturaController::class, 'ajaxListadoClientesBusqueda'])->name('factura.ajaxListadoClientesBusqueda');
    });

    // CLIENTE
    Route::prefix('/cliente')->group(function () {
        Route::get('/listado', [ClienteController::class, 'listado'])->name('cliente.listado');
        Route::post('/ajaxListado', [ClienteController::class, 'ajaxListado'])->name('cliente.ajaxListado');
        Route::post('/guardarCliente', [ClienteController::class, 'guardarCliente'])->name('cliente.guardarCliente');
        Route::post('/eliminarCliente', [ClienteController::class, 'eliminarCliente'])->name('cliente.eliminarCliente');
    });

    // DEPARTAMENTO
    Route::prefix('/departamento')->group(function () {
        Route::get('/listado', [DepartamentoController::class, 'listado'])->name('departamento.listado');
        Route::post('/ajaxListado', [DepartamentoController::class, 'ajaxListado'])->name('departamento.ajaxListado');
        Route::post('/guardarDepartamento', [DepartamentoController::class, 'guardarDepartamento'])->name('departamento.guardarDepartamento');
    });

    // PROVINCIAS
    Route::prefix('provincia')->group(function () {
        Route::post('/ajaxListado', [ProvinciaController::class, 'ajaxListado'])->name('provincia.ajaxListado');
        Route::post('/guardarProvincia', [ProvinciaController::class, 'guardarProvincia'])->name('provincia.guardarProvincia');
    });

    // CATEGORIAS
    Route::prefix('categoria')->group(function () {
        Route::get('/listado', [CategoriaController::class, 'listado'])->name('categoria.listado');
        Route::post('/ajaxListado', [CategoriaController::class, 'ajaxListado'])->name('categoria.ajaxListado');
        Route::post('/guardarCategoria', [CategoriaController::class, 'guardarCategoria'])->name('categoria.guardarCategoria');
    });

    //MOVIMIENTOS
    Route::prefix('movimiento')->group(function () {
        Route::post('/guardarIngreso', [App\Http\Controllers\MovimientoController::class, 'guardarIngreso'])->name('movimientos.guardarIngreso');
        Route::post('/guardarSalida', [App\Http\Controllers\MovimientoController::class, 'guardarSalida'])->name('movimientos.guardarSalida');
        Route::post('/movimientos/transferencia', [App\Http\Controllers\MovimientoController::class, 'guardarTransferencia'])->name('movimientos.transferencia');
    });

    //PEDIDOS

    Route::prefix('pedidos')->group(function () {


        Route::get('/listado', [PedidosController::class, 'listado'])->name('pedidos.listado');
        Route::get('/create', [App\Http\Controllers\PedidosController::class, 'create'])->name('pedidos.create');
        Route::post('/store', [App\Http\Controllers\PedidosController::class, 'store'])->name('pedidos.store');
        Route::post('/{id}/confirmar', [App\Http\Controllers\PedidosController::class, 'confirmar'])->name('pedidos.confirmar');
        Route::post('/{id}/cancelar', [App\Http\Controllers\PedidosController::class, 'cancelar'])->name('pedidos.cancelar');
        Route::get('/{id}/obtener', [App\Http\Controllers\PedidosController::class, 'obtener'])->name('pedidos.obtener');
        Route::put('/{id}/actualizar', [App\Http\Controllers\PedidosController::class, 'actualizar'])->name('pedidos.actualizar');

    });



});

require __DIR__ . '/auth.php';
