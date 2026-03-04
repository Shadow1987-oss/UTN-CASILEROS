<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Period;
use App\Models\Student;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['student', 'locker', 'period', 'usuario'])->get();

        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $students = Student::all();
        $lockers = Locker::all();
        $periods = Period::all();

        return view('assignments.create', compact('students', 'lockers', 'periods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'matricula' => ['required', 'exists:alumnos,matricula'],
            'idcasillero' => ['required', 'exists:casilleros,idcasillero'],
            'idPeriodo' => ['required', 'exists:periodos,idperiodo'],
        ]);

        $alreadyAssigned = Assignment::where('matricula', $data['matricula'])
            ->where('idPeriodo', $data['idPeriodo'])
            ->exists();

        if ($alreadyAssigned) {
            return back()->withInput()->withErrors(['matricula' => 'El estudiante ya tiene un casillero en este período.']);
        }

        $activeCount = Assignment::where('idcasillero', $data['idcasillero'])
            ->where('idPeriodo', $data['idPeriodo'])
            ->count();

        if ($activeCount >= 2) {
            return back()->withInput()->withErrors(['idcasillero' => 'El casillero ya está asignado a 2 estudiantes en este periodo.']);
        }

        Assignment::create($data + ['idusuario' => auth()->id() ?? null, 'fechaAsignacion' => now()]);

        return redirect()->route('assignments.index')->with('status', 'Asignación creada.');
    }

    public function edit(Assignment $assignment)
    {
        $students = Student::all();
        $lockers = Locker::all();
        $periods = Period::all();

        return view('assignments.edit', compact('assignment', 'students', 'lockers', 'periods'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'matricula' => ['required', 'exists:alumnos,matricula'],
            'idcasillero' => ['required', 'exists:casilleros,idcasillero'],
            'idPeriodo' => ['required', 'exists:periodos,idperiodo'],
        ]);

        $alreadyAssigned = Assignment::where('matricula', $data['matricula'])
            ->where('idPeriodo', $data['idPeriodo'])
            ->where('idasigna', '!=', $assignment->idasigna)
            ->exists();

        if ($alreadyAssigned) {
            return back()->withInput()->withErrors(['matricula' => 'El estudiante ya tiene un casillero en este período.']);
        }

        $activeCount = Assignment::where('idcasillero', $data['idcasillero'])
            ->where('idPeriodo', $data['idPeriodo'])
            ->where('idasigna', '!=', $assignment->idasigna)
            ->count();

        if ($activeCount >= 2) {
            return back()->withInput()->withErrors(['idcasillero' => 'El casillero ya está asignado a 2 estudiantes en este periodo.']);
        }

        $assignment->update($data);

        return redirect()->route('assignments.index')->with('status', 'Asignación actualizada.');
    }

    public function release(Assignment $assignment)
    {
        if ($assignment->released_at) {
            return redirect()->route('assignments.index')->with('status', 'Assignment already released.');
        }

        $assignment->update([
            'released_at' => now(),
            'status' => 'released',
        ]);

        return redirect()->route('assignments.index')->with('status', 'Assignment released.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('assignments.index')->with('status', 'Assignment deleted.');
    }
}
