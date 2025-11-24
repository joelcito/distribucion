<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class ProductoController extends Controller
{
    public function listado()
    {
        $categotias = Categoria::all();

        // PARA LOS PRODUCTOS
        $producto = new Producto();
        $productoDisponibles = $producto->productosDsoponibles(null, null);
        return view('producto.listado')->with(compact('categotias','productoDisponibles'));
    }

    public function listadoCatalogo()
    {
        $categorias = Categoria::all(); // ⚡ nombre correcto
        return view('producto.listadoCatalogo', compact('categorias'));
    }



    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $productos = Producto::with('proveedor')->get();
            $valores = [
                'listado' => view('producto.ajaxListado')->with(compact('productos'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function ajaxListadoTransferencia(Request $request)
    {

        // dd($request->all());

        if (!$request->ajax()) {
            return response()->json([
                'estado' => false,
                'data' => null,
                'message' => 'No es petición Ajax'
            ]);
        }

        $producto_id = $request->input('producto_id');

        try {
            $sucursales = Sucursal::all()->map(function($sucursal){
                return [
                    'id' => $sucursal->id,
                    'codigo_sucursal' => $sucursal->codigo_sucursal,
                    'nombre' => $sucursal->nombre,
                    'direccion' => $sucursal->direccion,
                ];
            });

            // PARA LOS PRODUCTOS
            $producto = new Producto();
            $productoDisponibles = $producto->productosDsoponibles($producto_id, null);

            $valores = [
                'sucursales' => $sucursales,
                'disponibleProducto' => $productoDisponibles
            ];

            return response()->json([
                'estado' => true,
                'data' => $valores,
                'message' => 'Datos obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'estado' => false,
                'data' => null,
                'message' => $e->getMessage()
            ]);
        }
    }

    // public function guardarProducto(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $producto_id = $request->input('id');
    //         $codigo = $request->input('codigo');
    //         $nombre = $request->input('nombre');
    //         $proveedores_idproveedores = $request->input('proveedores_idproveedores');
    //         $categoria_id = $request->input('categoria_id');
    //         $precio_compra = $request->input('precio_compra');
    //         $precio_venta = $request->input('precio_venta');
    //         $usuario = Auth::user();

    //         if ($producto_id == "0") {
    //             $producto = new Producto();
    //             $producto->usuario_creador_id = $usuario->id;
    //             $producto->usuario_modificador_id = $usuario->id;
    //         } else {
    //             $producto = Producto::find($producto_id);
    //             $producto->usuario_modificador_id = $usuario->id;
    //         }
    //         $producto->codigo = 123;
    //         $producto->nombre = $nombre;
    //         $producto->proveedor_id = $proveedores_idproveedores;
    //         $producto->categoria_id = $categoria_id;
    //         $producto->precio_compra = $precio_compra;
    //         $producto->precio_venta = $precio_venta;
    //         $producto->save();
    //         $data = Respuesta::success(null, "Producto guardado correctamente");
    //     } else {
    //         $data = Respuesta::error(null, "Error al guardar el producto");
    //     }
    //     return $data;
    // }

    public function guardarProducto(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->input('id');
            $usuario = Auth::user();

            // Buscar o crear producto
            if ($producto_id == "0") {
                $producto = new Producto();
                $producto->usuario_creador_id = $usuario->id;
            } else {
                $producto = Producto::find($producto_id);
            }

            $producto->usuario_modificador_id = $usuario->id;
            $producto->codigo                 = $this->generaCodigoProducto();
            $producto->nombre                 = $request->input('nombre');
            $producto->proveedor_id           = $request->input('proveedores_idproveedores');
            $producto->categoria_id           = $request->input('categoria_id');
            $producto->precio_compra          = $request->input('precio_compra');
            $producto->precio_venta           = $request->input('precio_venta');

            // Guardar imágenes (array de objetos)
            if ($request->hasFile('imagenes')) {
                $imagenesGuardadas = $producto->imagenes ?? [];
                foreach ($request->file('imagenes') as $imagen) {
                    $nombre = time() . '_' . $imagen->getClientOriginalName();
                    $ruta = 'uploads/productos/' . $nombre;
                    $imagen->move(public_path('uploads/productos'), $nombre);
                    $imagenesGuardadas[] = [
                        'ruta' => $ruta,
                        'nombre' => $imagen->getClientOriginalName()
                    ];
                }
                $producto->imagenes = $imagenesGuardadas;
            }

            $producto->save();

            return Respuesta::success(null, "Producto guardado correctamente");
        }

        return Respuesta::error(null, "Error al guardar el producto");
    }

    public function eliminarProducto(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $producto = Producto::find($id);
            if ($producto) {
                $usuario = Auth::user();
                $producto->usuario_eliminador_id = $usuario->id;
                $producto->save();
                $producto->delete();
                $data = Respuesta::success(null, "Producto eliminado correctamente");
            } else {
                $data = Respuesta::error(null, "Producto no encontrado");
            }
        } else {
            $data = Respuesta::error(null, "Error al eliminar el producto");
        }
        return $data;
    }

    public function ajaxStockSucursal(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->input('producto_id');

            $sucursales = Sucursal::withSum([
                'movimientos' => function ($query) use ($producto_id) {
                    $query->where('producto_id', $producto_id);
                }
            ], 'ingreso')
                ->withSum([
                    'movimientos' => function ($query) use ($producto_id) {
                        $query->where('producto_id', $producto_id);
                    }
                ], 'salida')
                ->get();
            $producto = Producto::find($producto_id);
            $valores = [
                'listado' => view('producto.ajaxStockSucursal')->with(compact('sucursales', 'producto'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }


    // public function ajaxPorCategoria(Request $request)
    // {
    //     $categoria_id = $request->categoria_id;
    //     $productos = Producto::where('categoria_id', $categoria_id)->get();

    //     $listadoHtml = view('producto.listado', compact('productos'))->render();

    //     return response()->json([
    //         'estado' => true,
    //         'data' => ['listado' => $listadoHtml]
    //     ]);
    // }


    public function ajaxPorCategoria(Request $request)
    {
        $categoriaId = $request->categoria_id;

        $productos = Producto::where('categoria_id', $categoriaId)->get();

        $data = $productos->map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'proveedores_idproveedores' => $p->proveedor_id,
                'categoria_id' => $p->categoria_id,
                'precio_compra' => $p->precio_compra,
                'precio_venta' => $p->precio_venta,
                'imagenes' => is_array($p->imagenes) ? $p->imagenes : json_decode($p->imagenes, true) ?? []
            ];
        });

        return response()->json([
            'estado' => true,
            'data' => $data
        ]);
    }

    // public function obtenerProducto(Request $request)
    // {
    //     $producto = Producto::find($request->id);

    //     if (!$producto) {
    //         return response()->json(['estado' => false, 'message' => 'Producto no encontrado']);
    //     }

    //     return response()->json([
    //         'estado' => true,
    //         'data' => [
    //             'id' => $producto->id,
    //             'nombre' => $producto->nombre,
    //             'categoria_id' => $producto->categoria_id,
    //             'precio_venta' => $producto->precio_venta,
    //             'imagenes' => json_decode($producto->imagenes) ?? []
    //         ]
    //     ]);
    // }


    public function obtenerProducto(Request $request)
    {
        try {
            $producto = Producto::find($request->id);

            if (!$producto) {
                return response()->json(['estado' => false, 'message' => 'Producto no encontrado']);
            }

            // Verificar si es string antes de json_decode
            if (is_string($producto->imagenes)) {
                $imagenes = json_decode($producto->imagenes, true) ?? [];
            } elseif (is_array($producto->imagenes)) {
                $imagenes = $producto->imagenes;
            } else {
                $imagenes = [];
            }

            return response()->json([
                'estado' => true,
                'data' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo,
                    'proveedor_id' => $producto->proveedor_id,
                    'categoria_id' => $producto->categoria_id,
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $producto->precio_venta,
                    'imagenes' => $imagenes,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'estado' => false,
                'message' => 'Error al obtener el producto: ' . $e->getMessage()
            ]);
        }
    }


    private function generaCodigoProducto() {
        $ultimoProducto = Producto::latest()->first();
        if ($ultimoProducto)
            $codigo = str_pad($ultimoProducto->codigo + 1, 6, '0', STR_PAD_LEFT);
        else
            $codigo = '000001';
        return $codigo;
    }
}
