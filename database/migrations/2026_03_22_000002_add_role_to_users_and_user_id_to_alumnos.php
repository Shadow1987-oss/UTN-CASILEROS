<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('admin')->after('email');
            }
        });

        if (Schema::hasTable('alumnos')) {
            Schema::table('alumnos', function (Blueprint $table) {
                if (!Schema::hasColumn('alumnos', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->unique()->after('matricula');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });

        if (Schema::hasTable('alumnos')) {
            Schema::table('alumnos', function (Blueprint $table) {
                if (Schema::hasColumn('alumnos', 'user_id')) {
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
