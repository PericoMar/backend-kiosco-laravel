<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();  // Crea la columna 'id' tipo INT con auto incremento.
            $table->string('articulo', 255)->nullable(); // Columna 'articulo' tipo NVARCHAR(255).
            $table->string('codigo', 100)->nullable(); // Columna 'codigo' tipo NVARCHAR(100).
            $table->unsignedBigInteger('familia_id')->nullable(); // Columna 'familia_id' tipo INT.
            $table->integer('estado')->nullable(); // Columna 'estado' tipo INT.
            $table->boolean('visible_TPV')->nullable(); // Columna 'visible_TPV' tipo BIT.
            $table->unsignedBigInteger('tipo_iva_id')->nullable(); // Columna 'tipo_iva_id' tipo INT.
            $table->text('imagen')->nullable(); // Columna 'imagen' tipo VARCHAR con longitud indefinida.
            $table->text('descripcion')->nullable(); // Columna 'descripcion' tipo NVARCHAR con longitud indefinida.

            // Aquí puedes agregar otras relaciones, como claves foráneas si las tienes.
            // Ejemplo:
            // $table->foreign('familia_id')->references('id')->on('familias')->onDelete('set null');
            // $table->foreign('tipo_iva_id')->references('id')->on('tipos_iva')->onDelete('set null');
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}


