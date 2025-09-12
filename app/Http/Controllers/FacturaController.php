<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Utils\Respuesta;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function formulario(Request $request){

        $servicios      = Producto::all();

        return view('factura.formulario')->with(compact('servicios'));

    }

    public function ajaxListadoClientesBusqueda(Request $request){
        if($request->ajax()){

            $query = Cliente::select('*');

            if(!is_null($request->input('nit_escogido'))){
                $nit = $request->input('nit_escogido');
                $query->where('nit', $nit)
                        ->orWhere('cedula', $nit);
            }

            if(!is_null($request->input('nombre_escogido'))){
                $nombre = $request->input('nombre_escogido');
                $query->where('nombres', 'LIKE', "%$nombre%");
            }

            if(!is_null($request->input('ap_paterno_escogido'))){
                $paterno = $request->input('ap_paterno_escogido');
                $query->where('ap_paterno', 'LIKE', "%$paterno%");
            }

            if(!is_null($request->input('ap_materno_escogido'))){
                $materno = $request->input('ap_materno_escogido');
                $query->where('ap_materno', 'LIKE', "%$materno%");
            }

            if(
                !is_null($request->input('cedula_escogido')) &&
                !is_null($request->input('nombre_escogido')) &&
                !is_null($request->input('ap_paterno_escogido')) &&
                !is_null($request->input('ap_materno_escogido'))
            ){

                $clientes = $query->limit(5)->get();

            }else{
                $clientes = $query->orderBy('id', 'desc')->limit(10)->get();
            }

            // dd($clientes, $request->all());

            // $data['text']   = 'No existe';
            // $data['estado'] = 'success';
            // $data['cantidad'] = count($clientes);
            // $data['listado'] = view('factura.ajaxListadoClientesBusqueda')->with(compact('clientes'))->render();

            $valores = [
                'listado' => view('factura.ajaxListadoClientesBusqueda')->with(compact('clientes'))->render(),
                'cantidad' => count($clientes)
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
