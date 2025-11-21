<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Detalle;
use App\Models\Factura;
use App\Models\Movimiento;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Promocion;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;
use Psy\TabCompletion\Matcher\FunctionsMatcher;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PDF;

class FacturaController extends Controller
{
    public function formulario(Request $request)
    {

        $servicios = Producto::all();

        return view('factura.formulario')->with(compact('servicios'));

    }

    public function formularioPedido()
    {
        return view('factura.formularioPedido');
    }

    public function ajaxListadoClientesBusqueda(Request $request)
    {
        if ($request->ajax()) {

            $query = Cliente::select('*');

            if (!is_null($request->input('nit_escogido'))) {
                $nit = $request->input('nit_escogido');
                $query->where('nit', $nit)
                    ->orWhere('cedula', $nit);
            }

            if (!is_null($request->input('nombre_escogido'))) {
                $nombre = $request->input('nombre_escogido');
                $query->where('nombres', 'LIKE', "%$nombre%");
            }

            if (!is_null($request->input('ap_paterno_escogido'))) {
                $paterno = $request->input('ap_paterno_escogido');
                $query->where('ap_paterno', 'LIKE', "%$paterno%");
            }

            if (!is_null($request->input('ap_materno_escogido'))) {
                $materno = $request->input('ap_materno_escogido');
                $query->where('ap_materno', 'LIKE', "%$materno%");
            }

            if (
                !is_null($request->input('cedula_escogido')) &&
                !is_null($request->input('nombre_escogido')) &&
                !is_null($request->input('ap_paterno_escogido')) &&
                !is_null($request->input('ap_materno_escogido'))
            ) {

                $clientes = $query->limit(5)->get();

            } else {
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

        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function formularioVenta(Request $request)
    {

        // $servicios      = Producto::all();
        $servicios = Producto::select('productos.id', 'productos.nombre', 'productos.precio_venta', 'categorias.nombre')
                            ->join('movimientos', 'movimientos.producto_id', '=', 'productos.id')
                            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                            ->selectRaw('SUM(movimientos.ingreso) - SUM(movimientos.salida) as stock')
                            ->groupBy('productos.id', 'movimientos.fecha_vencimiento', 'productos.nombre', 'categorias.nombre')
                            ->get();
            // ->toSql();
            // dd($servicios);

        $promociones = Promocion::all();

        return view('factura.formularioVenta')->with(compact('servicios', 'promociones'));

    }

    public function emitirRecibo(Request $request)
    {

        if ($request->ajax()) {
            try {

                // dd($request->all());

                $usuario             = Auth::user();
                $sucursal_objeto     = $usuario->sucursal;
                $sucursal_id         = $sucursal_objeto->id;
                $carroVentas         = $request->input('carrito');
                $cliente_id          = $request->input('cliente_id');
                $descuento_adicional = $request->input('descuento_adicional');
                $monto_total         = (float) $request->input('monto_total');
                $tipo_pago_pagado    = $request->input('tipo_pago_pagado');
                $realizo_pago        = $request->input('realizo_pago');
                $monto_total_pagado  = (float) $request->input('monto_total_pagado');
                $monto_pagado        = (float) $request->input('monto_pagado');
                $cambio_pagado       = (float) $request->input('cambio_pagado');
                $pedido_id           = (int) $request->input('pedido_id');

                // dd($request->all());

                $idDetalles = array();

                //================================== COMENZAMOS LA TRANSACCION ==================================
                DB::beginTransaction();

                // ----------------- AGREGAMOS EN L ATABLA DETALLES -----------------
                foreach ($carroVentas as $key => $item) {

                    $servicio = Producto::find($item['servicio_id']);

                    $detalle                     = new Detalle();
                    $detalle->usuario_creador_id = $usuario->id;
                    $detalle->sucursal_id        = $sucursal_id;
                    $detalle->cliente_id         = $cliente_id;
                    $detalle->producto_id        = $item['servicio_id'];
                      // $detalle->descripcion_adicional = $item['descripcion_adicional'];
                      // $detalle->numero_serie          = $item['numero_serie'];
                      // $detalle->numero_imei           = $item['numero_imei'];
                    $detalle->precio       = $item['precio'];
                    $detalle->cantidad     = $item['cantidad'];
                    $detalle->descuento    = $item['descuento'];
                    $detalle->total        = $item['total'];
                    $detalle->importe      = $item['subTotal'];
                    $detalle->fecha        = date('Y-m-d H:i:s');
                    $detalle->promocion_id = $item['promocion_id'];
                    $detalle->estado       = 'Parapagar';
                    $detalle->save();

                    //VERIFICAMOS QUE EXISTA EN ALMACEN ANTES DE CONTINUAR
                    $cantidad_almacen = $this->cantidadStockEmpresa($sucursal_id, $item['servicio_id']);

                    if ($cantidad_almacen->estado) {
                        if ($item['cantidad'] > $cantidad_almacen->data['cantidad']) {
                            DB::rollBack();
                            $data = Respuesta::error(null, 'Cantidad Solicitada ' . $item['cantidad'] . ', cantidad en almacen ' . $cantidad_almacen['cantidad'] . ' del producto ' . $servicio->descripcion);
                            return $data;
                        }
                    } else {
                        $data = $cantidad_almacen;
                        DB::rollBack();
                        return $data;
                    }

                    //AQUI LO MOVEREMOS LOS DETALLES PARA NO HACER OTRO FOR ABAJO
                    $movimiento = new Movimiento();
                    $movimiento->usuario_creador_id = $usuario->id;
                    $movimiento->sucursal_id = $sucursal_objeto->id;
                    $movimiento->producto_id = $servicio->id;
                    $movimiento->detalle_id = $detalle->id;
                    $movimiento->salida = $detalle->cantidad;
                    $movimiento->ingreso = 0;
                    $movimiento->fecha = date('Y-m-d H:i:s');
                    $movimiento->descripcion = "VENTA";
                    $movimiento->save();

                    array_push($idDetalles, $detalle->id);
                }

                // ================================== Si todo ha pasado correctamente, hacer commit ==================================
                DB::commit();

                $sucursalEmpresa = $sucursal_objeto->codigo_sucursal;

                $numeroFacturaRecibo = $this->numeroRecibo($sucursal_objeto->id);
                $numeroFacturaRecibo = ($numeroFacturaRecibo == null ? 1 : ($numeroFacturaRecibo + 1));

                // VERIFICAMOS SI EXISTE LOS DATOS SUFICINTES APRA EL MANDAO DEL CORREO
                $cliente = Cliente::find($cliente_id);

                // ESTO ES PARA LA FACTURA LA CREACION
                $facturaVerdad                      = new Factura();
                $facturaVerdad->usuario_creador_id  = Auth::user()->id;
                $facturaVerdad->cliente_id          = $cliente->id;
                $facturaVerdad->sucursal_id         = $sucursal_objeto->id;
                $facturaVerdad->fecha               = date('Y-m-d H:i:s');
                $facturaVerdad->numero_recibo       = $numeroFacturaRecibo;
                $facturaVerdad->total               = $monto_total;
                $facturaVerdad->descuento_adicional = $descuento_adicional;
                $facturaVerdad->pedido_id           = $pedido_id == 0 ? null : $pedido_id;
                $facturaVerdad->estado_pago         = ($monto_pagado == $monto_total) ? 'PAGADO' : 'DEUDA';
                $facturaVerdad->save();

                // AHORA AREMOS PARA LOS DETALLES
                Detalle::whereIn('id', $idDetalles)
                    ->update([
                        'estado' => 'Finalizado',
                        'factura_id' => $facturaVerdad->id
                    ]);

                if ($realizo_pago === "true") {
                    // PARA LA TABLA PAGOS
                    $pago                     = new Pago();
                    $pago->usuario_creador_id = $usuario->id;
                    $pago->factura_id         = $facturaVerdad->id;
                    $pago->monto              = ($monto_pagado <= $monto_total) ? $monto_pagado : $monto_total;
                    $pago->sucursal_id        = $sucursal_id;
                    $pago->cambio             = $cambio_pagado;
                    $pago->fecha              = $facturaVerdad->fecha;
                    $pago->descripcion        = 'VENTA';
                    $pago->tipo_pago          = $tipo_pago_pagado;
                    $pago->estado             = 'INGRESO';
                    $pago->save();
                }

                if($pedido_id != 0){
                    $pedido         = Pedido::find($pedido_id);
                    $pedido->estado = "VENDIDO";
                    $pedido->save();
                }

                $data = Respuesta::success(null, "Se registro con exito el recibo!");
            } catch (\Exception $e) {
                $data = Respuesta::error(null, $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            }
        } else {
            $data = Respuesta::error(null, "No existe");
        }

        return $data;
    }

    public function numeroRecibo($sucursal_id)
    {

        $numeroRecibo = Factura::where('sucursal_id', $sucursal_id)
            ->selectRaw('MAX(CAST(numero_recibo AS UNSIGNED)) as numero_recibo')
            ->pluck('numero_recibo')
            ->first();

        return $numeroRecibo;
    }

    public function listado(Request $request)
    {
        return view('factura.listado');
    }

    public function ajaxListadoFacturas(Request $request)
    {
        if ($request->ajax()) {

            $usuario = Auth::user();
            $usuario_id = Auth::user()->id;

            // if(!Auth::user()->isAdmin()){
            //     // DE AQUI ESE EL ANTIGUO
            //     $punto_venta_id = Auth::user()->punto_venta_id;
            //     $punto_venta    = PuntoVenta::find($punto_venta_id);
            //     $sucursal_id    = $punto_venta->sucursal->id;
            // }

            // DE AQUI ESE EL ANTIGUO

            // $query = Factura::select('*')
            $query = Factura::select(
                'facturas.estado',
                'facturas.nit',
                'facturas.id',
                'facturas.fecha',
                'facturas.total',
                'facturas.usuario_creador_id',
                'facturas.numero_recibo',
                'facturas.sucursal_id',
                'clientes.cedula',
                'clientes.nombres',
                'clientes.ap_paterno',
                'clientes.ap_materno',
            )
                ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id')
                // ->where('facturas.sucursal_id', $sucursal_id)
                // ->where('facturas.punto_venta_id', $punto_venta_id)
                // ->whereNull('facturas.codigo_descripcion')
                // ->whereNotNull('facturas.numero_factura')
                // ->whereNull('facturas.numero_recibo')
            ;

            if (Auth::user()->rol_id != 1) {
                $query->where('facturas.sucursal_id', Auth::user()->sucursal->id);
                // ->where('facturas.punto_venta_id', Auth::user()->punto_venta->id);
            }

            if (!is_null($request->input('buscar_nro_factura'))) {
                $numero_factura = $request->input('buscar_nro_factura');
                $query->where('facturas.numero_factura', $numero_factura);
            }

            if (!is_null($request->input('buscar_nro_cedula'))) {
                $cedula = $request->input('buscar_nro_cedula');
                $query->where('clientes.cedula', $cedula);
            }

            if (!is_null($request->input('buscar_nit'))) {
                $nit = $request->input('buscar_nit');
                $query->where('facturas.nit', $nit);
            }

            if (!is_null($request->input('buscar_fecha_inicio')) && !is_null($request->input('buscar_fecha_fin'))) {
                $fecha_ini = $request->input('buscar_fecha_inicio');
                $fecha_fin = $request->input('buscar_fecha_fin');
                $query->whereBetween('facturas.fecha', [$fecha_ini . " 00:00:00", $fecha_fin . " 23:59:59"]);
            }

            if (
                !is_null($request->input('buscar_nro_factura')) &&
                !is_null($request->input('buscar_nro_cedula')) &&
                !is_null($request->input('buscar_fecha_inicio')) &&
                !is_null($request->input('buscar_fecha_fin'))
            ) {
                $facturas = $query->limit(500)->get();
            } else {
                $facturas = $query->orderBy('facturas.id', 'desc')->limit(100)->get();
                // $facturas = $query->orderBy('facturas.id', 'desc')->with('empresa')->get();
            }

            // $urlApiServicioSiat = new UrlApiServicio();
            // $UrlVerificaFactura = $urlApiServicioSiat->getUrlVerificaFactura($this->codigo_ambiente);
            // $url_verifica_factura = $UrlVerificaFactura->url_servicio;
            // $nitEmpresa = $this->nit;

            $url_verifica_factura = null;
            $nitEmpresa = null;

            $valores = [
                'listado' => view('factura.ajaxListadoFacturas')->with(compact('facturas', 'url_verifica_factura', 'nitEmpresa'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");

        } else {
            // $data['text']   = 'No existe';
            // $data['estado'] = 'error';
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    function imprimeRecibo(Request $request, $factura_id)
    {

        $usuario                = Auth::user();
        $nitSeleccionado        = session('nit_seleccionado');
        $sucursalSeleccionado   = session('sucursal_seleccionado');
        $puntoVentaSeleccionado = session('puntoVenta_seleccionado');

        $factura = Factura::find($factura_id);

        if ($factura) {

            $pdf = PDF::loadView('factura.pdf.imprimeRecibo', compact('factura'))->setPaper('letter');

            return $pdf->stream('facturaCv.pdf');

        } else {
            throw new NotFoundHttpException();
        }

    }

    public function formularioVentaPedido(Request $request, $pedido_id){

        // dd($pedido_id);

        $servicios = Producto::select('productos.id', 'productos.nombre', 'productos.precio_venta')
                                ->join('movimientos', 'movimientos.producto_id', '=', 'productos.id')
                                ->selectRaw('SUM(movimientos.ingreso) - SUM(movimientos.salida) as stock')
                                ->groupBy('productos.id', 'productos.nombre')
                                ->get();

        $pedido = Pedido::find($pedido_id);
        $pedidos = json_decode($pedido->pedidos_productos, true);

        $cliente = $pedido->cliente;

        return view('factura.formularioVentaPedido')->with(compact('servicios', 'pedidos', 'pedido', 'cliente'));

    }

    // ********************* FUNCIONES PRIVADAS **************
    protected function cantidadStockEmpresa($sucursal_id, $id_servicio)
    {
        $movimientoModelo = new Movimiento();
        $stock = $movimientoModelo->cantidaDisponile($sucursal_id, $id_servicio);

        if ($stock > 0) {
            $valores = [
                'cantidad' => $stock,
            ];
            $data = Respuesta::success($valores, "Cantidad Existente en almacen");
        } else {
            $data = Respuesta::error(null, "Cantidad no disponible en almacen");
        }
        return $data;
    }
}

