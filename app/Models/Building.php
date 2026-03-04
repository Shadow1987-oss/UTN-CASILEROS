<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $table = 'edificios';
    protected $primaryKey = 'idedificio';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function lockers()
    {
        return $this->hasMany(Locker::class, 'idedificio', 'idedificio');
    }
}
