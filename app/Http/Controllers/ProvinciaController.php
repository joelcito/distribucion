<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProvinciaController extends Controller
{
    public function ajaxListado(Request $request){

        if($request->ajax()){

            // dd($request->all());

            $departamento_id = $request->input('departamento_id');

            $provincias = Provincia::where('departamento_id', $departamento_id)->get();

            $valores = [
                'listado' => view('provincia.ajaxListado')->with(compact('provincias', 'departamento_id'))->render()
            ];

            $data = Respuesta::success($valores, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function guardarProvincia(Request $request){

        if($request->ajax()){

            // dd($request->all());

            $departamento_id = $request->input('new_procincia_departameto');
            $provincia_id    = $request->input('provincia_id');
            $nombre          = $request->input('nombre_provincia');
            $usuario         = Auth::user();

            if($provincia_id == "0"){
                $provincia = new Provincia(); // CREACION DE UN OBJETO QUE DESPUES SE VA CONVERR EN UN REGISTOR DE DB
                $provincia->usuario_creador_id = $usuario->id;
            }else{
                $provincia = Provincia::find($provincia_id); //BUSCANDO EN LA BD EL ROL EL $rol_id
                $provincia->usuario_modificador_id = $usuario->id;
            }

            $provincia->departamento_id = $departamento_id;
            $provincia->nombre          = $nombre;
            $provincia->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
