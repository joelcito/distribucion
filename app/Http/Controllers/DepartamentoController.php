<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartamentoController extends Controller
{
    public function listado(){
        return view('departamento.listado');
    }

    public function ajaxListado(Request $request){

        if($request->ajax()){

            $departamentos = Departamento::all();

            $valores = [
                'listado' => view('departamento.ajaxListado')->with(compact('departamentos'))->render()
            ];

            $data = Respuesta::success($valores, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }

    public function guardarDepartamento(Request $request){

        if($request->ajax()){

            $departamento_id = $request->input('id');
            $nombre          = $request->input('nombre');
            $usuario         = Auth::user();

            if($departamento_id == "0"){
                $departamento = new Departamento(); // CREACION DE UN OBJETO QUE DESPUES SE VA CONVERR EN UN REGISTOR DE DB
                $departamento->usuario_creador_id = $usuario->id;
            }else{
                $departamento = Departamento::find($departamento_id); //BUSCANDO EN LA BD EL ROL EL $rol_id
                $departamento->usuario_modificador_id = $usuario->id;
            }

            $departamento->nombre = $nombre;
            $departamento->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
