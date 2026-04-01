<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\LockerRequest;
use App\Models\Period;
use App\Models\Student;
use App\Models\UserNotification;
use Illuminate\Http\Request;

/**
 * Controlador para solicitudes de casillero de estudiantes.
 *
 * Los estudiantes crean solicitudes desde "Mi Casillero";
 * los admin/tutores las revisan aquí (aprobar o rechazar).
 * Al aprobar/rechazar se notifica al estudiante.
 *
 * Tabla: solicitudes_casillero
 */
class LockerRequestController extends Controller
{
    /**
     * Listado paginado de solicitudes con filtros opcionales.
     *
     * Filtros: estado (pendiente/aprobada/rechazada), idperiodo, matricula.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = LockerRequest::with(['student.career', 'period', 'reviewer']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('idperiodo')) {
            $query->where('idperiodo', (int) $request->input('idperiodo'));
        }

        if ($request->filled('matricula')) {
            $matricula = strtoupper(trim((string) $request->input('matricula')));
            $matricula = preg_replace('/\s+/', '', $matricula);

            if (preg_match('/^([A-Z]{2,10})-?(\d{3,10})$/', $matricula, $matches)) {
                $query->where('matricula', $matches[1] . '-' . $matches[2]);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $requests = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $periods = Period::orderBy('idperiodo', 'desc')->get();

        return view('locker_requests.index', compact('requests', 'periods'));
    }

    /**
     * Aprueba una solicitud pendiente.
     *
     * Verifica que el estudiante no tenga ya una asignación activa
     * en el período solicitado. Notifica al estudiante.
     *
     * @param  \Illuminate\Http\Request           $request
     * @param  \App\Models\LockerRequest           $lockerRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, LockerRequest $lockerRequest)
    {
        if ($lockerRequest->estado !== 'pendiente') {
            return redirect()->route('locker_requests.index')->with('status', 'La solicitud ya fue atendida.');
        }

        $data = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $alreadyAssigned = Assignment::where('matricula', $lockerRequest->matricula)
            ->where('idPeriodo', $lockerRequest->idperiodo)
            ->whereNull('released_at')
            ->exists();

        if ($alreadyAssigned) {
            return redirect()->route('locker_requests.index')->with('status', 'No se puede aprobar: el estudiante ya tiene una asignación activa en ese período.');
        }

        $lockerRequest->update([
            'estado' => 'aprobada',
            'review_notes' => $data['review_notes'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $student = Student::where('matricula', $lockerRequest->matricula)->first();
        if ($student && $student->user_id) {
            UserNotification::create([
                'user_id' => $student->user_id,
                'type' => 'locker_request',
                'title' => 'Solicitud aprobada',
                'message' => 'Tu solicitud de casillero fue aprobada.',
                'payload' => [
                    'locker_request_id' => (string) $lockerRequest->id,
                    'idperiodo' => (string) $lockerRequest->idperiodo,
                ],
            ]);
        }

        return redirect()->route('locker_requests.index')->with('status', 'Solicitud aprobada correctamente.');
    }

    /**
     * Rechaza una solicitud pendiente.
     *
     * Requiere notas de revisión obligatorias como motivo.
     * Notifica al estudiante con el motivo del rechazo.
     *
     * @param  \Illuminate\Http\Request           $request
     * @param  \App\Models\LockerRequest           $lockerRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, LockerRequest $lockerRequest)
    {
        if ($lockerRequest->estado !== 'pendiente') {
            return redirect()->route('locker_requests.index')->with('status', 'La solicitud ya fue atendida.');
        }

        $alreadyAssigned = Assignment::where('matricula', $lockerRequest->matricula)
            ->where('idPeriodo', $lockerRequest->idperiodo)
            ->whereNull('released_at')
            ->exists();

        if ($alreadyAssigned) {
            return redirect()->route('locker_requests.index')->with('status', 'No se puede rechazar: el estudiante ya tiene una asignación activa en ese período.');
        }

        $data = $request->validate([
            'review_notes' => ['required', 'string', 'max:255'],
        ]);

        $lockerRequest->update([
            'estado' => 'rechazada',
            'review_notes' => $data['review_notes'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $student = Student::where('matricula', $lockerRequest->matricula)->first();
        if ($student && $student->user_id) {
            UserNotification::create([
                'user_id' => $student->user_id,
                'type' => 'locker_request',
                'title' => 'Solicitud rechazada',
                'message' => 'Tu solicitud de casillero fue rechazada. Revisa el motivo.',
                'payload' => [
                    'locker_request_id' => (string) $lockerRequest->id,
                    'idperiodo' => (string) $lockerRequest->idperiodo,
                    'review_notes' => $data['review_notes'],
                ],
            ]);
        }

        return redirect()->route('locker_requests.index')->with('status', 'Solicitud rechazada correctamente.');
    }
}
