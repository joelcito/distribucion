<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $primaryKey = 'idproveedores';

    protected $fillable = [
        'nombre',
        'nit',
        'razon_social',
        'direccion',
        'celular',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at'
    ];
}
