<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamiliasTable extends Migration
{
    public function up()
    {
        Schema::create('familias', function (Blueprint $table) {
            $table->id(); // Autoincremental ID
            $table->string('codigo');
            $table->integer('orden');
            $table->boolean('visible_TPV');
            $table->boolean('estado');
            $table->string('imagen')->nullable();
            $table->timestamps(); // Crea campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('familias');
    }
}
