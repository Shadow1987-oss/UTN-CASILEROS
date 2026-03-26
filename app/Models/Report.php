<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reportes';
    protected $primaryKey = 'idreporte';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'idusuario', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'matricula', 'matricula');
    }

    public function casilleros()
    {
        return $this->belongsToMany(Locker::class, 'puede', 'idreporte', 'idcasillero');
    }
}
