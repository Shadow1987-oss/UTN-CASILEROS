<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Period;
use App\Models\Student;

/**
 * Controlador del panel principal (Dashboard).
 *
 * Muestra contadores generales: total de alumnos, casilleros,
 * períodos, asignaciones activas, casilleros disponibles,
 * ocupados y dañados.
 */
class DashboardController extends Controller
{
    /**
     * Dashboard con estadísticas generales del sistema.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $students = Student::count();
        $lockers = Locker::count();
        $periods = Period::count();
        $active_assignments = Assignment::whereNull('released_at')->count();

        $damaged_lockers = Locker::where('estado', 'dañado')->count();

        $occupied_lockers = Assignment::whereNull('released_at')
            ->distinct('idcasillero')
            ->count('idcasillero');

        $available_lockers = max(0, ($lockers - $damaged_lockers) - $occupied_lockers);

        return view('dashboard', compact('students', 'lockers', 'periods', 'active_assignments', 'available_lockers', 'occupied_lockers', 'damaged_lockers'));
    }
}
