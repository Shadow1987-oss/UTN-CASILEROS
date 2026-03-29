<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $table = 'sanciones';
    protected $primaryKey = 'idsancion';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $guarded = [];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idusuario', 'idusuario');
    }

    public function recibes()
    {
        return $this->hasMany(Receipt::class, 'idsancion', 'idsancion');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'idsancion', 'idsancion');
    }
}
