<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Utils\Respuesta;
use Auth;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{

    public function guardarIngreso(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $movimiento_id = $request->integer('movimiento_id');
            $usuario = Auth::user();

            if ($movimiento_id === 0) {
                $movimiento = new Movimiento();
                $movimiento->usuario_creador_id = $usuario->id;
                // $movimiento->usuario_modificador_id = $usuario->id;
            } else {
                $movimiento = Movimiento::find($movimiento_id);
                $movimiento->usuario_modificador_id = $usuario->id;
            }
            $movimiento->producto_id       = $request->input('producto_id');
            $movimiento->detalle_id        = $request->input('detalle_id');
            $movimiento->sucursal_id       = $request->input('sucursal_id');
            $movimiento->ingreso           = $request->input('ingreso');
            $movimiento->salida            = 0;
            $movimiento->descripcion       = $request->input('descripcion');
            $movimiento->lotes             = $request->input('lotes');
            $movimiento->fecha_vencimiento = $request->input('fecha_vencimiento');
            $movimiento->fecha             = date('Y-m-d H:i:s');
            $movimiento->precio_compra     = $request->input('precio_compra');
            $movimiento->precio_venta      = $request->input('precio_venta');
            $movimiento->save();

            $data = Respuesta::success(null, "movimiento guardado correctamente");
        } else {
            $data = Respuesta::error(null, "Error al guardar el movimiento");
        }
        return $data;
    }

    public function guardarSalida(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $movimiento_id = $request->integer('movimiento_id');
            $salida_movimiento_id = $request->integer('salida_movimiento_id');
            $usuario = Auth::user();

            // BUSCAMOS EL MOVIMIENTO
            $movimiento = Movimiento::find($salida_movimiento_id);

            if($movimiento){

                $movimientoSalida                     = new Movimiento();
                $movimientoSalida->usuario_creador_id = $usuario->id;
                $movimientoSalida->producto_id        = $request->input('producto_id');
                $movimientoSalida->movimiento_id      = $salida_movimiento_id;
                $movimientoSalida->salida             = $request->input('salida');
                $movimientoSalida->ingreso            = 0;
                $movimientoSalida->fecha              = date('Y-m-d H:i:s');
                $movimientoSalida->sucursal_id        = $request->input('sucursal_id');
                $movimientoSalida->descripcion        = $request->input('descripcion');
                $movimientoSalida->save();

                $data = Respuesta::success(null, "movimiento guardado correctamente");

            }else{
                $data = Respuesta::success(null, "movimiento NO encotrado");
            }

            // if ($movimiento_id === 0) {
            //     $movimiento = new Movimiento();
            //     $movimiento->usuario_creador_id = $usuario->id;
            //     $movimiento->usuario_modificador_id = $usuario->id;
            // } else {
            //     $movimiento = Movimiento::find($movimiento_id);
            //     $movimiento->usuario_modificador_id = $usuario->id;
            // }
            // $movimiento->producto_id = $request->input('producto_id');
            // $movimiento->detalle_id = $request->input('detalle_id');
            // $movimiento->sucursal_id = $request->input('sucursal_id');
            //$movimiento->ingreso = $request->input('ingreso');
            // $movimiento->salida = $request->input('salida');
            // $movimiento->descripcion = $request->input('descripcion');
            // $movimiento->estado = 1;
            // $movimiento->fecha = $request->input('fecha_salida');
            // $movimiento->save();
            // $data = Respuesta::success(null, "movimiento guardado correctamente");
        } else {
            $data = Respuesta::error(null, "Error al guardar el movimiento");
        }
        return $data;
    }

    public function guardarTransferencia(Request $request)
    {
        if ($request->ajax()) {

            // dd($request->all());

            // VERIFICAMOS SI EL STOKC YA EXISTE EN LA SUCRUSAL
            $movimiento_id    = $request->input('movimiento');
            $sucursal_origen  = $request->input('sucursal_origen');
            $sucursal_destino = $request->input('sucursal_destino');
            $usuario          = Auth::user();

            $movimiento = Movimiento::find($movimiento_id);

            $movimientoExiste = Movimiento::where('fecha_vencimiento', $movimiento->fecha_vencimiento)
                                            ->where('lotes', $movimiento->lotes)
                                            ->where('sucursal_id', $sucursal_destino)
                                            ->first();
                                            // ->toSql();
                                            // dd(
                                            //     $movimientoExiste,
                                            //     $movimiento->fecha_vencimiento,
                                            //     $movimiento->lotes,
                                            //     $sucursal_destino,
                                            //     $request->all()
                                            // );

            if($movimientoExiste){
                $cantidadExistente          = $movimientoExiste->ingreso;
                $movimientoExiste->ingreso = $cantidadExistente + $request->input('cantidad');
                $movimientoExiste->save();
            }else{
                $ingreso                         = new Movimiento();
                $ingreso->usuario_creador_id     = $usuario->id;
                $ingreso->usuario_modificador_id = $usuario->id;
                $ingreso->producto_id            = $request->input('producto_id');
                $ingreso->sucursal_id            = $request->input('sucursal_destino');
                $ingreso->ingreso                = $request->input('cantidad');
                $ingreso->fecha                  = $request->input('fecha');
                $ingreso->descripcion            = "INGRESO POR TRANSFERENCIA";
                $ingreso->fecha_vencimiento      = $movimiento->fecha_vencimiento;
                $ingreso->lotes                  = $movimiento->lotes;
                $ingreso->precio_venta           = $movimiento->precio_venta;
                $ingreso->salida                 = 0;
                $ingreso->save();
            }

            $salida                         = new Movimiento();
            $salida->usuario_creador_id     = $usuario->id;
            $salida->usuario_modificador_id = $usuario->id;
            $salida->producto_id            = $request->input('producto_id');
            $salida->sucursal_id            = $request->input('sucursal_origen');
            $salida->salida                 = $request->input('cantidad');
            $salida->descripcion            = "SALIDA POR TRANSFERENCIA";
            $salida->fecha                  = $request->input('fecha');
            $salida->ingreso                = 0;
            $salida->movimiento_id          = $movimiento_id;
            $salida->save();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Transferencia registrada correctamente'
            ]);
        } else {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Error al guardar la transferencia'
            ]);
        }
    }

}
