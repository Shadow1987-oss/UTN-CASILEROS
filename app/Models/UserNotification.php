<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para notificaciones internas del sistema.
 *
 * Almacena notificaciones dirigidas a usuarios específicos
 * (asignaciones, solicitudes, etc.) con soporte de lectura.
 *
 * Tabla: user_notifications  |  PK: id (autoincremental)
 *
 * Relaciones:
 * - user() → belongsTo User
 */
class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'payload',
        'read_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
