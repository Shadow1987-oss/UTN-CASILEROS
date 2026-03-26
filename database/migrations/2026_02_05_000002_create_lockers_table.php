<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLockersTable extends Migration
{
    public function up()
    {
        Schema::create('casilleros', function (Blueprint $table) {
            $table->integer('idcasillero')->primary();
            $table->integer('idedificio')->nullable();
            $table->integer('numeroCasiller');
            $table->string('estado', 10);
        });
    }

    public function down()
    {
        Schema::dropIfExists('casilleros');
    }
}
