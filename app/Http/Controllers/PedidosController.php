<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Utils\Respuesta;
use Auth;
use DB;
use Illuminate\Http\Request;

class PedidosController extends Controller
{



    // Mostrar formulario de pedido
    public function create(Request $request)
    {
        // Aquí puedes recibir un cliente_id desde la lista anterior
        $clienteSeleccionado = null;
        if ($request->has('cliente_id')) {
            $clienteSeleccionado = Cliente::find($request->cliente_id);
        }

        return view('factura.formulario', compact('clienteSeleccionado'));
    }

    public function listado()
    {
        $pedidos = Pedido::with('cliente')->orderBy('id', 'desc')->get();
        return view('factura.formulario', compact('pedidos'));
    }

    public function store(Request $request)
    {
        try {
            $usuario = Auth::user();
            $request->validate([
                'cliente_id' => 'required|exists:clientes,id',

                'tipo' => 'required|string',
                'fecha' => 'required|date',
                'productos' => 'required'
            ]);

            DB::beginTransaction();


            $productos = json_decode($request->productos, true);
            if (!$productos || !is_array($productos)) {
                throw new \Exception("Formato inválido de productos (JSON esperado).");
            }


            $pedido = new Pedido();
            $pedido->cliente_id = $request->cliente_id;
            $pedido->usuario_id = $usuario->id;
            $pedido->tipo = $request->tipo;
            $pedido->fecha = $request->fecha;
            $pedido->pedidos_productos = json_encode($productos);
            $pedido->estado = 'PENDIENTE';
            $pedido->save();


            foreach ($productos as $producto) {
                Movimiento::create([
                    'usuario_creador_id' => $request->usuario_id,
                    'producto_id' => $producto['producto_id'],
                    'sucursal_id' => $producto['sucursal_id'] ?? null,
                    'salida' => $producto['cantidad'],
                    'fecha' => now(),
                    'descripcion' => 'Salida por pedido #' . $pedido->id,
                    'precio_venta' => $producto['precio'] ?? null,
                    'lotes' => $producto['lotes'] ?? null,
                    'fecha_vencimiento' => $producto['fecha_vencimiento'] ?? null,
                    'estado' => 'pendiente'
                ]);
            }

            DB::commit();

            return response()->json(['estado' => true, 'pedido_id' => $pedido->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => false, 'message' => $e->getMessage()]);
        }
    }




    //cancelar pedido

    public function cancelar($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            $pedido->estado = 'CANCELADO';
            $pedido->save();

            $productos = $pedido->pedidos_productos;
            if (is_string($productos)) {
                $productos = json_decode($productos, true) ?? [];
            }

            foreach ($productos as $producto) {
                $producto_id = $producto['producto_id'] ?? null;
                $cantidad = $producto['cantidad'] ?? 0;
                $sucursal_id = $producto['sucursal_id'] ?? null;

                if ($producto_id && $cantidad > 0) {

                    Movimiento::create([
                        'producto_id' => $producto_id,
                        'sucursal_id' => $sucursal_id,
                        'ingreso' => $cantidad,
                        'descripcion' => 'Reingreso por cancelación de pedido ' . $pedido->id,
                        'fecha' => now(),
                    ]);
                }
            }

            return response()->json(['estado' => true, 'mensaje' => 'Pedido cancelado y stock reingresado']);
        } catch (\Exception $e) {
            \Log::error('Error al cancelar pedido: ' . $e->getMessage());
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage()]);
        }
    }





}
