<?php

namespace App\Http\Controllers;

use App\Models\Detalle;
use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\Factura;
use Illuminate\Http\Request;

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

        $ventas = Detalle::with(['sucursal', 'cliente', 'usuario'])
            ->whereBetween('fecha', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
            ->get();

        $resultado = $ventas->map(function ($v, $index) {
            return [
                'sucursal' => $v->sucursal->nombre ?? '',
                'cliente' => $v->cliente->nombre ?? '',
                'fecha' => $v->fecha,
                'monto' => $v->total,
                'correlativo' => $index + 1,
                'usuario' => $v->usuario_creador_id ? $v->usuario->name : '',
            ];
        });

        return response()->json($resultado);
    }


}
