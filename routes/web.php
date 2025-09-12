<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SucursalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('home');
    // return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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
});

require __DIR__ . '/auth.php';
