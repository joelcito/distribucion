<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Utils\Respuesta;
use Auth;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Movimiento $movimiento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movimiento $movimiento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movimiento $movimiento)
    {
        //
    }

    public function guardarIngreso(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $movimiento_id = $request->integer('movimiento_id');
            $usuario = Auth::user();

            if ($movimiento_id === 0) {
                $movimiento = new Movimiento();
                $movimiento->usuario_creador_id = $usuario->id;
                $movimiento->usuario_modificador_id = $usuario->id;
            } else {
                $movimiento = Movimiento::find($movimiento_id);
                $movimiento->usuario_modificador_id = $usuario->id;
            }
            $movimiento->producto_id = $request->input('producto_id');
            $movimiento->detalle_id = $request->input('detalle_id');
            $movimiento->sucursal_id = $request->input('sucursal_id');
            $movimiento->ingreso = $request->input('ingreso');
            //$movimiento->salida = $request->input('salida');
            $movimiento->descripcion = $request->input('descripcion');
            $movimiento->estado = 1;
            $movimiento->lotes = $request->input('lotes');
            $movimiento->fecha_vencimiento = $request->input('fecha_vencimiento');

            $movimiento->save();

            $data = Respuesta::success(null, "movimiento guardado correctamente");
        } else {
            $data = Respuesta::error(null, "Error al guardar el movimiento");
        }
        return $data;
    }
}
