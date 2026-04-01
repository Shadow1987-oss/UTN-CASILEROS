<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para solicitudes de casillero.
 *
 * Los estudiantes generan solicitudes que quedan en estado 'pendiente'
 * hasta que un admin/tutor las apruebe o rechace.
 *
 * Tabla: solicitudes_casillero  |  PK: id (autoincremental)
 *
 * Relaciones:
 * - student()  → belongsTo Student (por matricula)
 * - period()   → belongsTo Period  (por idperiodo)
 * - reviewer() → belongsTo User    (por reviewed_by)
 *
 * Accesor:
 * - status_label: devuelve 'Pendiente', 'Aprobada' o 'Rechazada'
 */
class LockerRequest extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_casillero';

    protected $fillable = [
        'matricula',
        'idperiodo',
        'estado',
        'observaciones',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'matricula', 'matricula');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'idperiodo', 'idperiodo');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    public function getStatusLabelAttribute(): string
    {
        $status = strtolower((string) $this->estado);

        if ($status === 'aprobada') {
            return 'Aprobada';
        }

        if ($status === 'rechazada') {
            return 'Rechazada';
        }

        return 'Pendiente';
    }
}
