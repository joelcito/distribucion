<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class ProveedorController extends Controller
{
    public function listado()
    {
        return view('proveedor.listado');
    }

    // public function ajaxListado(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $proveedores = Proveedor::all();
    //         $valores = [
    //             'listado' => view('proveedor.ajaxListado')->with(compact('proveedores'))->render()
    //         ];
    //         $data = Respuesta::success($valores, "Datos obtenidos correctamente");
    //     } else {
    //         $data = Respuesta::error(null, "Error al obtener los datos");
    //     }
    //     return $data;
    // }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $proveedores = Proveedor::all(); // Devuelve colección, excluye eliminados
            $valores = [
                'listado' => view('proveedor.ajaxListado', compact('proveedores'))->render()
            ];

            return response()->json([
                'estado' => true,
                'data' => $valores,
                'message' => "Datos obtenidos correctamente"
            ]);
        }

        return response()->json([
            'estado' => false,
            'data' => null,
            'message' => "Error al obtener los datos"
        ]);
    }

    public function guardarProveedor(Request $request)
    {
        if ($request->ajax()) {

            // dd($request->all());
            $proveedor_id = $request->input('id');
            $nombre       = $request->input('nombre');
            $nit          = $request->input('nit');
            $razon_social = $request->input('razon_social');
            $direccion    = $request->input('direccion');
            $celular      = $request->input('celular');
            $banco        = $request->input('banco');
            $nro_cuenta   = $request->input('nro_cuenta');
            $usuario      = Auth::user();

            if ($proveedor_id == "0") {
                $proveedor = new Proveedor();
                $proveedor->usuario_creador_id = $usuario->id;
                $proveedor->usuario_modificador_id = $usuario->id;
            } else {
                $proveedor = Proveedor::find($proveedor_id);
                $proveedor->usuario_modificador_id = $usuario->id;
            }
            $proveedor->nombre        = $nombre;
            $proveedor->nit           = $nit;
            $proveedor->razon_social  = $razon_social;
            $proveedor->direccion     = $direccion;
            $proveedor->celular       = $celular;
            $proveedor->banco         = $banco;
            $proveedor->numero_cuenta = $nro_cuenta;
            $proveedor->save();
            $data = Respuesta::success(null, "Proveedor guardado correctamente");
        } else {
            $data = Respuesta::error(null, "Error al guardar el proveedor");
        }
        return $data;
    }

    public function eliminarProveedor(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $proveedor = Proveedor::find($id);
            if ($proveedor) {
                $usuario = Auth::user();
                $proveedor->usuario_eliminador_id = $usuario->id;
                $proveedor->save();
                $proveedor->delete();
                $data = Respuesta::success(null, "Proveedor eliminado correctamente");
            } else {
                $data = Respuesta::error(null, "Proveedor no encontrado");
            }
        } else {
            $data = Respuesta::error(null, "Error al eliminar el proveedor");
        }
        return $data;
    }
}
