<?php

namespace App\Http\Controllers;

use App\Models\Detalle;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\Factura;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Auth;
use DB;
use PDF;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Utils\Respuesta;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportesController extends Controller
{
    // REPORTE DE STOCK
    public function reporteStock()
    {
        $productos = Producto::with('sucursal')->get();
        return view('reportes.reporteStock', compact('productos'));
    }

    // REPORTE DE MOVIMIENTOS
    public function reporteMovimiento()
    {
        $movimientos = Movimiento::with('producto', 'sucursalOrigen', 'sucursalDestino')->get();
        return view('reportes.reporteMovimiento', compact('movimientos'));
    }

    // REPORTE DE VENTAS
    public function reporteVenta()
    {
        $ventas = Factura::with('cliente', 'detalleFactura.producto')->get();
        return view('reportes.reporteVenta', compact('ventas'));
    }
    public function vistaVentas()
    {
        return view('reportes.ventas');
    }

    public function buscarVentas(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        $ventas = Pago::with(['factura.cliente', 'sucursal', 'usuario'])
            ->whereBetween('fecha', [$fecha_inicio, $fecha_fin])
            ->get();

        $resultado = $ventas->map(function ($v, $index) {
            return [
                'sucursal' => $v->sucursal->nombre ?? '',
                'cliente' => $v->cliente->nombre ?? '',
                'fecha' => $v->fecha,
                'monto' => $v->monto_total ?? 0,
                'correlativo' => $index + 1,
                'usuario' => $v->usuarioCreador->name ?? '',
            ];
        });

        return response()->json([
            'estado' => 'success',
            'data' => $resultado
        ]);
    }

    public function imprimeReporteVentas($fecha_inicio, $fecha_fin)
    {
        $ventas = Pago::with(['factura.cliente', 'sucursal', 'usuario'])
            ->whereBetween('fecha', [$fecha_inicio, $fecha_fin])
            ->get();

        $pdf = PDF::loadView('reportes.pdf.pagosPdf', compact('pagos', 'fecha_inicio', 'fecha_fin'))
            ->setPaper('letter');

        return $pdf->stream("reporte_pagos_{$fecha_inicio}_{$fecha_fin}.pdf");
    }



    public function vistaMovimientos()
    {
        $productos = Producto::all();
        return view('reportes.movimientos', compact('productos'));
    }

    public function listarMovimientos(Request $request)
    {
        $fecha = $request->fecha;
        $producto = $request->producto;

        $movimientos = Movimiento::with('sucursal')
            ->whereDate('created_at', $fecha)
            ->where('producto_id', $producto)
            ->get();

        return response()->json($movimientos);
    }
    public function pdfMovimientos($fecha, $producto)
    {
        $movimientos = Movimiento::with('sucursal')
            ->whereDate('created_at', $fecha)
            ->where('producto_id', $producto)
            ->get();

        $productoNombre = Producto::find($producto)->nombre;

        $pdf = \PDF::loadView('reportes.pdf.movimientosPdf', compact('movimientos', 'fecha', 'productoNombre'))
            ->setPaper('letter');

        return $pdf->stream("movimientos_$fecha.pdf");
    }



    public function buscarMovimientos(Request $request)
    {
        $fecha = $request->input('fecha');
        $producto_id = $request->input('producto_id');

        $movimientos = Movimiento::with('producto', 'sucursal', 'usuarioCreador')
            ->when($fecha, function ($query, $fecha) {
                $query->whereDate('fecha', $fecha);
            })
            ->when($producto_id, function ($query, $producto_id) {
                $query->where('producto_id', $producto_id);
            })
            ->get();

        $resultado = $movimientos->map(function ($m, $index) {
            return [
                'producto' => $m->producto->nombre ?? '',
                'cantidad' => $m->ingreso ?? $m->salida ?? 0,
                'tipo' => $m->ingreso > 0 ? 'Ingreso' : 'Salida',
                'fecha' => $m->fecha,
                'descripcion' => $m->descripcion,
                'sucursal' => $m->sucursal->nombre ?? '',
                'usuario' => $m->usuarioCreador->name ?? '',
            ];
        });

        return response()->json(['estado' => 'success', 'data' => $resultado]);
    }

    // PDF
    public function imprimeMovimientosPorFechaYProducto($fecha, $producto_id)
    {
        $movimientos = Movimiento::with('producto', 'sucursal')
            ->whereDate('fecha', $fecha)
            ->where('producto_id', $producto_id)
            ->get();

        $pdf = PDF::loadView('factura.pdf.imprimeReporteMovimientos', compact('movimientos', 'fecha'))
            ->setPaper('letter');

        return $pdf->stream("movimientos_{$fecha}_producto_{$producto_id}.pdf");
    }

    //stock
    public function vistaStock()
    {
        $productos = Producto::all();
        $sucursales = Sucursal::all();
        return view('reportes.stock', compact('productos', 'sucursales'));
    }

    public function listarStock(Request $request)
    {
        $fecha = $request->input('fecha');

        $productos = Producto::all();
        $sucursales = Sucursal::all();

        $data = [];

        foreach ($productos as $producto) {
            $row = ['producto' => $producto->nombre];

            foreach ($sucursales as $sucursal) {
                $cantidad = Movimiento::where('producto_id', $producto->id)
                    ->where('sucursal_id', $sucursal->id)
                    ->whereDate('fecha', '<=', $fecha)
                    ->sum(DB::raw('ingreso - salida'));

                $row['sucursales'][$sucursal->id] = $cantidad;
            }

            $data[] = $row;
        }

        return response()->json(['productos' => $data, 'sucursales' => $sucursales]);
    }

    // PDF
    public function pdfStock($fecha)
    {
        $usuario = Auth::user();
        $productos = Producto::all();
        $sucursales = Sucursal::all();

        $data = [];
        foreach ($productos as $producto) {
            $row = ['producto' => $producto->nombre];

            foreach ($sucursales as $sucursal) {
                $cantidad = Movimiento::where('producto_id', $producto->id)
                    ->where('sucursal_id', $sucursal->id)
                    ->whereDate('fecha', '<=', $fecha)
                    ->sum(DB::raw('ingreso - salida'));

                $row['sucursales'][$sucursal->id] = $cantidad;
            }

            $data[] = $row;
        }

        $pdf = PDF::loadView('factura.pdf.stockPdf', compact('data', 'sucursales', 'fecha'))
            ->setPaper('letter');

        return $pdf->stream("stock_{$fecha}.pdf");
    }


}
