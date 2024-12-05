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
        Schema::create('datafonos', function (Blueprint $table) {
            $table->id(); // Campo id autoincremental
            $table->string('nombre'); // Nombre del datáfono
            $table->string('num_serie')->unique(); // Número de serie único
            $table->string('TID'); // Terminal ID
            $table->boolean('estado')->default(true); // Estado del datáfono (true: activo, false: inactivo)
            $table->text('descripcion')->nullable(); // Descripción opcional
            $table->string('supervisor')->nullable(); // Supervisor del datáfono
            $table->boolean('devoluciones')->default(false); // Si permite devoluciones (true: sí, false: no)
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datafonos');
    }
};
