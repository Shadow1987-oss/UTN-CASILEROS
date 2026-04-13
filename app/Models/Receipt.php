<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para recibos de sanciones.
 *
 * Vincula una sanción con un estudiante, registrando
 * que el alumno recibió dicha penalización.
 *
 * Tabla: recibe  |  PK: idrecibe (int, no autoincremental)
 *
 * Relaciones:
 * - sanction() → belongsTo Sanction
 * - student()  → belongsTo Student (por matricula)
 */
class Receipt extends Model
{
    use HasFactory;

    protected $table = 'recibe';
    protected $primaryKey = 'idrecibe';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idrecibe',
        'idsancion',
        'matricula',
    ];

    public function sanction()
    {
        return $this->belongsTo(Sanction::class, 'idsancion', 'idsancion');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'matricula', 'matricula');
    }
}
