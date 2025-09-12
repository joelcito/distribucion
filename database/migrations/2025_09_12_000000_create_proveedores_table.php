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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_creador_id')->constrained('users');
            $table->foreignId('usuario_modificador_id')->constrained('users');
            $table->foreignId('usuario_eliminador_id')->nullable()->constrained('users');

            $table->string('nombre', 45);
            $table->string('nit', 45);
            $table->string('razon_social', 45);
            $table->string('direccion', 45);
            $table->string('celular', 45);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
