<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';
    // protected $primaryKey = 'idproductos';

    protected $fillable = [
        'codigo',
        'nombre',
        'proveedores_idproveedores',
        'precio_compra',
        'precio_venta',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at'
    ];

    // public function proveedor()
    // {
    //     return $this->belongsTo(Proveedor::class, 'proveedores_idproveedores', 'idproveedores');
    // }
}
