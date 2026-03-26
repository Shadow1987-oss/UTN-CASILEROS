<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->integer('matricula')->primary();
            $table->string('nombre', 50);
            $table->integer('idcarrera')->nullable();
            $table->integer('cuatrimestre')->nullable();
            $table->string('apellidoPaterno', 50)->nullable();
            $table->string('apellidoMaterno', 50)->nullable();
            $table->string('numero_telefonico', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alumnos');
    }
}
