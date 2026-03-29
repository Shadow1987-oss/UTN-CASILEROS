<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('carreras')) {
            Schema::create('carreras', function (Blueprint $table) {
                $table->integer('idcarrera')->primary();
                $table->string('nombre_carre', 50);
            });
        }

        if (!Schema::hasTable('edificios')) {
            Schema::create('edificios', function (Blueprint $table) {
                $table->integer('idedificio')->primary();
                $table->string('num_edific', 50);
            });
        }

        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->integer('idusuario')->primary();
                $table->string('nombre', 50);
                $table->string('apellidoP', 50)->nullable();
                $table->string('apellidoM', 50)->nullable();
                $table->string('cargo', 50)->nullable();
            });
        }

        if (!Schema::hasTable('sanciones')) {
            Schema::create('sanciones', function (Blueprint $table) {
                $table->integer('idsancion')->primary();
                $table->integer('idusuario')->nullable();
                $table->string('sancion', 50);
                $table->string('motivo', 50)->nullable();
            });
        }

        if (!Schema::hasTable('recibe')) {
            Schema::create('recibe', function (Blueprint $table) {
                $table->integer('idrecibe')->primary();
                $table->integer('idsancion');
                $table->string('matricula', 20);
            });
        }

        if (!Schema::hasTable('reportes')) {
            Schema::create('reportes', function (Blueprint $table) {
                $table->integer('idreporte')->primary();
                $table->integer('idusuario')->nullable();
                $table->string('descripcion', 50);
            });
        }

        if (!Schema::hasTable('puede')) {
            Schema::create('puede', function (Blueprint $table) {
                $table->integer('idpuede')->primary();
                $table->integer('idreporte');
                $table->integer('idcasillero');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('puede');
        Schema::dropIfExists('reportes');
        Schema::dropIfExists('recibe');
        Schema::dropIfExists('sanciones');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('edificios');
        Schema::dropIfExists('carreras');
    }
};
