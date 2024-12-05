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
        Schema::create('Usuarios_Kioscos', function (Blueprint $table) {
            $table->id(); // Campo id autoincremental
            $table->unsignedBigInteger('id_usuario'); // Relación con la tabla usuarios
            $table->unsignedBigInteger('id_kiosco'); // Relación con la tabla kioscos
            $table->timestamps(); // Campos created_at y updated_at

            // Claves foráneas
            $table->foreign('id_usuario')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_kiosco')->references('id')->on('kioscos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_kioscos');
    }
};
