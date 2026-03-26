<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alumnos') && !Schema::hasColumn('alumnos', 'grupo')) {
            Schema::table('alumnos', function (Blueprint $table) {
                $table->string('grupo', 50)->nullable()->after('cuatrimestre');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('alumnos') && Schema::hasColumn('alumnos', 'grupo')) {
            Schema::table('alumnos', function (Blueprint $table) {
                $table->dropColumn('grupo');
            });
        }
    }
};
