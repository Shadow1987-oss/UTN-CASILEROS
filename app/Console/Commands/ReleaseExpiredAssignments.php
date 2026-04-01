<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Period;
use Illuminate\Console\Command;

/**
 * Comando Artisan para liberar automáticamente asignaciones de períodos vencidos.
 *
 * Busca períodos cuya fechaFin ya pasó y marca sus asignaciones
 * activas como liberadas (released_at = now, status = 'liberado').
 * También sincroniza el estado de los casilleros afectados.
 *
 * Ejecución: php artisan assignments:release-expired
 * Programación: diaria (vía Console\Kernel schedule)
 */
class ReleaseExpiredAssignments extends Command
{
    /** @var string  Firma del comando */
    protected $signature = 'assignments:release-expired';

    /** @var string  Descripción mostrada en artisan list */
    protected $description = 'Libera asignaciones de periodos vencidos';

    /**
     * Ejecuta el comando.
     *
     * 1. Obtiene IDs de períodos vencidos.
     * 2. Registra los casilleros afectados antes de la actualización.
     * 3. Actualiza masivamente las asignaciones.
     * 4. Sincroniza el estado de cada casillero afectado.
     *
     * @return void
     */
    public function handle()
    {
        $expiredPeriods = Period::where('fechaFin', '<', now()->toDateString())->pluck('idperiodo');

        $affectedLockerIds = Assignment::whereIn('idPeriodo', $expiredPeriods)
            ->whereNull('released_at')
            ->pluck('idcasillero')
            ->unique()
            ->values();

        $assignments = Assignment::whereIn('idPeriodo', $expiredPeriods)
            ->whereNull('released_at')
            ->update([
                'released_at' => now(),
                'status' => 'liberado',
            ]);

        foreach ($affectedLockerIds as $lockerId) {
            $this->syncLockerStatus((int) $lockerId);
        }

        $this->info("Asignaciones liberadas: $assignments.");
    }

    /**
     * Sincroniza el estado de un casillero según sus asignaciones activas.
     *
     * @param  int  $lockerId
     * @return void
     */
    private function syncLockerStatus(int $lockerId): void
    {
        $locker = Locker::find($lockerId);

        if (!$locker || $locker->estado === 'dañado') {
            return;
        }

        $hasActiveAssignment = Assignment::where('idcasillero', $lockerId)
            ->whereNull('released_at')
            ->exists();

        $locker->update([
            'estado' => $hasActiveAssignment ? 'ocupado' : 'disponible',
        ]);
    }
}
