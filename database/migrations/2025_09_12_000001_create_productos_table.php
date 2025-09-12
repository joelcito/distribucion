<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('idproductos');
            $table->string('codigo', 45);
            $table->string('nombre', 45);
            $table->foreignId('proveedores_idproveedores')->constrained('proveedores', 'idproveedores');
            $table->decimal('precio_compra', 12, 2);
            $table->decimal('precio_venta', 12, 2);
            $table->foreignId('usuario_creador_id')->constrained('users');
            $table->foreignId('usuario_modificador_id')->constrained('users');
            $table->foreignId('usuario_eliminador_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
