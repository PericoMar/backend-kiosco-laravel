<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpcionesPreguntasArticuloTable extends Migration
{
    public function up()
    {
        Schema::create('opciones_preguntas_articulo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pregunta_articulo_id');
            $table->integer('orden')->nullable();
            $table->unsignedBigInteger('articulo_id');
            $table->decimal('suplemento', 8, 2)->nullable(); // Manejo de suplemento como decimal
            $table->timestamps();

            // Llave foránea hacia Preguntas_Articulo
            $table->foreign('pregunta_articulo_id')->references('id')->on('preguntas_articulo')->onDelete('cascade');
            
            // Llave foránea hacia Articulos
            $table->foreign('articulo_id')->references('id')->on('articulos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('opciones_preguntas_articulo');
    }
}