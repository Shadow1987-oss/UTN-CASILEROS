<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'alumnos';
    protected $primaryKey = 'matricula';
    public $incrementing = false;
    protected $keyType = 'int';
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
        'numero_telefono',
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
        return $this->hasMany(Report::class);
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class);
    }

    public function lockerRequests()
    {
        return $this->hasMany(LockerRequest::class, 'matricula', 'matricula');
    }
}
