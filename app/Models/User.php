<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo de autenticación de usuarios.
 *
 * Gestiona las cuentas de acceso al sistema. El campo 'role' determina
 * los permisos: 'admin', 'tutor' o 'estudiante'.
 *
 * Tabla: users  |  PK: id (autoincremental)
 *
 * Relaciones:
 * - student() → hasOne Student (vinculación con tabla alumnos vía user_id)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'id');
    }

    /**
     * Verifica si el usuario tiene alguno de los roles proporcionados.
     *
     * @param  string  ...$roles  Roles a verificar (ej. 'admin', 'tutor')
     * @return bool
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
