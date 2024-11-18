<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamiliasTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('familias', function (Blueprint $table) {
            $table->id();  // Crea la columna 'id' tipo INT con auto incremento.
            $table->string('codigo', 100)->nullable(); // Columna 'codigo' tipo NVARCHAR(100).
            $table->integer('orden')->nullable(); // Columna 'orden' tipo INT.
            $table->boolean('visible_TPV')->nullable(); // Columna 'visible_TPV' tipo BIT.
            $table->string('estado', 50)->nullable(); // Columna 'estado' tipo NVARCHAR(50).
            $table->string('imagen', 255)->nullable(); // Columna 'imagen' tipo VARCHAR(255).
            $table->string('descripcion', 255)->nullable(); // Columna 'descripcion' tipo NVARCHAR(255).

            // Aquí puedes agregar otras relaciones si las tienes, como claves foráneas.
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('familias');
    }
}
