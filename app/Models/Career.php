<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;

    protected $table = 'carreras';
    protected $primaryKey = 'idcarrera';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function students()
    {
        return $this->hasMany(Student::class, 'idcarrera', 'idcarrera');
    }
}
