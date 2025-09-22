<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    public function listado(Request $request){
        return view('categoria.listado');
    }

    public function ajaxListado(Request $request){

        if($request->ajax()){

            $categorias = Categoria::all();

            $valores = [
                'listado' => view('categoria.ajaxListado')->with(compact('categorias'))->render()
            ];

            $data = Respuesta::success($valores, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function guardarCategoria(Request $request){

        if($request->ajax()){

            // dd($request->all());

            $rol_id = $request->input('id');
            $nombre = $request->input('nombre');
            $usuario = Auth::user();

            if($rol_id == "0"){
                $categoria = new Categoria(); // CREACION DE UN OBJETO QUE DESPUES SE VA CONVERR EN UN REGISTOR DE DB
                $categoria->usuario_creador_id = $usuario->id;
            }else{
                $categoria = Categoria::find($rol_id); //BUSCANDO EN LA BD EL ROL EL $rol_id
                $categoria->usuario_modificador_id = $usuario->id;
            }

            $categoria->nombre = $nombre;
            $categoria->save();

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
