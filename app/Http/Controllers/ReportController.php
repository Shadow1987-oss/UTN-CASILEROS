<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['tutor', 'student', 'casilleros']);

        if ($request->filled('tutor_id')) {
            $query->where('idusuario', (int) $request->input('tutor_id'));
        }

        if ($request->filled('matricula')) {
            $query->where('matricula', (int) $request->input('matricula'));
        }

        if ($request->filled('idcasillero')) {
            $lockerId = (int) $request->input('idcasillero');
            $query->whereHas('casilleros', function ($relation) use ($lockerId) {
                $relation->where('casilleros.idcasillero', $lockerId);
            });
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('descripcion', 'like', "%{$search}%");

                if (ctype_digit($search)) {
                    $subQuery->orWhere('idreporte', (int) $search);
                }
            });
        }

        $reports = $query->orderBy('idreporte')->get();
        $tutors = User::where('role', 'tutor')->orderBy('name')->get();
        $students = Student::orderBy('matricula')->get();
        $lockers = Locker::orderBy('numeroCasiller')->get();

        return view('reportes.index', compact('reports', 'tutors', 'students', 'lockers'));
    }

    public function create()
    {
        $tutors = User::where('role', 'tutor')->orderBy('name')->get();
        $students = Student::orderBy('matricula')->get();
        $lockers = Locker::orderBy('numeroCasiller')->get();
        return view('reportes.create', compact('tutors', 'students', 'lockers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idreporte' => ['required', 'integer', 'unique:reportes,idreporte'],
            'idusuario' => ['required', 'integer', Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'tutor');
            })],
            'matricula' => ['required', 'integer', 'exists:alumnos,matricula'],
            'descripcion' => ['required', 'string', 'max:50'],
            'casilleros' => ['required', 'array', 'min:1'],
            'casilleros.*' => ['integer', 'exists:casilleros,idcasillero'],
        ]);

        if (!$this->studentOwnsLockers((int) $data['matricula'], $data['casilleros'])) {
            return back()->withInput()->withErrors([
                'casilleros' => 'Solo puedes reportar casilleros activos del alumno seleccionado.',
            ]);
        }

        DB::transaction(function () use ($data) {
            $report = Report::create([
                'idreporte' => $data['idreporte'],
                'idusuario' => $data['idusuario'],
                'matricula' => $data['matricula'],
                'descripcion' => $data['descripcion'],
            ]);

            $this->syncReportLockers((int) $report->idreporte, $data['casilleros'] ?? []);
        });

        return redirect()->route('reportes.index')->with('status', 'Reporte creado.');
    }

    public function edit(Report $report)
    {
        $tutors = User::where('role', 'tutor')->orderBy('name')->get();
        $students = Student::orderBy('matricula')->get();
        $lockers = Locker::orderBy('numeroCasiller')->get();
        $selectedLockers = $report->casilleros()->pluck('casilleros.idcasillero')->toArray();

        return view('reportes.edit', compact('report', 'tutors', 'students', 'lockers', 'selectedLockers'));
    }

    public function show(Report $report)
    {
        $report->load(['tutor', 'student', 'casilleros.building']);
        return view('reportes.show', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $data = $request->validate([
            'idreporte' => ['required', 'integer', Rule::unique('reportes', 'idreporte')->ignore($report->idreporte, 'idreporte')],
            'idusuario' => ['required', 'integer', Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'tutor');
            })],
            'matricula' => ['required', 'integer', 'exists:alumnos,matricula'],
            'descripcion' => ['required', 'string', 'max:50'],
            'casilleros' => ['required', 'array', 'min:1'],
            'casilleros.*' => ['integer', 'exists:casilleros,idcasillero'],
        ]);

        if (!$this->studentOwnsLockers((int) $data['matricula'], $data['casilleros'])) {
            return back()->withInput()->withErrors([
                'casilleros' => 'Solo puedes reportar casilleros activos del alumno seleccionado.',
            ]);
        }

        DB::transaction(function () use ($report, $data) {
            $report->update([
                'idreporte' => $data['idreporte'],
                'idusuario' => $data['idusuario'],
                'matricula' => $data['matricula'],
                'descripcion' => $data['descripcion'],
            ]);

            $this->syncReportLockers((int) $report->idreporte, $data['casilleros'] ?? []);
        });

        return redirect()->route('reportes.index')->with('status', 'Reporte actualizado.');
    }

    public function destroy(Report $report)
    {
        DB::table('puede')->where('idreporte', $report->idreporte)->delete();
        $report->delete();
        return redirect()->route('reportes.index')->with('status', 'Reporte eliminado.');
    }

    private function syncReportLockers(int $reportId, array $lockerIds): void
    {
        DB::table('puede')->where('idreporte', $reportId)->delete();

        $lockerIds = collect($lockerIds)
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        if ($lockerIds->isEmpty()) {
            return;
        }

        $nextId = ((int) DB::table('puede')->max('idpuede')) + 1;

        $rows = $lockerIds->map(function (int $lockerId, int $index) use ($reportId, $nextId) {
            return [
                'idpuede' => $nextId + $index,
                'idreporte' => $reportId,
                'idcasillero' => $lockerId,
            ];
        })->all();

        DB::table('puede')->insert($rows);
    }

    private function studentOwnsLockers(int $matricula, array $lockerIds): bool
    {
        $lockerIds = collect($lockerIds)
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        if ($lockerIds->isEmpty()) {
            return false;
        }

        $validCount = Assignment::where('matricula', $matricula)
            ->whereNull('released_at')
            ->whereIn('idcasillero', $lockerIds)
            ->distinct('idcasillero')
            ->count('idcasillero');

        return $validCount === $lockerIds->count();
    }
}
