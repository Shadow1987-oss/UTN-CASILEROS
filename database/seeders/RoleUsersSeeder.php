<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder de datos iniciales para desarrollo y pruebas.
 *
 * Crea:
 * - 3 usuarios de autenticación: admin, tutor y estudiante demo
 * - 1 carrera (TIC), 1 edificio, 1 período, 1 casillero
 * - 1 tutor del dominio en tabla usuarios
 * - 1 alumno demo vinculado al usuario estudiante
 * - 1 asignación activa de prueba
 *
 * Contraseña por defecto: password123
 */
class RoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@utnlockers.test'],
            [
                'name' => 'Administrador General',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'tutor@utnlockers.test'],
            [
                'name' => 'Tutor Operativo',
                'role' => 'tutor',
                'password' => Hash::make('password123'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'estudiante@utnlockers.test'],
            [
                'name' => 'Estudiante Demo',
                'role' => 'estudiante',
                'password' => Hash::make('password123'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $studentUser = DB::table('users')->where('email', 'estudiante@utnlockers.test')->first();

        DB::table('carreras')->updateOrInsert(
            ['idcarrera' => 101],
            ['nombre_carre' => 'Tecnologías de la Información']
        );

        DB::table('edificios')->updateOrInsert(
            ['idedificio' => 1],
            ['num_edific' => 'Edificio A']
        );

        DB::table('periodos')->updateOrInsert(
            ['idperiodo' => 1],
            [
                'nombrePerio' => 'Enero-Abril 2026',
                'fechaInicio' => '2026-01-01',
                'fechaFin' => '2026-04-30',
            ]
        );

        DB::table('casilleros')->updateOrInsert(
            ['idcasillero' => 1],
            [
                'idedificio' => 1,
                'numeroCasiller' => 101,
                'estado' => 'ocupado',
            ]
        );

        DB::table('usuarios')->updateOrInsert(
            ['idusuario' => 1],
            [
                'nombre' => 'Tutor',
                'apellidoP' => 'Demo',
                'apellidoM' => 'Sistema',
                'cargo' => 'Tutor',
            ]
        );

        $studentData = [
            'user_id' => $studentUser->id,
            'nombre' => 'Estudiante',
            'idcarrera' => 101,
            'cuatrimestre' => 3,
            'grupo' => 'TIC-3A',
            'apellidoPaterno' => 'Demo',
            'apellidoMaterno' => 'UTN',
        ];

        if (Schema::hasColumn('alumnos', 'numero_telefonico')) {
            $studentData['numero_telefonico'] = '3111234567';
        } elseif (Schema::hasColumn('alumnos', 'numero_telefono')) {
            $studentData['numero_telefono'] = '3111234567';
        }

        DB::table('alumnos')->updateOrInsert(
            ['matricula' => 'TIC-320072'],
            $studentData
        );

        DB::table('asignamientos')->updateOrInsert(
            ['idasigna' => 1],
            [
                'matricula' => 'TIC-320072',
                'idusuario' => 1,
                'idcasillero' => 1,
                'idPeriodo' => 1,
                'fechaAsignacion' => '2026-01-10',
                'status' => 'activo',
                'released_at' => null,
            ]
        );
    }
}
