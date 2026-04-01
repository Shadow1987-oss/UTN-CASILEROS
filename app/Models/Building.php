<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para edificios.
 *
 * Cada edificio contiene múltiples casilleros.
 *
 * Tabla: edificios  |  PK: idedificio (int, no autoincremental)
 *
 * Relaciones:
 * - lockers() → hasMany Locker
 */
class Building extends Model
{
    use HasFactory;

    protected $table = 'edificios';
    protected $primaryKey = 'idedificio';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function lockers()
    {
        return $this->hasMany(Locker::class, 'idedificio', 'idedificio');
    }
}
