<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para sanciones.
 *
 * Registra penalizaciones aplicadas por tutores.
 * Se vincula con estudiantes a través de la tabla "recibe".
 *
 * Tabla: sanciones  |  PK: idsancion (int, no autoincremental)
 *
 * Relaciones:
 * - usuario() → belongsTo Usuario (tutor que aplica la sanción)
 * - recibes()  → hasMany Receipt
 * - receipt()  → hasOne Receipt (acceso directo al único recibo)
 */
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
