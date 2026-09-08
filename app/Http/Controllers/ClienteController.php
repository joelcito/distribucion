<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function listado(){

        $departamentos = Departamento::all();
        $previncias = Provincia::all();

        return view('cliente.listado')->with(compact('departamentos', 'previncias'));
    }

    public function ajaxListado(Request $request){
        if($request->ajax()){
            $clientes = Cliente::all();
            $valores = [
                'listado' => view('cliente.ajaxListado')->with(compact('clientes'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarCliente(Request $request){
        if($request->ajax()){

            // dd($request->all());

            $request->validate([
                'nombres'        => 'required',
                'ap_paterno'     => 'required',
                //'ap_materno'     => 'required',
                // 'cedula'         => 'required',
                /* 'complemento'    => 'required',
                'nit'            => 'required',
                'razon_social'   => 'required',
                'correo'         => 'required',
                'numero_celular' => 'required', */
            ]);

            $id = $request->input('id');

            $nombres         = $request->input('nombres');
            $ap_paterno      = $request->input('ap_paterno');
            $ap_materno      = $request->input('ap_materno');
            $cedula          = $request->input('cedula');
            $complemento     = $request->input('complemento');
            $nit             = $request->input('nit');
            $razon_social    = $request->input('razon_social');
            $correo          = $request->input('correo');
            $numero_celular  = $request->input('numero_celular');
            $usuario         = Auth::user();
            $codigo_cliente  = $request->input('codigo_cliente');
            $nombre_farmcia  = $request->input('nombre_farmacia');
            $provincia_id    = $request->input('provincia_id');
            $ubicacion       = $request->input('direccion');
            $departamento_id = $request->input('departamento_id');

            if( $id == 0 ){
                // $request->validate([
                //     'cedula' => 'unique:clientes,cedula'
                // ]);

                $cliente                     = new Cliente();
                $cliente->usuario_creador_id = $usuario->id;
            }else{
                // if( $existe = Cliente::where('cedula', $cedula)->where('id', '!=', $id)->first() ){
                //     $request->validate([
                //         'cedula' => 'unique:clientes,cedula'
                //     ]);
                // }
                $cliente = Cliente::find($id);
                $cliente->usuario_modificador_id = $usuario->id;
            }

            $cliente->nombres         = $nombres;
            $cliente->ap_paterno      = $ap_paterno;
            $cliente->ap_materno      = $ap_materno;
            $cliente->cedula          = $cedula;
            $cliente->complemento     = $complemento;
            $cliente->nit             = $nit;
            $cliente->razon_social    = $razon_social;
            $cliente->correo          = $correo;
            $cliente->numero_celular  = $numero_celular;
            $cliente->codigo_cliente  = $codigo_cliente;
            $cliente->nombre_farmcia  = $nombre_farmcia;
            $cliente->provincia_id    = $provincia_id;
            $cliente->ubicacion       = $ubicacion;
            $cliente->departamento_id = $departamento_id;
            $cliente->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminarCliente(Request $request){
        if($request->ajax()){

            $id = $request->input('id');
            $usuario = Auth::user();

            $cliente = Cliente::find($id);
            $cliente->usuario_eliminador_id = $usuario->id;
            $cliente->save();

            Cliente::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
