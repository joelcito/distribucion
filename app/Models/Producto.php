<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';
    // protected $primaryKey = 'idproductos';

    protected $fillable = [
        'codigo',
        'nombre',
        'proveedor_id',
        'precio_compra',
        'precio_venta',
        'imagenes',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at'
    ];

    protected $casts = [
        'imagenes' => 'array',
    ];
    public function proveedor()
    {
        return $this->belongsTo('App\Models\Proveedor', 'proveedor_id');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\Categoria', 'categoria_id');
    }

    public function productosDsoponibles($producto_id, $sucursal_id)
    {

        $query = $this->select(
            'productos.id as producto_id',
            'productos.nombre as nombre_producto',
            'categorias.nombre as nombre_categoria',
            'm.id as movimiento_id',
            'm.precio_venta',
            'm.fecha_vencimiento',
            'm.lotes',
            'm.sucursal_id',
            'm.ingreso as total_ingreso',
            DB::raw('(SELECT IFNULL(SUM(s.salida), 0)
                                        FROM movimientos s
                                        WHERE s.movimiento_id = m.id) AS total_salida'),
            DB::raw('(m.ingreso -
                                        (SELECT IFNULL(SUM(s.salida), 0)
                                        FROM movimientos s
                                        WHERE s.movimiento_id = m.id)
                                        ) AS stock')
        )
            ->join('movimientos as m', 'm.producto_id', '=', 'productos.id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->whereNull('m.movimiento_id')
            ->whereNull('productos.deleted_at')
            ->whereNull('categorias.deleted_at')
            ->whereRaw('
                                (m.ingreso - (
                                    SELECT IFNULL(SUM(s.salida),0)
                                    FROM movimientos s
                                    WHERE s.movimiento_id = m.id
                                )) > 0
                            ')
            ->orderBy('productos.id')
            ->orderBy('m.fecha_vencimiento');
        if ($producto_id) {
            $query->where('productos.id', $producto_id);
        }

        if ($sucursal_id) {
            $query->where('m.sucursal_id', $sucursal_id);
        }

        // dd($query->toSql());

        return $query->get();

    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'producto_id');
    }


    public function ultimoMovimiento()
    {
        return $this->hasOne(Movimiento::class, 'producto_id')->latest('created_at');
    }

    public function stock()
    {
        // Suma de ingresos menos salidas
        return $this->movimientos()->sum('ingreso') - $this->movimientos()->sum('salida');
    }


}
