<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Proveedor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MigrationController extends Controller
{
    public function migrarTodo()
    {

        DB::beginTransaction();
        try{

            // ------------------------- VAMOS POR PROVEEDORE -------------------------
            $path       = public_path('migration/proveedores.xlsx');
            $data       = Excel::toArray([], $path);

            foreach ($data[0] as $index => $row) {

                if ($index == 0) continue; // saltar encabezado

                $ru           = $row[0];
                $nombres      = $row[1];

                $proveedor                     = new Proveedor();
                $proveedor->usuario_creador_id = 1;
                $proveedor->nombre             = $nombres;
                $proveedor->save();
            }

            // ------------------------- VAMOS POR CATEGORIAS -------------------------
            $path       = public_path('migration/categorias.xlsx');
            $data       = Excel::toArray([], $path);

            foreach ($data[0] as $index => $row) {

                if ($index == 0) continue; // saltar encabezado

                $ru           = $row[0];
                $nombres      = $row[1];

                $categoria                     = new Categoria();
                $categoria->usuario_creador_id = 1;
                $categoria->nombre             = $nombres;
                $categoria->save();
            }

            // ------------------------- VAMOS POR PRODUCTOS -------------------------
            $path       = public_path('migration/productosxlsx.xlsx');
            $data       = Excel::toArray([], $path);

            foreach ($data[0] as $index => $row) {

                if ($index == 0) continue; // saltar encabezado

                $ru                = $row[0];
                $nombres           = $row[1];
                $precio_compra     = $row[2];
                $precio_venta      = $row[3];
                $codigo            = $row[4];
                $cantidad          = $row[5];
                $fecha_vencimiento = $row[6];
                $lote              = $row[7];
                $provedor          = $row[8];
                $categoria         = $row[9];
                $marca             = $row[10];

                $producto                     = new Producto();
                $producto->usuario_creador_id = 1;
                $producto->proveedor_id       = $provedor;
                $producto->categoria_id       = $categoria;
                $producto->nombre             = $nombres;
                $producto->codigo             = $codigo;
                $producto->save();

                $fecha_vencimiento = trim(strtolower($row[6]));

                $meses = [
                    'ene.' => '01',
                    'feb.' => '02',
                    'mar.' => '03',
                    'abr.' => '04',
                    'may.' => '05',
                    'jun.' => '06',
                    'jul.' => '07',
                    'ago.' => '08',
                    'sep.' => '09',
                    'oct.' => '10',
                    'nov.' => '11',
                    'dic.' => '12',
                ];

                if (!empty($fecha_vencimiento)) {

                    $partes = explode('-', $fecha_vencimiento);

                    if (count($partes) == 2 && isset($meses[$partes[0]])) {

                        $fecha_vencimiento = '20' . $partes[1] . '-' . $meses[$partes[0]] . '-01';
                    } else {

                        $fecha_vencimiento = null;
                    }
                }


                if($cantidad > 0){

                    $movimiento                     = new Movimiento();
                    $movimiento->usuario_creador_id = 1;
                    $movimiento->producto_id        = $producto->id;
                    $movimiento->sucursal_id        = 1;
                    $movimiento->ingreso            = $cantidad;
                    $movimiento->salida             = 0;
                    $movimiento->fecha              = date('Y-m-d H:i:s');
                    $movimiento->precio_compra      = $precio_compra;
                    $movimiento->precio_venta       = $precio_venta;
                    $movimiento->fecha_vencimiento  = $fecha_vencimiento;
                    $movimiento->lotes              = $lote;
                    $movimiento->save();

                }

            }

            DB::commit();
            return response()->json(['res' => true, 'message' => 'Saneamiento y vinculación PEPS completada con éxito.']);

        }
        catch(Exception $e){
            DB::rollBack();
            return response()->json(['res' => false, 'message' => 'Error: ' . $e->getMessage() . ' en línea ' . $e->getLine()]);
        }
    }
}
