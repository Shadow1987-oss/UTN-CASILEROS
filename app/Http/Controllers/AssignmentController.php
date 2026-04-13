<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Period;
use App\Models\Student;
use App\Models\Usuario;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador para la gestión de asignaciones de casilleros a estudiantes.
 *
 * Permite crear, editar, liberar y eliminar asignaciones. Cada asignación
 * vincula un estudiante (matrícula) con un casillero en un período determinado,
 * opcionalmente supervisado por un tutor (usuario). Incluye validaciones de
 * negocio como límite de 2 estudiantes por casillero y bloqueo de casilleros dañados.
 *
 * Tabla: asignamientos
 */
class AssignmentController extends Controller
{
    /**
     * Listado paginado de asignaciones con filtros opcionales.
     *
     * Filtros disponibles: idPeriodo, grupo del estudiante, idusuario (tutor).
     * También calcula la carga activa de cada tutor para mostrarla en la vista.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Assignment::with(['student', 'locker', 'period', 'usuario']);

        if ($request->filled('idPeriodo')) {
            $query->where('idPeriodo', (int) $request->idPeriodo);
        }

        if ($request->filled('grupo')) {
            $group = $request->grupo;
            $query->whereHas('student', function ($studentQuery) use ($group) {
                $studentQuery->where('grupo', $group);
            });
        }

        if ($request->filled('idusuario')) {
            $query->where('idusuario', (int) $request->idusuario);
        }

        $assignments = $query->orderByDesc('idasigna')->paginate(20)->withQueryString();
        $periods = Period::orderBy('idperiodo', 'desc')->get();
        $groups = Student::whereNotNull('grupo')->distinct()->orderBy('grupo')->pluck('grupo');
        $usuarios = Usuario::orderBy('nombre')->orderBy('apellidoP')->get();

        $tutorLoads = Assignment::query()
            ->selectRaw('idusuario, COUNT(*) as active_assignments')
            ->whereNotNull('idusuario')
            ->whereNull('released_at')
            ->groupBy('idusuario')
            ->pluck('active_assignments', 'idusuario');

        return view('assignments.index', compact('assignments', 'periods', 'groups', 'usuarios', 'tutorLoads'));
    }

    /**
     * Formulario para crear una nueva asignación.
     *
     * Carga estudiantes, casilleros, períodos, tutores y la carga
     * activa de cada tutor para orientar la distribución.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $students = Student::orderBy('matricula')->get();
        $lockers = Locker::all();
        $periods = Period::all();
        $usuarios = Usuario::where('cargo', 'Tutor')
            ->orderBy('nombre')->orderBy('apellidoP')->get();

        $tutorLoads = Assignment::query()
            ->selectRaw('idusuario, COUNT(*) as active_assignments')
            ->whereNotNull('idusuario')
            ->whereNull('released_at')
            ->groupBy('idusuario')
            ->pluck('active_assignments', 'idusuario');

        return view('assignments.create', compact('students', 'lockers', 'periods', 'usuarios', 'tutorLoads'));
    }

    /**
     * Almacena una nueva asignación de casillero.
     *
     * Validaciones de negocio:
     * - Un estudiante solo puede tener un casillero por período.
     * - Máximo 2 estudiantes activos por casillero en el mismo período.
     * - No se permite asignar casilleros con estado "dañado".
     *
     * Al crear, notifica al estudiante y al staff (admin/tutores),
     * y sincroniza el estado del casillero (disponible/ocupado).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'idasigna' => ['required', 'integer', 'min:1', 'unique:asignamientos,idasigna'],
            'matricula' => ['required', 'exists:alumnos,matricula'],
            'idcasillero' => ['required', 'integer', 'min:1', 'exists:casilleros,idcasillero'],
            'idPeriodo' => ['required', 'integer', 'min:1', 'exists:periodos,idperiodo'],
            'idusuario' => ['nullable', 'integer', 'min:1', 'exists:usuarios,idusuario'],
            'fechaAsignacion' => ['required', 'date'],
        ]);

        $alreadyAssigned = Assignment::where('matricula', $data['matricula'])
            ->where('idPeriodo', $data['idPeriodo'])
            ->whereNull('released_at')
            ->exists();

        if ($alreadyAssigned) {
            return back()->withInput()->withErrors(['matricula' => 'El estudiante ya tiene un casillero activo en este período.']);
        }

        $activeCount = Assignment::where('idcasillero', $data['idcasillero'])
            ->where('idPeriodo', $data['idPeriodo'])
            ->whereNull('released_at')
            ->count();

        if ($activeCount >= 2) {
            return back()->withInput()->withErrors(['idcasillero' => 'El casillero ya está asignado a 2 estudiantes en este periodo.']);
        }

        $locker = Locker::find($data['idcasillero']);
        if ($locker && $locker->estado === 'dañado') {
            return back()->withInput()->withErrors(['idcasillero' => 'No se puede asignar un casillero dañado.']);
        }

        $assignment = Assignment::create(array_merge($data, [
            'status' => 'activo',
            'released_at' => null,
        ]));

        $student = Student::where('matricula', $assignment->matricula)->first();
        if ($student && $student->user_id) {
            UserNotification::create([
                'user_id' => $student->user_id,
                'type' => 'assignment',
                'title' => 'Casillero asignado',
                'message' => 'Se te asignó un casillero para el período seleccionado.',
                'payload' => [
                    'idasigna' => (string) $assignment->idasigna,
                    'idPeriodo' => (string) $assignment->idPeriodo,
                ],
            ]);
        }

        // Notificar a todos los admin y tutores
        $this->notifyStaff(
            'Nueva asignación de casillero',
            "Se asignó el casillero #{$assignment->idcasillero} al estudiante {$assignment->matricula}.",
            ['idasigna' => (string) $assignment->idasigna]
        );

        $this->syncLockerStatus((int) $data['idcasillero']);

        return redirect()->route('assignments.index')->with('status', 'Asignación creada.');
    }

    /**
     * Formulario de edición de una asignación existente.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\View\View
     */
    public function edit(Assignment $assignment)
    {
        $students = Student::orderBy('matricula')->get();
        $lockers = Locker::all();
        $periods = Period::all();
        $usuarios = Usuario::where('cargo', 'Tutor')
            ->orderBy('nombre')->orderBy('apellidoP')->get();

        $tutorLoads = Assignment::query()
            ->selectRaw('idusuario, COUNT(*) as active_assignments')
            ->whereNotNull('idusuario')
            ->whereNull('released_at')
            ->groupBy('idusuario')
            ->pluck('active_assignments', 'idusuario');

        return view('assignments.edit', compact('assignment', 'students', 'lockers', 'periods', 'usuarios', 'tutorLoads'));
    }

    /**
     * Actualiza una asignación existente.
     *
     * Aplica las mismas reglas de negocio que store() excluyendo
     * el registro actual. Si se cambia de casillero, sincroniza
     * el estado tanto del casillero anterior como del nuevo.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  \App\Models\Assignment      $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Assignment $assignment)
    {
        $previousLockerId = (int) $assignment->idcasillero;

        $data = $request->validate([
            'idasigna' => ['required', 'integer', 'min:1', Rule::unique('asignamientos', 'idasigna')->ignore($assignment->idasigna, 'idasigna')],
            'matricula' => ['required', 'exists:alumnos,matricula'],
            'idcasillero' => ['required', 'integer', 'min:1', 'exists:casilleros,idcasillero'],
            'idPeriodo' => ['required', 'integer', 'min:1', 'exists:periodos,idperiodo'],
            'idusuario' => ['nullable', 'integer', 'min:1', 'exists:usuarios,idusuario'],
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
            ->whereNull('released_at')
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

    /**
     * Libera una asignación activa.
     *
     * Establece released_at y status='liberado'. Notifica al estudiante
     * y al staff, y actualiza el estado del casillero a 'disponible'
     * si no quedan más asignaciones activas.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function release(Assignment $assignment)
    {
        if ($assignment->released_at) {
            return redirect()->route('assignments.index')->with('status', 'La asignación ya fue liberada.');
        }

        $assignment->update([
            'released_at' => now(),
            'status' => 'liberado',
        ]);

        $student = Student::where('matricula', $assignment->matricula)->first();
        if ($student && $student->user_id) {
            UserNotification::create([
                'user_id' => $student->user_id,
                'type' => 'assignment',
                'title' => 'Casillero liberado',
                'message' => 'Tu asignación de casillero fue liberada.',
                'payload' => [
                    'idasigna' => (string) $assignment->idasigna,
                    'idPeriodo' => (string) $assignment->idPeriodo,
                ],
            ]);
        }

        $this->syncLockerStatus((int) $assignment->idcasillero);

        // Notificar a todos los admin y tutores
        $this->notifyStaff(
            'Casillero liberado',
            "Se liberó el casillero #{$assignment->idcasillero} del estudiante {$assignment->matricula}.",
            ['idasigna' => (string) $assignment->idasigna]
        );

        return redirect()->route('assignments.index')->with('status', 'Asignación liberada.');
    }

    /**
     * Elimina una asignación y sincroniza el estado del casillero.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Sincroniza el estado de un casillero según sus asignaciones activas.
     *
     * Si el casillero está marcado como "dañado" no se modifica.
     * En caso contrario se pone "ocupado" u "disponible" según
     * existan asignaciones sin liberar.
     *
     * @param  int  $lockerId  ID del casillero (idcasillero)
     * @return void
     */
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

    /**
     * Envía una notificación interna a todos los usuarios con rol admin o tutor.
     *
     * @param  string  $title    Título de la notificación
     * @param  string  $message  Cuerpo del mensaje
     * @param  array   $payload  Datos adicionales en formato clave-valor
     * @return void
     */
    private function notifyStaff(string $title, string $message, array $payload = []): void
    {
        $staffUsers = User::whereIn('role', ['admin', 'tutor'])->get(['id']);
        foreach ($staffUsers as $staffUser) {
            UserNotification::create([
                'user_id' => (int) $staffUser->id,
                'type' => 'assignment',
                'title' => $title,
                'message' => $message,
                'payload' => $payload,
            ]);
        }
    }
}
