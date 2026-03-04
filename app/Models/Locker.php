<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    use HasFactory;

    protected $table = 'casilleros';
    protected $primaryKey = 'idcasillero';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idedificio',
        'numeroCasiller',
        'estado',
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
