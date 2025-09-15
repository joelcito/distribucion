<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class ProductoController extends Controller
{
    public function listado()
    {
        return view('producto.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $productos = Producto::with('proveedor')->get();
            $valores = [
                'listado' => view('producto.ajaxListado')->with(compact('productos'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarProducto(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->input('id');
            $codigo = $request->input('codigo');
            $nombre = $request->input('nombre');
            $proveedores_idproveedores = $request->input('proveedores_idproveedores');
            $precio_compra = $request->input('precio_compra');
            $precio_venta = $request->input('precio_venta');
            $usuario = Auth::user();

            if ($producto_id == "0") {
                $producto = new Producto();
                $producto->usuario_creador_id = $usuario->id;
                $producto->usuario_modificador_id = $usuario->id;
            } else {
                $producto = Producto::find($producto_id);
                $producto->usuario_modificador_id = $usuario->id;
            }
            $producto->codigo = $codigo;
            $producto->nombre = $nombre;
            $producto->proveedores_idproveedores = $proveedores_idproveedores;
            $producto->precio_compra = $precio_compra;
            $producto->precio_venta = $precio_venta;
            $producto->save();
            $data = Respuesta::success(null, "Producto guardado correctamente");
        } else {
            $data = Respuesta::error(null, "Error al guardar el producto");
        }
        return $data;
    }

    public function eliminarProducto(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $producto = Producto::find($id);
            if ($producto) {
                $usuario = Auth::user();
                $producto->usuario_eliminador_id = $usuario->id;
                $producto->save();
                $producto->delete();
                $data = Respuesta::success(null, "Producto eliminado correctamente");
            } else {
                $data = Respuesta::error(null, "Producto no encontrado");
            }
        } else {
            $data = Respuesta::error(null, "Error al eliminar el producto");
        }
        return $data;
    }

    public function ajaxStockSucursal(Request $request){
        if($request->ajax()){
            $producto_id = $request->input('producto_id');

            $sucursales = Sucursal::withSum(['movimientos' => function ($query) use($producto_id) {
                                                $query->where('producto_id', $producto_id);
                                            }], 'ingreso')
                                    ->withSum(['movimientos' => function ($query) use($producto_id) {
                                                $query->where('producto_id', $producto_id);
                                            }], 'salida')
                                    ->get();
            $producto = Producto::find($producto_id);
            $valores = [
                'listado' => view('producto.ajaxStockSucursal')->with(compact('sucursales', 'producto'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }
}
