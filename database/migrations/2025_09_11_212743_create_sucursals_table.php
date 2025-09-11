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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id('idsucursales');
            $table->string('codigo_sucursal',45);
            $table->string('nombre',45);
            $table->string('direccion',45);
            $table->foreignId('usuario_creador_id')->constrained('users');
            $table->foreignId('usuario_modificador_id')->constrained('users');
            $table->foreignId('usuario_eliminador_id')->nullable()->constrained('users');
            
            
            $table->string('estado')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
