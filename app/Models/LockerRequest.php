<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
