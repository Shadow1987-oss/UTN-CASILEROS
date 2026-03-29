<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('solicitudes_casillero')) {
            return;
        }

        Schema::table('solicitudes_casillero', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitudes_casillero', 'review_notes')) {
                $table->string('review_notes', 255)->nullable()->after('observaciones');
            }

            if (!Schema::hasColumn('solicitudes_casillero', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('review_notes');
            }

            if (!Schema::hasColumn('solicitudes_casillero', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });

        $matriculaType = $this->getColumnType('solicitudes_casillero', 'matricula');
        if ($matriculaType !== null && stripos($matriculaType, 'varchar') === false) {
            DB::statement("ALTER TABLE solicitudes_casillero MODIFY matricula VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL");
        }

        Schema::table('solicitudes_casillero', function (Blueprint $table) {
            if (!$this->hasIndex('solicitudes_casillero', 'solicitudes_estado_idx')) {
                $table->index('estado', 'solicitudes_estado_idx');
            }
            if (!$this->hasIndex('solicitudes_casillero', 'solicitudes_periodo_idx')) {
                $table->index('idperiodo', 'solicitudes_periodo_idx');
            }
            if (!$this->hasIndex('solicitudes_casillero', 'solicitudes_matricula_idx')) {
                $table->index('matricula', 'solicitudes_matricula_idx');
            }

            if (!$this->hasForeign('solicitudes_casillero', 'solicitudes_matricula_fk')) {
                $table->foreign('matricula', 'solicitudes_matricula_fk')
                    ->references('matricula')
                    ->on('alumnos')
                    ->onDelete('cascade');
            }

            if (!$this->hasForeign('solicitudes_casillero', 'solicitudes_periodo_fk')) {
                $table->foreign('idperiodo', 'solicitudes_periodo_fk')
                    ->references('idperiodo')
                    ->on('periodos')
                    ->onDelete('cascade');
            }

            if (!$this->hasForeign('solicitudes_casillero', 'solicitudes_reviewed_by_fk')) {
                $table->foreign('reviewed_by', 'solicitudes_reviewed_by_fk')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('solicitudes_casillero')) {
            return;
        }

        Schema::table('solicitudes_casillero', function (Blueprint $table) {
            if ($this->hasForeign('solicitudes_casillero', 'solicitudes_matricula_fk')) {
                $table->dropForeign('solicitudes_matricula_fk');
            }
            if ($this->hasForeign('solicitudes_casillero', 'solicitudes_periodo_fk')) {
                $table->dropForeign('solicitudes_periodo_fk');
            }
            if ($this->hasForeign('solicitudes_casillero', 'solicitudes_reviewed_by_fk')) {
                $table->dropForeign('solicitudes_reviewed_by_fk');
            }

            if ($this->hasIndex('solicitudes_casillero', 'solicitudes_estado_idx')) {
                $table->dropIndex('solicitudes_estado_idx');
            }
            if ($this->hasIndex('solicitudes_casillero', 'solicitudes_periodo_idx')) {
                $table->dropIndex('solicitudes_periodo_idx');
            }
            if ($this->hasIndex('solicitudes_casillero', 'solicitudes_matricula_idx')) {
                $table->dropIndex('solicitudes_matricula_idx');
            }

            if (Schema::hasColumn('solicitudes_casillero', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('solicitudes_casillero', 'reviewed_by')) {
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('solicitudes_casillero', 'review_notes')) {
                $table->dropColumn('review_notes');
            }
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }

    private function hasForeign(string $table, string $constraint): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.referential_constraints')
            ->where('constraint_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_name', $constraint)
            ->exists();
    }

    private function getColumnType(string $table, string $column): ?string
    {
        $database = DB::getDatabaseName();

        $record = DB::table('information_schema.columns')
            ->select('column_type')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->first();

        return $record->column_type ?? null;
    }
};
