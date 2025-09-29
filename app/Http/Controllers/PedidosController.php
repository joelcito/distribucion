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

    public function create()
    {
        $clientes = Cliente::all(); // Trae todos los clientes
        return view('ventas.formulario', compact('clientes'));
    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Parsear productos
            $productos = is_array($request->productos) ? $request->productos : json_decode($request->productos, true);

            if (!$productos || count($productos) == 0) {
                return response()->json(['estado' => false, 'message' => 'Debe agregar al menos un producto.']);
            }

            // Guardar pedido
            $pedido = new Pedido();
            $pedido->cliente_id = $request->cliente_id;
            $pedido->usuario_id = Auth::id(); // Usuario autenticado
            $pedido->provincia_id = $request->provincia_id ?? null;
            $pedido->tipo = $request->tipo;
            $pedido->fecha = $request->fecha ?? now();
            $pedido->pedidos_productos = json_encode($productos);
            $pedido->estado = 'pendiente';
            $pedido->save();

            // Crear movimientos
            foreach ($productos as $producto) {
                Movimiento::create([
                    'producto_id' => $producto['producto_id'],
                    'sucursal_id' => $request->sucursal_id ?? null,
                    'salida' => $producto['cantidad'],
                    'fecha' => now(),
                    'descripcion' => 'Reserva por pedido #' . $pedido->id,
                    'pedido_id' => $pedido->id,
                ]);
            }

            DB::commit();
            return response()->json(['estado' => true, 'pedido_id' => $pedido->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Registrar error en logs
            \Log::error('Error guardando pedido: ' . $e->getMessage());
            return response()->json(['estado' => false, 'message' => 'Error interno al guardar el pedido.']);
        }
    }


    // Confirmar pedido = venta
    public function confirmar($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->estado = 'confirmado';
        $pedido->save();

        return response()->json(['estado' => true, 'message' => 'Pedido confirmado']);
    }

    // Cancelar pedido = eliminar movimientos y cambiar estado
    public function cancelar($id)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::findOrFail($id);
            Movimiento::where('pedido_id', $pedido->id)->delete();

            $pedido->estado = 'cancelado';
            $pedido->save();

            DB::commit();
            return response()->json(['estado' => true, 'message' => 'Pedido cancelado']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => false, 'message' => $e->getMessage()]);
        }
    }
}
