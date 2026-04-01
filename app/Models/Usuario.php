<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para tutores/usuarios del dominio de negocio.
 *
 * Representa a los tutores y administradores dentro del contexto
 * operativo (distinto de User que es para autenticación).
 * Se usan como responsables en asignaciones, reportes y sanciones.
 *
 * Tabla: usuarios  |  PK: idusuario (int, no autoincremental)
 *
 * Relaciones:
 * - reports()     → hasMany Report
 * - assignments() → hasMany Assignment
 * - sanctions()   → hasMany Sanction
 */
class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function reports()
    {
        return $this->hasMany(Report::class, 'idusuario', 'idusuario');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'idusuario', 'idusuario');
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class, 'idusuario', 'idusuario');
    }
}
