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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_creador_id')->nullable()->after('id');
            $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable()->after('usuario_creador_id');
            $table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable()->after('usuario_modificador_id');
            $table->foreign('usuario_eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('sucursal_id')->nullable()->after('usuario_modificador_id');
            $table->foreign('sucursal_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('rol_id')->nullable()->after('sucursal_id');
            $table->foreign('rol_id')->references('id')->on('roles');

            $table->string('ap_paterno')->nullable()->after('password');
            $table->string('ap_materno')->nullable()->after('ap_paterno');
            $table->string('cedula')->nullable()->after('ap_materno');
            $table->string('celular')->nullable()->after('cedula');

            $table->string('estado')->nullable();
            $table->datetime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
