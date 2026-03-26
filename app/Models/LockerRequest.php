<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockerRequest extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_casillero';

    protected $fillable = [
        'matricula',
        'idperiodo',
        'estado',
        'observaciones',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'matricula', 'matricula');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'idperiodo', 'idperiodo');
    }
}
