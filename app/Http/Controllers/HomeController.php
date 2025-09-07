<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Factura;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeController extends Controller
{
    use AuthorizesRequests;

    public function index() {
        // $fecha_ini = date('Y-m-d').' 00:00:00';
        // $fecha_fin = date('Y-m-d').' 23:59:59';

        // $cantRecibo = Factura::whereBetween('fecha', [$fecha_ini, $fecha_fin])
        //                     ->whereNull('numero_factura')
        //                     ->whereNotNull('numero_recibo')
        //                     ->count();
        // $cantFactura = Factura::whereBetween('fecha', [$fecha_ini, $fecha_fin])
        //                     ->whereNotNull('numero_factura')
        //                     ->whereNull('numero_recibo')
        //                     ->count();
        // $cantUsuarios = User::where('rol_id', '!=', 1)->count();

        // $pedidos = Movimiento::query()
        //                     ->join('productos', 'movimientos.producto_id', '=', 'productos.id')
        //                     ->join('sucursales', 'movimientos.sucursal_id', '=', 'sucursales.id')
        //                     ->select([
        //                         'movimientos.producto_id',
        //                         'movimientos.sucursal_id',
        //                         'productos.nombre as producto_nombre',
        //                         'productos.minimo_stock',
        //                         'sucursales.nombre as sucursal_nombre',
        //                         DB::raw('SUM(movimientos.ingreso) - SUM(movimientos.salida) as total'),
        //                     ])
        //                     ->groupBy(
        //                         'movimientos.producto_id',
        //                         'movimientos.sucursal_id',
        //                         'productos.nombre',
        //                         'productos.minimo_stock',
        //                         'sucursales.nombre'
        //                     )
        //                     ->havingRaw('total < productos.minimo_stock')
        //                     ->orderBy('movimientos.sucursal_id', 'ASC')
        //                     ->get();

        // $pedidoUrgente = Movimiento::query()
        //                     ->join('productos', 'movimientos.producto_id', '=', 'productos.id')
        //                     ->join('sucursales', 'movimientos.sucursal_id', '=', 'sucursales.id')
        //                     ->select([
        //                         'movimientos.producto_id',
        //                         'movimientos.sucursal_id',
        //                         'productos.nombre as producto_nombre',
        //                         'productos.minimo_stock',
        //                         'sucursales.nombre as sucursal_nombre',
        //                         DB::raw('SUM(movimientos.ingreso) - SUM(movimientos.salida) as total'),
        //                     ])
        //                     ->groupBy(
        //                         'movimientos.producto_id',
        //                         'movimientos.sucursal_id',
        //                         'productos.nombre',
        //                         'productos.minimo_stock',
        //                         'sucursales.nombre'
        //                     )
        //                     ->havingRaw('total = 0')
        //                     ->orderBy('movimientos.sucursal_id', 'ASC')
        //                     ->get();
        // return view('home.inicio')->with(compact(['cantFactura', 'cantRecibo', 'cantUsuarios', 'pedidos', 'pedidoUrgente']));

        return view('home.inicio');
    }


}
