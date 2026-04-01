<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para reportes de incidencias.
 *
 * Un reporte documenta un incidente asociado a un tutor,
 * un estudiante y opcionalmente casilleros involucrados.
 *
 * Tabla: reportes  |  PK: idreporte (int, no autoincremental)
 *
 * Relaciones:
 * - tutor()      → belongsTo Usuario (por idusuario)
 * - student()    → belongsTo Student (por matricula)
 * - casilleros() → belongsToMany Locker (vía tabla pivote "puede")
 */
class Report extends Model
{
    use HasFactory;

    protected $table = 'reportes';
    protected $primaryKey = 'idreporte';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function tutor()
    {
        return $this->belongsTo(Usuario::class, 'idusuario', 'idusuario');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'matricula', 'matricula');
    }

    public function casilleros()
    {
        return $this->belongsToMany(Locker::class, 'puede', 'idreporte', 'idcasillero');
    }
}
