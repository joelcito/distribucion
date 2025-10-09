<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'cliente_id',
        'usuario_id',
        'provincia_id',
        'pedidos_productos',
        'fecha',
        'tipo',
        'estado',
        'deleted_at',
    ];

    protected $casts = [
        'pedidos_productos' => 'array',
        'fecha' => 'datetime',
    ];

    // Relaciones (opcional)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}
