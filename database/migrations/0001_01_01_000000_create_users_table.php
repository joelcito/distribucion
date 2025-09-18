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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
           // $table->foreign('usuario_creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            //$table->foreign('usuario_modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
           // $table->foreign('usuario_eliminador_id')->references('id')->on('users');
           
            //$table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');

            $table->foreign('sucursales_id')->references('id')->on('sucursales');
            $table->unsignedBigInteger('sucursales_id')->nullable();
            $table->foreign('roles_id')->references('id')->on('roles');
            $table->unsignedBigInteger('roles_id')->nullable();
            
            $table->string('name')->nullable();;
            $table->string('ap_paterno')->nullable();
            $table->string('ap_materno')->nullable();
            $table->string('cedula')->nullable();;
            $table->string('celular')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('estado')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->timestamps();

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
