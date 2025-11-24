<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;

use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function listado()
    {
        return view('sucursal.listado');
    }

    // public function ajaxListado(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $sucursales = Sucursal::all();
    //         $valores = [
    //             'listado' => view('sucursal.ajaxListado')->with(compact('sucursales'))->render()
    //         ];
    //         $data = \App\Utils\Respuesta::success($valores, "Datos obtenidos correctamente");
    //     } else {
    //         $data = \App\Utils\Respuesta::error(null, "Error al obtener los datos");
    //     }
    //     return $data;
    // }


 public function ajaxListado(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'estado' => false,
                'data' => null,
                'message' => 'No es petición Ajax'
            ]);
        }

        try {
            $sucursales = Sucursal::all()->map(function($sucursal){
                return [
                    'id' => $sucursal->id,
                    'codigo_sucursal' => $sucursal->codigo_sucursal,
                    'nombre' => $sucursal->nombre,
                    'direccion' => $sucursal->direccion,
                ];
            });

            return response()->json([
                'estado' => true,
                'data' => $sucursales,
                'message' => 'Datos obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'estado' => false,
                'data' => null,
                'message' => $e->getMessage()
            ]);
        }
    }



    public function guardarSucursal(Request $request)
    {
        if ($request->ajax()) {
            $sucursal_id = $request->input('id');
            $codigo_sucursal = $request->input('codigo_sucursal');
            $nombre = $request->input('nombre');
            $direccion = $request->input('direccion');
            $usuario = \Illuminate\Support\Facades\Auth::user();

            if ($sucursal_id == "0") {
                $sucursal = new Sucursal();
                $sucursal->usuario_creador_id = $usuario->id;
                $sucursal->usuario_modificador_id = $usuario->id;
            } else {
                $sucursal = Sucursal::find($sucursal_id);
                $sucursal->usuario_modificador_id = $usuario->id;
            }

            $sucursal->codigo_sucursal = $codigo_sucursal;
            $sucursal->nombre = $nombre;
            $sucursal->direccion = $direccion;
            $sucursal->save();

            $data = \App\Utils\Respuesta::success(null, "Sucursal guardada correctamente");
        } else {
            $data = \App\Utils\Respuesta::error(null, "Error al guardar la sucursal");
        }
        return $data;
    }

    public function eliminarSucursal(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $sucursal = Sucursal::find($id);
            if ($sucursal) {
                $usuario = \Illuminate\Support\Facades\Auth::user();
                $sucursal->usuario_eliminador_id = $usuario->id;
                $sucursal->save();
                $sucursal->delete();
                $data = \App\Utils\Respuesta::success(null, "Sucursal eliminada correctamente");
            } else {
                $data = \App\Utils\Respuesta::error(null, "Sucursal no encontrada");
            }
        } else {
            $data = \App\Utils\Respuesta::error(null, "Error al eliminar la sucursal");
        }
        return $data;
    }

}
