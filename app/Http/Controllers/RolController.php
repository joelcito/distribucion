<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    public function listado(Request $request){
        return view('rol.listado');
    }

    public function ajaxListado(Request $request){

        if($request->ajax()){

            $roles = Rol::all();

            $valores = [
                'listado' => view('rol.ajaxListado')->with(compact('roles'))->render()
            ];

            $data = Respuesta::success($valores, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function guardarRol(Request $request){

        if($request->ajax()){

            // dd($request->all());

            $rol_id = $request->input('id');
            $nombre = $request->input('nombre');
            $usuario = Auth::user();

            if($rol_id == "0"){
                $rol = new Rol(); // CREACION DE UN OBJETO QUE DESPUES SE VA CONVERR EN UN REGISTOR DE DB
                $rol->usuario_creador_id = $usuario->id;
            }else{
                $rol = Rol::find($rol_id); //BUSCANDO EN LA BD EL ROL EL $rol_id
                $rol->usuario_modificador_id = $usuario->id;
            }

            $rol->nombre = $nombre;
            $rol->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function eliminarRol(Request $request){

        if($request->ajax()){

            // dd($request->all());

            $rol_id = $request->input('rol');
            $usuario = Auth::user();

            $rol = Rol::find($rol_id);
            $rol->usuario_eliminador_id = $usuario->id;
            $rol->save();

            Rol::destroy($rol->id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
