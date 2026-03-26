<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('casilleros')) {
            Schema::table('casilleros', function (Blueprint $table) {
                if (!Schema::hasColumn('casilleros', 'area')) {
                    $table->string('area', 50)->nullable()->after('idedificio');
                }
                if (!Schema::hasColumn('casilleros', 'planta')) {
                    $table->string('planta', 20)->nullable()->after('area');
                }
                if (!Schema::hasColumn('casilleros', 'observaciones')) {
                    $table->string('observaciones', 255)->nullable()->after('estado');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('casilleros')) {
            Schema::table('casilleros', function (Blueprint $table) {
                $toDrop = [];
                if (Schema::hasColumn('casilleros', 'area')) {
                    $toDrop[] = 'area';
                }
                if (Schema::hasColumn('casilleros', 'planta')) {
                    $toDrop[] = 'planta';
                }
                if (Schema::hasColumn('casilleros', 'observaciones')) {
                    $toDrop[] = 'observaciones';
                }
                if (!empty($toDrop)) {
                    $table->dropColumn($toDrop);
                }
            });
        }
    }
};
