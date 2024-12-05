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
        Schema::create('usuarios_familias', function (Blueprint $table) {
            $table->id(); // Campo id autoincremental
            $table->unsignedBigInteger('id_usuario'); // Relación con la tabla usuarios
            $table->unsignedBigInteger('id_familia'); // Relación con la tabla familias
            $table->timestamps(); // Campos created_at y updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_familias');
    }
};
