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
        Schema::create('impresoras', function (Blueprint $table) {
            $table->id(); // Campo id autoincremental
            $table->string('nombre'); // Nombre de la impresora
            $table->string('impresora_ip'); // Nombre o dirección IP de la impresora
            $table->boolean('estado')->default(true); // Estado de la impresora (true: activo, false: inactivo)
            $table->text('descripcion')->nullable(); // Descripción opcional de la impresora
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impresoras');
    }
};
