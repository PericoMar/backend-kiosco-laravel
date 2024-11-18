<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosAlergenosTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos_alergenos', function (Blueprint $table) {
            $table->unsignedBigInteger('articulo_id'); // Columna 'articulo_id' tipo INT (unsigned).
            $table->unsignedBigInteger('alergeno_id');  // Columna 'alergeno_id' tipo INT (unsigned).
            
            // Definir las claves foráneas:
            $table->foreign('articulo_id')->references('id')->on('articulos')->onDelete('cascade');
            $table->foreign('alergeno_id')->references('id')->on('alergenos')->onDelete('cascade');
            
            // Opcionalmente, puedes añadir índices para mejorar el rendimiento
            $table->primary(['articulo_id', 'alergeno_id']); // Combinación de claves primarias
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos_alergenos');
    }
}
