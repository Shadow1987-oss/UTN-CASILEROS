<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'recibe';
    protected $primaryKey = 'idrecibe';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function sanction()
    {
        return $this->belongsTo(Sanction::class, 'idsancion', 'idsancion');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'matricula', 'matricula');
    }
}
