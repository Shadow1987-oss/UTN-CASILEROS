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
            $query->where('matricula', $this->normalizeMatricula((string) $request->input('matricula')));
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
                $subQuery->where('descripcion', 'like', "%{$search}%")
                    ->orWhere('observaciones', 'like', "%{$search}%");

                if (ctype_digit($search)) {
                    $subQuery->orWhere('idreporte', (int) $search);
                }
            });
        }

        $reports = $query->orderBy('idreporte')->paginate(20)->withQueryString();
        $tutors = User::where('role', 'tutor')->orderBy('name')->get();
        $students = Student::orderBy('matricula')->get();
        $lockers = Locker::orderBy('numeroCasiller')->get();

        return view('reportes.index', compact('reports', 'tutors', 'students', 'lockers'));
    }

    public function create()
    {
        $tutors = User::where('role', 'tutor')->orderBy('name')->get();
        $students = Student::orderBy('matricula')->get();
        [$lockers, $lockerStudentMap] = $this->buildLockerSelectionData();

        return view('reportes.create', compact('tutors', 'students', 'lockers', 'lockerStudentMap'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idreporte' => ['required', 'integer', 'min:1', 'unique:reportes,idreporte'],
            'idusuario' => ['required', 'integer', 'min:1', Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'tutor');
            })],
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', 'exists:alumnos,matricula'],
            'descripcion' => ['required', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string', 'max:255'],
            'casilleros' => ['nullable', 'array'],
            'casilleros.*' => ['integer', 'min:1', 'exists:casilleros,idcasillero'],
        ]);

        $data['matricula'] = $this->normalizeMatricula((string) $data['matricula']) ?? $data['matricula'];

        if (!empty($data['casilleros']) && !$this->studentOwnsLockers((string) $data['matricula'], $data['casilleros'])) {
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
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            $this->syncReportLockers((int) $report->idreporte, $data['casilleros'] ?? []);
        });

        return redirect()->route('reportes.index')->with('status', 'Reporte creado.');
    }

    public function edit(Report $report)
    {
        $tutors = User::where('role', 'tutor')->orderBy('name')->get();
        $students = Student::orderBy('matricula')->get();
        $selectedLockers = $report->casilleros()->pluck('casilleros.idcasillero')->toArray();
        [$lockers, $lockerStudentMap] = $this->buildLockerSelectionData($selectedLockers, (string) $report->matricula);

        return view('reportes.edit', compact('report', 'tutors', 'students', 'lockers', 'selectedLockers', 'lockerStudentMap'));
    }

    public function show(Report $report)
    {
        $report->load(['tutor', 'student', 'casilleros.building']);
        return view('reportes.show', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $data = $request->validate([
            'idreporte' => ['required', 'integer', 'min:1', Rule::unique('reportes', 'idreporte')->ignore($report->idreporte, 'idreporte')],
            'idusuario' => ['required', 'integer', 'min:1', Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'tutor');
            })],
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', 'exists:alumnos,matricula'],
            'descripcion' => ['required', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string', 'max:255'],
            'casilleros' => ['nullable', 'array'],
            'casilleros.*' => ['integer', 'min:1', 'exists:casilleros,idcasillero'],
        ]);

        $data['matricula'] = $this->normalizeMatricula((string) $data['matricula']) ?? $data['matricula'];

        if (!empty($data['casilleros']) && !$this->studentOwnsLockers((string) $data['matricula'], $data['casilleros'])) {
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
                'observaciones' => $data['observaciones'] ?? null,
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

    private function studentOwnsLockers(string $matricula, array $lockerIds): bool
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

    private function normalizeMatricula(?string $matricula): ?string
    {
        if ($matricula === null) {
            return null;
        }

        $normalized = strtoupper(trim((string) $matricula));
        $normalized = preg_replace('/\s+/', '', $normalized);

        if ($normalized === '') {
            return null;
        }

        if (preg_match('/^([A-Z]{2,10})-?(\d{3,10})$/', $normalized, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        return $normalized;
    }

    private function buildLockerSelectionData(array $includeLockerIds = [], ?string $includeMatricula = null): array
    {
        $activeAssignments = Assignment::query()
            ->whereNull('released_at')
            ->get(['matricula', 'idcasillero']);

        $lockerStudentMap = [];

        foreach ($activeAssignments as $assignment) {
            $lockerId = (int) $assignment->idcasillero;
            $normalizedMatricula = $this->normalizeMatricula((string) $assignment->matricula) ?? strtoupper(trim((string) $assignment->matricula));

            if (!isset($lockerStudentMap[$lockerId])) {
                $lockerStudentMap[$lockerId] = [];
            }

            $lockerStudentMap[$lockerId][] = $normalizedMatricula;
        }

        $includedMatricula = $this->normalizeMatricula($includeMatricula);
        if ($includedMatricula !== null) {
            foreach ($includeLockerIds as $lockerId) {
                $lockerId = (int) $lockerId;
                if (!isset($lockerStudentMap[$lockerId])) {
                    $lockerStudentMap[$lockerId] = [];
                }
                $lockerStudentMap[$lockerId][] = $includedMatricula;
            }
        }

        $lockerStudentMap = collect($lockerStudentMap)
            ->map(function (array $matriculas) {
                return array_values(array_unique($matriculas));
            })
            ->toArray();

        $lockerIds = collect(array_keys($lockerStudentMap))
            ->merge(collect($includeLockerIds)->map(fn($id) => (int) $id))
            ->unique()
            ->values();

        $lockers = $lockerIds->isEmpty()
            ? collect()
            : Locker::with('building')
            ->whereIn('idcasillero', $lockerIds)
            ->orderBy('numeroCasiller')
            ->get();

        return [$lockers, $lockerStudentMap];
    }
}
