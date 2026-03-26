<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Period;
use Illuminate\Console\Command;

class ReleaseExpiredAssignments extends Command
{
    protected $signature = 'assignments:release-expired';

    protected $description = 'Libera asignaciones de periodos vencidos';

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
