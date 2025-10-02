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
                'productos' => 'required' // JSON de productos
            ]);

            DB::beginTransaction();

            // Convertir productos JSON en array asociativo
            $productos = json_decode($request->productos, true);
            if (!$productos || !is_array($productos)) {
                throw new \Exception("Formato inválido de productos (JSON esperado).");
            }

            // 1. Guardar pedido
            $pedido = new Pedido();
            $pedido->cliente_id = $request->cliente_id;
            $pedido->usuario_id = $usuario->id;  // usuario que crea el pedido
            $pedido->tipo = $request->tipo;
            $pedido->fecha = $request->fecha;
            $pedido->pedidos_productos = json_encode($productos);
            $pedido->estado = 'pendiente';
            $pedido->save();

            // 2. Guardar movimientos de salida automáticamente
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








}
