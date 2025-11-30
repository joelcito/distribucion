<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ReportesController;
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
        Route::post('/ajaxPorCategoria', [App\Http\Controllers\ProductoController::class, 'ajaxPorCategoria'])->name('producto.ajaxPorCategoria');
        Route::post('/obtenerProducto', [App\Http\Controllers\ProductoController::class, 'obtenerProducto'])->name('producto.obtenerProducto');
        Route::post('/ajaxListadoTransferencia', [App\Http\Controllers\ProductoController::class, 'ajaxListadoTransferencia'])->name('producto.ajaxListadoTransferencia');
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
        Route::get('/formularioPedido', [FacturaController::class, 'formularioPedido'])->name('factura.formularioPedido');

        Route::post('/ajaxListadoClientesBusqueda', [FacturaController::class, 'ajaxListadoClientesBusqueda'])->name('factura.ajaxListadoClientesBusqueda');
        Route::get('/formularioVenta', [FacturaController::class, 'formularioVenta'])->name('factura.formularioVenta');
        Route::post('/emitirRecibo', [FacturaController::class, 'emitirRecibo'])->name('factura.emitirRecibo');
        Route::get('/listado', [FacturaController::class, 'listado'])->name('factura.listado');
        Route::post('/ajaxListadoFacturas', [FacturaController::class, 'ajaxListadoFacturas'])->name('factura.ajaxListadoFacturas');
        Route::get('/imprimeRecibo/{factura_id}', [FacturaController::class, 'imprimeRecibo'])->name('factura.imprimeRecibo');
        Route::get('/formularioVentaPedido/{pedido_id}', [FacturaController::class, 'formularioVentaPedido'])->name('factura.formularioVentaPedido');

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
        Route::get('/imprimePedido/{id}', [App\Http\Controllers\PedidosController::class, 'imprimePedido'])->name('pedidos.imprimePedido');

    });

    //CATALOGO
    Route::prefix('catalogo')->group(function () {
        Route::get('/listadoCatalogo', [App\Http\Controllers\ProductoController::class, 'listadoCatalogo'])->name('catalogo.listadoCatalogo');

    });

    //PAGO
    Route::prefix('/pago')->group(function () {
        Route::post('/guardarTipoIngresoSalida', [PagoController::class, 'guardarTipoIngresoSalida']);
        Route::get('/listado', [PagoController::class, 'listado'])->name('pago.listado');
        Route::post('/ajaxListado', [PagoController::class, 'ajaxListado'])->name('pago.ajaxListado');
        Route::get('/listadoDeuda', [PagoController::class, 'listadoDeuda'])->name('pago.listadoDeuda');
        Route::post('/ajaxListadoDeuda', [PagoController::class, 'ajaxListadoDeuda'])->name('pago.ajaxListadoDeuda');
        Route::post('/ajaxFormPagoDeuda', [PagoController::class, 'ajaxFormPagoDeuda'])->name('pago.ajaxFormPagoDeuda');
        Route::post('/guardarPagoDeuda', [PagoController::class, 'guardarPagoDeuda'])->name('pago.guardarPagoDeuda');
        Route::post('/identificarProvincias', [PagoController::class, 'identificarProvincias'])->name('pago.identificarProvincias');
    });

});

//REPORTE
Route::prefix('/reporte')->group(function () {
    Route::get('/reporteStock', [PagoController::class, 'reporteStock'])->name('reporte.reporteStock');
    //Route::get('/reporteMovimiento', [PagoController::class, 'reporteMovimiento'])->name('reporte.reporteMovimiento');

    Route::get('/ventas', [ReportesController::class, 'vistaVentas'])->name('reporte.reporteVenta');
    Route::post('/ventas/listado', [ReportesController::class, 'buscarVentas'])->name('reporte.ventas.listado');
    Route::get('/reportes/listar-ventas', [ReportesController::class, 'listarVentas'])->name('reportes.listarVentas');
    Route::get('/imprimeReporteVentas/{inicio}/{fin}', [ReportesController::class, 'imprimeReporteVentas'])->name('reporte.imprimeReporteVentas');

    Route::get('/imprimeStock/{id}', [ReportesController::class, 'imprimeStock'])->name('factura.imprimeStock');

    // Mostrar vista
    Route::get('reporte/movimientos', [ReportesController::class, 'vistaMovimientos'])->name('reporte.reporteMovimiento');

    // Obtener datos por AJAX
    Route::post('reporte/movimientos/listar', [ReportesController::class, 'listarMovimientos'])->name('reporte.movimientos.listar');

    // PDF
    Route::get('reporte/movimientos/pdf/{fecha}/{producto}', [ReportesController::class, 'pdfMovimientos'])->name('reporte.movimientos.pdf');

    Route::post('/movimientos/listado', [ReportesController::class, 'buscarMovimientos'])->name('reporte.movimientos.listado');
    Route::get(
        '/imprimeMovimientosPorFechaYProducto/{fecha}/{producto_id}',
        [ReportesController::class, 'imprimeMovimientosPorFechaYProducto']
    )
        ->name('reporte.imprimeMovimientos');

});



require __DIR__ . '/auth.php';
