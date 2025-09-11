<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    /** @use HasFactory<\Database\Factories\SucursalFactory> */
    use HasFactory, SoftDeletes;

    protected $table="sucursal";

    protected $fillable = [ 'usuario_creador_id',
                            'usuario_modificador_id',
                            'usuario_eliminador_id',
                            'codigo_sucursal',
                            'nombre',
                            'direccion',
                            'estado',
                            'deleted_at' 
                        ];


}
