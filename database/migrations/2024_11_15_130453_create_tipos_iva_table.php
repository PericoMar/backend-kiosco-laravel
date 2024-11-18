<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposIvaTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_iva', function (Blueprint $table) {
            $table->id();  // Crea la columna 'id' tipo INT con auto incremento.
            $table->decimal('tipo_iva', 10, 2)->nullable(); // Columna 'tipo_iva' tipo DECIMAL que puede ser NULL.
            $table->integer('iva_porcentaje')->nullable(); // Columna 'iva_porcentaje' tipo INT que puede ser NULL.
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_iva');
    }
}
