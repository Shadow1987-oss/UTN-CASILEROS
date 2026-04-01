<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para períodos académicos.
 *
 * Define rangos de fecha durante los cuales se realizan asignaciones.
 *
 * Tabla: periodos  |  PK: idperiodo (int, no autoincremental)
 *
 * Relaciones:
 * - assignments() → hasMany Assignment
 */
class Period extends Model
{
    use HasFactory;

    protected $table = 'periodos';
    protected $primaryKey = 'idperiodo';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idperiodo',
        'nombrePerio',
        'fechaInicio',
        'fechaFin',
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFin' => 'date',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'idPeriodo', 'idperiodo');
    }
}
