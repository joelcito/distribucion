<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'clientes';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'nombres',
        'ap_paterno',
        'ap_materno',
        'cedula',
        'complemento',
        'nit',
        'razon_social',
        'correo',
        'numero_celular',
        'estado',
         'deleted_at'
    ];

    public function provincia(){
        return $this->belongsTo('App\Models\Provincia', 'provincia_id');
    }

}
