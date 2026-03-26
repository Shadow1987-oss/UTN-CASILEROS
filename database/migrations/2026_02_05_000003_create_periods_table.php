<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodsTable extends Migration
{
    public function up()
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->integer('idperiodo')->primary();
            $table->string('nombrePerio', 50);
            $table->date('fechaInicio')->nullable();
            $table->date('fechaFin')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('periodos');
    }
}
