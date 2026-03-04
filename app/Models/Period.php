<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $table = 'periodos';
    protected $primaryKey = 'idperiodo';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nombrePerio',
        'fechaInicio',
        'fechaFin',
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFin' => 'date',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'idPeriodo', 'idperiodo');
    }
}
