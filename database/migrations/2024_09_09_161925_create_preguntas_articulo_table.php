<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreguntasArticuloTable extends Migration
{
    public function up()
    {
        Schema::create('preguntas_articulo', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->integer('orden');
            $table->string('texto');
            $table->unsignedBigInteger('articulo_id'); // Llave foránea
            $table->string('tipo_pregunta');
            $table->integer('unidades_maximas')->nullable();
            $table->timestamps(); // Crea campos created_at y updated_at

            // Relación con la tabla artículos
            $table->foreign('articulo_id')->references('id')->on('articulos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('preguntas_articulo');
    }
}
