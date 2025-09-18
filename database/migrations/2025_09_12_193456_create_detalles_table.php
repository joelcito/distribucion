<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('facturas_idfacturas')->nullable();
            $table->foreign('facturas_idfacturas')->references('id')->on('facturas');
            $table->unsignedBigInteger('clientes_id')->nullable();
            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->unsignedBigInteger('productos_idproductos')->nullable();
            $table->foreign('productos_idproductos')->references('id')->on('productos');
            $table->unsignedBigInteger('sucursales_idsucursales')->nullable();
            $table->foreign('sucursales_idsucursales')->references('id')->on('sucursales');
            
            
            $table->decimal('precio', 12, 2)->default(0);
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('importe', 12, 2)->default(0);
            $table->date('fecha')->nullable();

            $table->string('estado')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles');
    }
};
