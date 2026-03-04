<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $table = 'asignaciones';
    protected $primaryKey = 'idasigna';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'matricula',
        'idusuario',
        'idcasillero',
        'idPeriodo',
        'fechaAsignacion',
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
}
