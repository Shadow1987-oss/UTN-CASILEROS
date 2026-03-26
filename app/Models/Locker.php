<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(Report::class);
    }
}
