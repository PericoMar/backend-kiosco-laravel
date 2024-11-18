<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlergenosTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alergenos', function (Blueprint $table) {
            $table->id(); // Crea una columna 'id' tipo BIGINT (auto_increment por defecto).
            $table->string('nombre', 50); // Columna 'nombre' tipo VARCHAR(50).
            $table->timestamps(); // Agrega las columnas 'created_at' y 'updated_at'.
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alergenos');
    }
}
