<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $table = 'asignamientos';
    protected $primaryKey = 'idasigna';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idasigna',
        'matricula',
        'idusuario',
        'idcasillero',
        'idPeriodo',
        'fechaAsignacion',
        'released_at',
        'status',
    ];

    protected $casts = [
        'fechaAsignacion' => 'date',
        'released_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'matricula', 'matricula');
    }

    public function locker()
    {
        return $this->belongsTo(Locker::class, 'idcasillero', 'idcasillero');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'idPeriodo', 'idperiodo');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idusuario', 'idusuario');
    }

    public function getStatusLabelAttribute(): string
    {
        $status = strtolower((string) ($this->status ?? ''));

        if ($status === 'released' || $status === 'liberado') {
            return 'Liberado';
        }

        if ($status === 'active' || $status === 'activo' || $status === '') {
            return 'Activo';
        }

        return ucfirst($status);
    }
}
