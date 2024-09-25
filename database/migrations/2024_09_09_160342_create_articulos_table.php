<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->string('articulo');
            $table->string('codigo');
            $table->unsignedBigInteger('familia_id');
            $table->boolean('estado');
            $table->boolean('visible_TPV');
            $table->unsignedBigInteger('tipo_iva_id');
            $table->string('imagen')->nullable();
            $table->timestamps(); // Crea campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
