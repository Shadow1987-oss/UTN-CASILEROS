<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para carreras universitarias.
 *
 * Catálogo de carreras al que se vinculan los alumnos.
 *
 * Tabla: carreras  |  PK: idcarrera (int, no autoincremental)
 *
 * Relaciones:
 * - students() → hasMany Student
 */
class Career extends Model
{
    use HasFactory;

    protected $table = 'carreras';
    protected $primaryKey = 'idcarrera';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idcarrera',
        'nombre_carre',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'idcarrera', 'idcarrera');
    }
}
