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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id(); // Campo id autoincremental
            $table->string('usuario')->unique(); // Nombre de usuario único
            $table->string('contraseña'); // Contraseña encriptada
            $table->string('nombre'); // Nombre del usuario
            $table->enum('rol', ['admin', 'usuario', 'moderador'])->default('usuario'); // Rol del usuario con valores predeterminados
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
