<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alumnos') && $this->isNumericColumn('alumnos', 'matricula')) {
            DB::statement("ALTER TABLE alumnos MODIFY matricula VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL");
        }

        if (Schema::hasTable('asignamientos') && $this->isNumericColumn('asignamientos', 'matricula')) {
            DB::statement("ALTER TABLE asignamientos MODIFY matricula VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL");
        }

        if (Schema::hasTable('reportes') && Schema::hasColumn('reportes', 'matricula') && $this->isNumericColumn('reportes', 'matricula')) {
            DB::statement("ALTER TABLE reportes MODIFY matricula VARCHAR(20) COLLATE utf8mb4_unicode_ci NULL");
        }

        if (Schema::hasTable('recibe') && $this->isNumericColumn('recibe', 'matricula')) {
            DB::statement("ALTER TABLE recibe MODIFY matricula VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL");
        }

        if (Schema::hasTable('solicitudes_casillero') && $this->isNumericColumn('solicitudes_casillero', 'matricula')) {
            DB::statement("ALTER TABLE solicitudes_casillero MODIFY matricula VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL");
        }
    }

    public function down(): void {}

    private function isNumericColumn(string $table, string $column): bool
    {
        $database = DB::getDatabaseName();

        $record = DB::table('information_schema.columns')
            ->select('column_type')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->first();

        if (!$record || empty($record->column_type)) {
            return false;
        }

        $columnType = strtolower((string) $record->column_type);

        return strpos($columnType, 'int') !== false;
    }
};
