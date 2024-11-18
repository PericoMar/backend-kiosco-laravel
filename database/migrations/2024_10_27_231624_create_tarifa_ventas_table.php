<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifaVentaTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifa_venta', function (Blueprint $table) {
            $table->id();  // Crea la columna 'id' tipo INT con auto incremento.
            $table->decimal('precio_venta', 10, 2)->nullable(); // Columna 'precio_venta' tipo DECIMAL que puede ser NULL.
            $table->integer('tipo_tarifa_id')->nullable(); // Columna 'tipo_tarifa_id' tipo INT que puede ser NULL.
            $table->integer('articulo_id')->nullable(); // Columna 'articulo_id' tipo INT que puede ser NULL.

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
        Schema::dropIfExists('tarifa_venta');
    }
}
