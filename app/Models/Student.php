<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para estudiantes (alumnos).
 *
 * PK es la matrícula (string, ej. 'TIC-320072'). Puede vincularse
 * opcionalmente a un User de autenticación vía user_id.
 *
 * Tabla: alumnos  |  PK: matricula (string, no autoincremental)
 *
 * Relaciones:
 * - user()           → belongsTo User
 * - assignments()    → hasMany Assignment
 * - career()         → belongsTo Career
 * - reports()        → hasMany Report
 * - sanctions()      → belongsToMany Sanction (vía tabla "recibe")
 * - lockerRequests() → hasMany LockerRequest
 *
 * Accesores:
 * - matricula_display: matrícula formateada
 * - full_name: nombre completo (nombre + apellidos)
 *
 * Método estático:
 * - formatMatricula(): normaliza cualquier matrícula al formato estándar
 */
class Student extends Model
{
    use HasFactory;

    protected $table = 'alumnos';
    protected $primaryKey = 'matricula';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'matricula',
        'user_id',
        'nombre',
        'idcarrera',
        'cuatrimestre',
        'grupo',
        'apellidoPaterno',
        'apellidoMaterno',
        'numero_telefonico',
    ];

    protected $appends = [
        'matricula_display',
        'full_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'matricula', 'matricula');
    }

    public function career()
    {
        return $this->belongsTo(Career::class, 'idcarrera', 'idcarrera');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'matricula', 'matricula');
    }

    public function sanctions()
    {
        return $this->belongsToMany(Sanction::class, 'recibe', 'matricula', 'idsancion');
    }

    public function lockerRequests()
    {
        return $this->hasMany(LockerRequest::class, 'matricula', 'matricula');
    }

    public static function formatMatricula(?string $matricula): string
    {
        if ($matricula === null) {
            return '-';
        }

        $normalized = strtoupper(trim((string) $matricula));
        $normalized = preg_replace('/\s+/', '', $normalized);

        if ($normalized === '') {
            return '-';
        }

        if (preg_match('/^([A-Z]{2,10})-?(\d{3,10})$/', $normalized, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        return $normalized;
    }

    public function getMatriculaDisplayAttribute(): string
    {
        return self::formatMatricula($this->matricula);
    }

    public function getFullNameAttribute(): string
    {
        return trim(preg_replace('/\s+/', ' ', implode(' ', [
            (string) ($this->nombre ?? ''),
            (string) ($this->apellidoPaterno ?? ''),
            (string) ($this->apellidoMaterno ?? ''),
        ])));
    }
}
