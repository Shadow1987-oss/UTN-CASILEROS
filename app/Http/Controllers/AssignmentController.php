<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Period;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $usuarios = \App\Models\Usuario::all();

        return view('assignments.create', compact('students', 'lockers', 'periods', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idasigna' => ['required', 'integer', 'unique:asignamientos,idasigna'],
            'matricula' => ['required', 'exists:alumnos,matricula'],
            'idcasillero' => ['required', 'exists:casilleros,idcasillero'],
            'idPeriodo' => ['required', 'exists:periodos,idperiodo'],
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'fechaAsignacion' => ['required', 'date'],
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

        $locker = Locker::find($data['idcasillero']);
        if ($locker && $locker->estado === 'dañado') {
            return back()->withInput()->withErrors(['idcasillero' => 'No se puede asignar un casillero dañado.']);
        }

        Assignment::create(array_merge($data, [
            'status' => 'activo',
            'released_at' => null,
        ]));

        $this->syncLockerStatus((int) $data['idcasillero']);

        return redirect()->route('assignments.index')->with('status', 'Asignación creada.');
    }

    public function edit(Assignment $assignment)
    {
        $students = Student::all();
        $lockers = Locker::all();
        $periods = Period::all();
        $usuarios = \App\Models\Usuario::all();

        return view('assignments.edit', compact('assignment', 'students', 'lockers', 'periods', 'usuarios'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $previousLockerId = (int) $assignment->idcasillero;

        $data = $request->validate([
            'idasigna' => ['required', 'integer', Rule::unique('asignamientos', 'idasigna')->ignore($assignment->idasigna, 'idasigna')],
            'matricula' => ['required', 'exists:alumnos,matricula'],
            'idcasillero' => ['required', 'exists:casilleros,idcasillero'],
            'idPeriodo' => ['required', 'exists:periodos,idperiodo'],
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'fechaAsignacion' => ['required', 'date'],
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

        $locker = Locker::find($data['idcasillero']);
        if ($locker && $locker->estado === 'dañado') {
            return back()->withInput()->withErrors(['idcasillero' => 'No se puede asignar un casillero dañado.']);
        }

        $assignment->update($data);

        if (is_null($assignment->released_at) && strtolower((string) $assignment->status) === 'liberado') {
            $assignment->update(['status' => 'activo']);
        }

        $this->syncLockerStatus($previousLockerId);
        $this->syncLockerStatus((int) $assignment->idcasillero);

        return redirect()->route('assignments.index')->with('status', 'Asignación actualizada.');
    }

    public function show(Assignment $assignment)
    {
        return redirect()->route('assignments.index');
    }

    public function release(Assignment $assignment)
    {
        if ($assignment->released_at) {
            return redirect()->route('assignments.index')->with('status', 'La asignación ya fue liberada.');
        }

        $assignment->update([
            'released_at' => now(),
            'status' => 'liberado',
        ]);

        $this->syncLockerStatus((int) $assignment->idcasillero);

        return redirect()->route('assignments.index')->with('status', 'Asignación liberada.');
    }

    public function destroy(Assignment $assignment)
    {
        $lockerId = (int) $assignment->idcasillero;

        try {
            $assignment->delete();
        } catch (QueryException $exception) {
            return redirect()->route('assignments.index')->with('status', 'No se puede eliminar la asignación porque está relacionada con otros registros.');
        }

        $this->syncLockerStatus($lockerId);

        return redirect()->route('assignments.index')->with('status', 'Asignación eliminada.');
    }

    private function syncLockerStatus(int $lockerId): void
    {
        $locker = Locker::find($lockerId);

        if (!$locker || $locker->estado === 'dañado') {
            return;
        }

        $hasActiveAssignment = Assignment::where('idcasillero', $lockerId)
            ->whereNull('released_at')
            ->exists();

        $locker->update([
            'estado' => $hasActiveAssignment ? 'ocupado' : 'disponible',
        ]);
    }
}
