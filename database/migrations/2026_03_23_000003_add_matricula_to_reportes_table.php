<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reportes') && !Schema::hasColumn('reportes', 'matricula')) {
            Schema::table('reportes', function (Blueprint $table) {
                $table->string('matricula', 20)->nullable()->after('idusuario');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reportes') && Schema::hasColumn('reportes', 'matricula')) {
            Schema::table('reportes', function (Blueprint $table) {
                $table->dropColumn('matricula');
            });
        }
    }
};
