<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';
    public $incrementing = true;
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
