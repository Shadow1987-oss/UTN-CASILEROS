<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para casilleros.
 *
 * Cada casillero pertenece a un edificio y tiene un número único.
 * Estados posibles: disponible, ocupado, dañado.
 *
 * Tabla: casilleros  |  PK: idcasillero (int, no autoincremental)
 *
 * Relaciones:
 * - assignments() → hasMany Assignment
 * - building()    → belongsTo Building
 * - reports()     → belongsToMany Report (vía tabla pivote "puede")
 */
class Locker extends Model
{
    use HasFactory;

    protected $table = 'casilleros';
    protected $primaryKey = 'idcasillero';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idcasillero',
        'idedificio',
        'area',
        'planta',
        'numeroCasiller',
        'estado',
        'observaciones',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'idcasillero', 'idcasillero');
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'idedificio', 'idedificio');
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'puede', 'idcasillero', 'idreporte');
    }
}
