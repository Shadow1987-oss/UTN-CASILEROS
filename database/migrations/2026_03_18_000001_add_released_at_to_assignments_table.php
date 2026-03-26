<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('asignamientos')) {
            Schema::table('asignamientos', function (Blueprint $table) {
                if (!Schema::hasColumn('asignamientos', 'released_at')) {
                    $table->timestamp('released_at')->nullable();
                }
                if (!Schema::hasColumn('asignamientos', 'status')) {
                    $table->string('status')->default('activo');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('asignamientos')) {
            Schema::table('asignamientos', function (Blueprint $table) {
                $toDrop = [];
                if (Schema::hasColumn('asignamientos', 'released_at')) {
                    $toDrop[] = 'released_at';
                }
                if (Schema::hasColumn('asignamientos', 'status')) {
                    $toDrop[] = 'status';
                }
                if (!empty($toDrop)) {
                    $table->dropColumn($toDrop);
                }
            });
        }
    }
};
