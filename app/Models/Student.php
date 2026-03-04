<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'alumnos';
    protected $primaryKey = 'matricula';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'idcarrera',
        'cuatrimestre',
        'apellidoPaterno',
        'apellidoMaterno',
        'numero_telefono',
    ];

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
        return $this->hasMany(Report::class);
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class);
    }
}
