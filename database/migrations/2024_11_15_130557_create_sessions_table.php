<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255); // id como nvarchar(255)
            $table->text('payload'); // payload como nvarchar(max), se usa text para este caso
            $table->integer('last_activity'); // last_activity como int
            $table->integer('user_id')->nullable(); // user_id como int, nullable
            $table->integer('ip_address')->nullable(); // ip_address como int, nullable
            $table->integer('user_agent')->nullable(); // user_agent como int, nullable
            $table->primary('id'); // Definir 'id' como clave primaria
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
