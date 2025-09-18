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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('facturas_idfacturas')->nullable();
            $table->foreign('facturas_idfacturas')->references('id')->on('facturas');
            $table->unsignedBigInteger('sucursales_idsucursales')->nullable();
            $table->foreign('sucursales_idsucursales')->references('id')->on('sucursales');
           
            $table->decimal('monto', 12, 2)->default(0);
            $table->decimal('cambio', 12, 2)->default(0);
            $table->date('fecha')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('tipo_pago', 45);

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
        Schema::dropIfExists('pagos');
    }
};
