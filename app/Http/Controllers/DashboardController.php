<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Period;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        // Sin base de datos - mostrando solo datos de ejemplo
        $students = 0;
        $lockers = 0;
        $periods = 0;
        $active_assignments = 0;

        return view('dashboard', compact('students', 'lockers', 'periods', 'active_assignments'));
    }
}
