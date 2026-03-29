<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('asignamientos', function (Blueprint $table) {
            $table->integer('idasigna')->primary();
            $table->string('matricula', 20);
            $table->integer('idusuario')->nullable();
            $table->integer('idcasillero');
            $table->integer('idPeriodo');
            $table->date('fechaAsignacion');
            $table->timestamp('released_at')->nullable();
            $table->string('status')->default('activo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asignamientos');
    }
}
