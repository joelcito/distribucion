<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'facturas';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'cliente_id',
        'sucursal_id',
        'fecha',
        'nit',
        'razon_social',
        'numero_recibo',
        'total',
        'descuento_adicional',
        'descripcion',

        'estado_pago',

        'estado',
        'deleted_at'
    ];

    public function usuarioCreador(){
        return $this->belongsTo('App\Models\User', 'usuario_creador_id');
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursal', 'sucursal_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function detalles(){
        return $this->hasMany(Detalle::class);
    }
}
