<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\LockerRequest;
use App\Models\Period;
use App\Models\Student;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * Controlador CRUD para estudiantes (alumnos).
 *
 * Gestiona el catálogo de alumnos con filtros avanzados por matrícula,
 * carrera, cuatrimestre, grupo, período y edificio. También maneja
 * la vista "Mi Casillero" para el rol estudiante y la creación de
 * solicitudes de casillero con notificación automática al staff.
 *
 * Tabla: alumnos  |  PK: matricula (string)
 */
class StudentController extends Controller
{
    /**
     * Listado paginado de estudiantes con filtros avanzados.
     *
     * Filtros: search (únicamente por matrícula normalizada),
     * idcarrera, cuatrimestre (1–10), grupo, idperiodo, idedificio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Student::with('career');
        $searchError = null;

        if ($request->filled('search')) {
            $search = $this->normalizeMatricula((string) $request->search);

            if ($search === null || !preg_match('/^[A-Z]{2,10}-\d{3,10}$/', $search)) {
                $searchError = 'Ingresa una matrícula válida (ej. TIC-320072).';
                $query->whereRaw('1 = 0');
            } else {
                $query->where('matricula', $search);
            }
        }

        if ($request->filled('idcarrera')) {
            $query->where('idcarrera', (int) $request->idcarrera);
        }

        if ($request->filled('cuatrimestre')) {
            $cuatrimestre = (int) $request->cuatrimestre;
            if ($cuatrimestre >= 1 && $cuatrimestre <= 10) {
                $query->where('cuatrimestre', $cuatrimestre);
            } else {
                $query->whereRaw('1 = 0');
            }
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

        $students = $query->orderBy('matricula')->paginate(20)->withQueryString();
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
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', 'unique:alumnos,matricula'],
            'user_id' => ['nullable', 'integer', 'exists:users,id', 'unique:alumnos,user_id'],
            'nombre' => ['required', 'string', 'max:50'],
            'idcarrera' => ['nullable', 'integer', 'exists:carreras,idcarrera'],
            'cuatrimestre' => ['nullable', 'integer', 'between:1,10'],
            'grupo' => ['nullable', 'string', 'max:50'],
            'apellidoPaterno' => ['nullable', 'string', 'max:50'],
            'apellidoMaterno' => ['nullable', 'string', 'max:50'],
            'numero_telefonico' => ['nullable', 'string', 'max:50'],
        ]);

        $data['matricula'] = $this->normalizeMatricula($data['matricula']) ?? $data['matricula'];

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
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', Rule::unique('alumnos', 'matricula')->ignore($student->matricula, 'matricula')],
            'user_id' => ['nullable', 'integer', 'exists:users,id', Rule::unique('alumnos', 'user_id')->ignore($student->matricula, 'matricula')],
            'nombre' => ['required', 'string', 'max:50'],
            'idcarrera' => ['nullable', 'integer', 'exists:carreras,idcarrera'],
            'cuatrimestre' => ['nullable', 'integer', 'between:1,10'],
            'grupo' => ['nullable', 'string', 'max:50'],
            'apellidoPaterno' => ['nullable', 'string', 'max:50'],
            'apellidoMaterno' => ['nullable', 'string', 'max:50'],
            'numero_telefonico' => ['nullable', 'string', 'max:50'],
        ]);

        $data['matricula'] = $this->normalizeMatricula($data['matricula']) ?? $data['matricula'];

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

    /**
     * Vista "Mi Casillero" para el rol estudiante.
     *
     * Muestra la asignación activa del estudiante autenticado
     * y su historial de solicitudes de casillero.
     *
     * @return \Illuminate\View\View
     */
    public function myLocker()
    {
        $student = Student::where('user_id', auth()->id())->first();

        $assignment = null;
        $lockerRequests = collect();

        if ($student) {
            $assignment = $student->assignments()
                ->with(['locker.building'])
                ->whereNull('released_at')
                ->orderByDesc('fechaAsignacion')
                ->first();

            $lockerRequests = $student->lockerRequests()
                ->with(['period', 'reviewer'])
                ->orderByDesc('created_at')
                ->paginate(10);
        }

        $periods = Period::orderBy('fechaInicio', 'desc')->get();

        return view('students.my_locker', compact('student', 'assignment', 'periods', 'lockerRequests'));
    }

    /**
     * Procesa la solicitud de casillero de un estudiante.
     *
     * Validaciones:
     * - El estudiante no debe tener casillero activo.
     * - No debe haber solicitud pendiente previa.
     * Envía notificación a todos los admin y tutores.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestLocker(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return redirect()->route('student.home')->with('status', 'Tu cuenta no está vinculada a un estudiante.');
        }

        $data = $request->validate([
            'idperiodo' => ['required', 'integer', 'min:1', 'exists:periodos,idperiodo'],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ]);

        $hasActiveAssignment = $student->assignments()->whereNull('released_at')->exists();
        if ($hasActiveAssignment) {
            return redirect()->route('student.home')->with('status', 'Ya tienes un casillero activo, no puedes generar otra solicitud.');
        }

        $hasPendingRequest = LockerRequest::where('matricula', $student->matricula)
            ->where('estado', 'pendiente')
            ->exists();

        if ($hasPendingRequest) {
            return redirect()->route('student.home')->with('status', 'Ya tienes una solicitud pendiente de revisión.');
        }

        $alreadyRequested = LockerRequest::where('matricula', $student->matricula)
            ->where('idperiodo', (int) $data['idperiodo'])
            ->where('estado', 'pendiente')
            ->exists();

        if ($alreadyRequested) {
            return redirect()->route('student.home')->with('status', 'Ya tienes una solicitud pendiente para ese período.');
        }

        $lockerRequest = LockerRequest::create([
            'matricula' => $student->matricula,
            'idperiodo' => (int) $data['idperiodo'],
            'estado' => 'pendiente',
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        $reviewerUsers = User::whereIn('role', ['admin', 'tutor'])->get(['id']);
        foreach ($reviewerUsers as $reviewerUser) {
            UserNotification::create([
                'user_id' => (int) $reviewerUser->id,
                'type' => 'locker_request',
                'title' => 'Nueva solicitud de casillero',
                'message' => 'Se registró una nueva solicitud pendiente de revisión.',
                'payload' => [
                    'locker_request_id' => (string) $lockerRequest->id,
                    'matricula' => (string) $student->matricula,
                    'idperiodo' => (string) $lockerRequest->idperiodo,
                ],
            ]);
        }

        return redirect()->route('student.home')->with('status', 'Solicitud de casillero enviada correctamente.');
    }

    /**
     * Normaliza datos del teléfono según columna existente en la BD.
     *
     * Detecta dinámicamente si la tabla alumnos tiene la columna
     * 'numero_telefonico' o 'numero_telefono' y asigna el valor
     * correspondiente para compatibilidad entre esquemas.
     *
     * @param  array  $data  Datos del formulario validados
     * @return array  Datos ajustados
     */
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

    /**
     * Normaliza la matrícula al formato LETRAS-NÚMEROS (ej. TIC-320072).
     *
     * @param  string|null  $matricula
     * @return string|null
     */
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
}
