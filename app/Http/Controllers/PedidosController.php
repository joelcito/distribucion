<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidosController extends Controller
{

    // Mostrar formulario de pedido
    public function create(Request $request)
    {

        $clienteSeleccionado = null;
        if ($request->has('cliente_id')) {
            $clienteSeleccionado = Cliente::find($request->cliente_id);
        }

        $usuario = Auth::user();

        //$productos = Producto::all();

        $productos = Producto::with('categoria')->get();
        // $producto = new Producto();
        // $productos = $producto->productosDsoponibles(null, $usuario->sucursal_id);

        return view('factura.formulario', compact('clienteSeleccionado', 'productos'));
    }

    public function listado()
    {
        $pedidos = Pedido::with('cliente')->orderBy('id', 'desc')->get();
        $productos = Producto::all();

        return view('factura.formulario', compact('pedidos', 'productos'));
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
            $pedido->usuario_creador_id = $usuario->id;
            $pedido->usuario_id = $usuario->id;
            $pedido->tipo = $request->tipo;
            $pedido->fecha = $request->fecha;
            $pedido->pedidos_productos = json_encode($productos);
            $pedido->estado = 'PENDIENTE';
            $pedido->save();


            foreach ($productos as $producto) {
                Movimiento::create([
                    'usuario_creador_id' => $usuario->id,
                    'usuario_id' => $usuario->id,
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

    //editar
    public function obtener($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);

            $productos = json_decode($pedido->pedidos_productos, true);
            if (!is_array($productos)) {
                $productos = [];
            }

            return response()->json([
                'estado' => true,
                'pedido' => [
                    'id' => $pedido->id,
                    'cliente' => $pedido->cliente->nombres ?? 'N/A',
                    'fecha' => $pedido->fecha ? $pedido->fecha->format('Y-m-d') : 'N/A',
                    'tipo' => $pedido->tipo,
                    'estado' => $pedido->estado,
                    'productos' => $productos
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage()]);
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pedido = Pedido::findOrFail($id);
            $productosEntrada = $request->input('productos');

            if (!is_array($productosEntrada)) {
                throw new \Exception('Formato inválido de productos');
            }

            $productos = [];
            foreach ($productosEntrada as $p) {
                $prod = Producto::find($p['id']);
                if (!$prod)
                    continue;

                $productos[] = [
                    'producto_id' => $prod->id,
                    'nombre' => $prod->nombre,
                    'cantidad' => $p['cantidad']
                ];
            }

            $pedido->pedidos_productos = json_encode($productos);
            $pedido->save();

            DB::commit();

            return response()->json(['estado' => true, 'mensaje' => 'Pedido actualizado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['estado' => false, 'mensaje' => 'Error al actualizar pedido: ' . $e->getMessage()]);
        }
    }

    public function imprimePedido(Request $request, $id)
    {
        $usuario = Auth::user();
        $sucursalSeleccionado = session('sucursal_seleccionado');
        $puntoVentaSeleccionado = session('puntoVenta_seleccionado');

        $pedido = Pedido::find($id);

        if ($pedido) {
            $pdf = Pdf::loadView('factura.pdf.imprimePedido', compact('pedido'))->setPaper('letter');
            return $pdf->stream('pedido.pdf');
        } else {
            abort(404);
        }
    }

    public function obtenerProducto(Request $request)
    {
        $producto = Producto::find($request->id);

        if (!$producto) {
            return response()->json(['estado' => false]);
        }

        $stock = Movimiento::where('producto_id', $producto->id)
            ->sum('ingreso') - Movimiento::where('producto_id', $producto->id)->sum('salida');


        return response()->json([
            'estado' => true,
            'data' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'stock' => $stock
            ]
        ]);
    }

}
