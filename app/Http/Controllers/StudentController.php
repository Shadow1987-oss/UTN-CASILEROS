<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\LockerRequest;
use App\Models\Period;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('career');
        $searchError = null;

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            if (!ctype_digit($search)) {
                $searchError = 'Ingresa solo el número de matrícula (ej. 320072).';
                $query->whereRaw('1 = 0');
            } else {
                $query->where('matricula', $search);
            }
        }

        if ($request->filled('idcarrera')) {
            $query->where('idcarrera', (int) $request->idcarrera);
        }

        if ($request->filled('cuatrimestre')) {
            $query->where('cuatrimestre', (int) $request->cuatrimestre);
        }

        if ($request->filled('grupo')) {
            $query->where('grupo', $request->grupo);
        }

        if ($request->filled('idperiodo')) {
            $periodId = (int) $request->idperiodo;
            $query->whereHas('assignments', function ($assignmentQuery) use ($periodId) {
                $assignmentQuery->where('idPeriodo', $periodId)->whereNull('released_at');
            });
        }

        if ($request->filled('idedificio')) {
            $buildingId = (int) $request->idedificio;
            $query->whereHas('assignments', function ($assignmentQuery) use ($buildingId) {
                $assignmentQuery->whereNull('released_at')
                    ->whereHas('locker', function ($lockerQuery) use ($buildingId) {
                        $lockerQuery->where('idedificio', $buildingId);
                    });
            });
        }

        $students = $query->get();
        $careers = Career::orderBy('nombre_carre')->get();
        $periods = Period::orderBy('idperiodo', 'desc')->get();
        $buildings = \App\Models\Building::orderBy('num_edific')->get();
        $groups = Student::whereNotNull('grupo')->distinct()->orderBy('grupo')->pluck('grupo');

        return view('students.index', compact('students', 'searchError', 'careers', 'periods', 'buildings', 'groups'));
    }

    public function create()
    {
        $careers = Career::all();
        $studentUsers = User::where('role', 'estudiante')->orderBy('name')->get();
        return view('students.create', compact('careers', 'studentUsers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'matricula' => ['required', 'integer', 'unique:alumnos,matricula'],
            'user_id' => ['nullable', 'integer', 'exists:users,id', 'unique:alumnos,user_id'],
            'nombre' => ['required', 'string', 'max:50'],
            'idcarrera' => ['nullable', 'integer', 'exists:carreras,idcarrera'],
            'cuatrimestre' => ['nullable', 'integer'],
            'grupo' => ['nullable', 'string', 'max:50'],
            'apellidoPaterno' => ['nullable', 'string', 'max:50'],
            'apellidoMaterno' => ['nullable', 'string', 'max:50'],
            'numero_telefonico' => ['nullable', 'string', 'max:50'],
        ]);

        $data = $this->normalizeStudentPhoneData($data);

        Student::create($data);

        return redirect()->route('students.index')->with('status', 'Estudiante creado.');
    }

    public function show(Student $student)
    {
        $assignments = $student->assignments()->with(['locker', 'period', 'usuario'])->orderBy('idasigna', 'desc')->get();
        return view('students.show', compact('student', 'assignments'));
    }

    public function edit(Student $student)
    {
        $careers = Career::all();
        $studentUsers = User::where('role', 'estudiante')->orderBy('name')->get();
        return view('students.edit', compact('student', 'careers', 'studentUsers'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'matricula' => ['required', 'integer', Rule::unique('alumnos', 'matricula')->ignore($student->matricula, 'matricula')],
            'user_id' => ['nullable', 'integer', 'exists:users,id', Rule::unique('alumnos', 'user_id')->ignore($student->matricula, 'matricula')],
            'nombre' => ['required', 'string', 'max:50'],
            'idcarrera' => ['nullable', 'integer', 'exists:carreras,idcarrera'],
            'cuatrimestre' => ['nullable', 'integer'],
            'grupo' => ['nullable', 'string', 'max:50'],
            'apellidoPaterno' => ['nullable', 'string', 'max:50'],
            'apellidoMaterno' => ['nullable', 'string', 'max:50'],
            'numero_telefonico' => ['nullable', 'string', 'max:50'],
        ]);

        $data = $this->normalizeStudentPhoneData($data);

        $student->update($data);

        return redirect()->route('students.index')->with('status', 'Estudiante actualizado.');
    }

    public function destroy(Student $student)
    {
        try {
            $student->delete();
        } catch (QueryException $exception) {
            return redirect()->route('students.index')->with('status', 'No se puede eliminar el estudiante porque está relacionado con otros registros.');
        }

        return redirect()->route('students.index')->with('status', 'Estudiante eliminado.');
    }

    public function myLocker()
    {
        $student = Student::where('user_id', auth()->id())->first();

        $assignment = null;

        if ($student) {
            $assignment = $student->assignments()
                ->with(['locker.building'])
                ->whereNull('released_at')
                ->orderByDesc('fechaAsignacion')
                ->first();
        }

        $periods = Period::orderBy('fechaInicio', 'desc')->get();

        return view('students.my_locker', compact('student', 'assignment', 'periods'));
    }

    public function requestLocker(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return redirect()->route('student.home')->with('status', 'Tu cuenta no está vinculada a un estudiante.');
        }

        $data = $request->validate([
            'idperiodo' => ['required', 'integer', 'exists:periodos,idperiodo'],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ]);

        $alreadyRequested = LockerRequest::where('matricula', $student->matricula)
            ->where('idperiodo', (int) $data['idperiodo'])
            ->where('estado', 'pendiente')
            ->exists();

        if ($alreadyRequested) {
            return redirect()->route('student.home')->with('status', 'Ya tienes una solicitud pendiente para ese período.');
        }

        LockerRequest::create([
            'matricula' => $student->matricula,
            'idperiodo' => (int) $data['idperiodo'],
            'estado' => 'pendiente',
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        return redirect()->route('student.home')->with('status', 'Solicitud de casillero enviada correctamente.');
    }

    private function normalizeStudentPhoneData(array $data): array
    {
        $hasNumeroTelefonico = Schema::hasColumn('alumnos', 'numero_telefonico');
        $hasNumeroTelefono = Schema::hasColumn('alumnos', 'numero_telefono');

        $phoneValue = $data['numero_telefonico'] ?? null;

        if ($hasNumeroTelefonico) {
            $data['numero_telefonico'] = $phoneValue;
        } else {
            unset($data['numero_telefonico']);
        }

        if ($hasNumeroTelefono) {
            $data['numero_telefono'] = $phoneValue;
        }

        return $data;
    }
}
