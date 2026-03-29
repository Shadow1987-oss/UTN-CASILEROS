<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('solicitudes_casillero')) {
            Schema::create('solicitudes_casillero', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('matricula', 20);
                $table->integer('idperiodo');
                $table->string('estado', 20)->default('pendiente');
                $table->string('observaciones', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_casillero');
    }
};
