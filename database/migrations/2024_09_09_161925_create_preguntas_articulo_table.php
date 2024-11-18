<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreguntasArticuloTable extends Migration
{
    public function up()
    {
        Schema::create('preguntas_articulo', function (Blueprint $table) {
            $table->id();  // Crea la columna 'id' tipo INT con auto incremento.
            $table->integer('orden')->nullable(); // Columna 'orden' tipo INT que puede ser NULL.
            $table->string('texto', 255)->nullable(); // Columna 'texto' tipo NVARCHAR(255) que puede ser NULL.
            $table->integer('articulo_id')->nullable(); // Columna 'articulo_id' tipo INT que puede ser NULL.
            $table->string('tipo_pregunta', 50)->nullable(); // Columna 'tipo_pregunta' tipo NVARCHAR(50) que puede ser NULL.
            $table->integer('unidades_maximas')->nullable(); // Columna 'unidades_maximas' tipo INT que puede ser NULL.
            $table->integer('unidades_minimas')->nullable(); // Columna 'unidades_minimas' tipo INT que puede ser NULL.
            $table->boolean('estado')->nullable(); // Columna 'estado' tipo BIT que puede ser NULL.
            $table->string('descripcion', 255)->nullable(); // Columna 'descripcion' tipo VARCHAR(255) que puede ser NULL.

            // Relación con la tabla artículos
            $table->foreign('articulo_id')->references('id')->on('articulos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('preguntas_articulo');
    }
}
