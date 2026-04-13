<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Sanction;
use App\Models\Student;
use App\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Controlador CRUD para sanciones.
 *
 * Una sanción registra una penalización (motivo y descripción)
 * asociada a un tutor (usuario). Al crear/actualizar una sanción
 * se crea automáticamente el recibo correspondiente en la tabla
 * "recibe" para vincularla con el estudiante afectado.
 *
 * Tabla: sanciones  |  PK: idsancion
 */
class SanctionController extends Controller
{
    public function index()
    {
        $sanciones = Sanction::with(['usuario', 'receipt.student'])->orderByDesc('idsancion')->paginate(20);
        return view('sanciones.index', compact('sanciones'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        $students = Student::orderBy('matricula')->get();
        return view('sanciones.create', compact('usuarios', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idsancion' => ['required', 'integer', 'min:1', 'unique:sanciones,idsancion'],
            'idusuario' => ['nullable', 'integer', 'min:1', 'exists:usuarios,idusuario'],
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', 'exists:alumnos,matricula'],
            'sancion' => ['required', 'string', 'max:50'],
            'motivo' => ['required', 'string', 'max:50'],
        ]);

        $normalizedMatricula = $this->normalizeMatricula((string) $data['matricula']) ?? (string) $data['matricula'];

        unset($data['matricula']);

        DB::transaction(function () use ($data, $normalizedMatricula) {
            Sanction::create($data);

            Receipt::updateOrCreate(
                ['idsancion' => (int) $data['idsancion']],
                [
                    'idrecibe' => (int) ((int) Receipt::max('idrecibe') + 1),
                    'matricula' => $normalizedMatricula,
                ]
            );
        });

        return redirect()->route('sanciones.index')->with('status', 'Sanción creada.');
    }

    public function edit(Sanction $sancione)
    {
        $usuarios = Usuario::all();
        $students = Student::orderBy('matricula')->get();
        $selectedMatricula = optional($sancione->receipt)->matricula;
        return view('sanciones.edit', ['sancione' => $sancione, 'usuarios' => $usuarios, 'students' => $students, 'selectedMatricula' => $selectedMatricula]);
    }

    public function update(Request $request, Sanction $sancione)
    {
        $data = $request->validate([
            'idsancion' => ['required', 'integer', 'min:1', Rule::unique('sanciones', 'idsancion')->ignore($sancione->idsancion, 'idsancion')],
            'idusuario' => ['nullable', 'integer', 'min:1', 'exists:usuarios,idusuario'],
            'matricula' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', 'exists:alumnos,matricula'],
            'sancion' => ['required', 'string', 'max:50'],
            'motivo' => ['required', 'string', 'max:50'],
        ]);

        $rawMatricula = $data['matricula'] ?? optional($sancione->receipt)->matricula;
        $normalizedMatricula = $this->normalizeMatricula((string) $rawMatricula) ?? (string) $rawMatricula;
        unset($data['matricula']);

        DB::transaction(function () use ($sancione, $data, $normalizedMatricula) {
            $sancione->update($data);

            Receipt::updateOrCreate(
                ['idsancion' => (int) $sancione->idsancion],
                [
                    'idrecibe' => optional($sancione->receipt)->idrecibe ? (int) $sancione->receipt->idrecibe : (int) ((int) Receipt::max('idrecibe') + 1),
                    'matricula' => $normalizedMatricula,
                ]
            );
        });

        return redirect()->route('sanciones.index')->with('status', 'Sanción actualizada.');
    }

    public function show(Sanction $sancione)
    {
        return redirect()->route('sanciones.index');
    }

    public function destroy(Sanction $sancione)
    {
        try {
            Receipt::where('idsancion', (int) $sancione->idsancion)->delete();
            $sancione->delete();
        } catch (QueryException $exception) {
            return redirect()->route('sanciones.index')->with('status', 'No se puede eliminar la sanción porque está relacionada con otros registros.');
        }

        return redirect()->route('sanciones.index')->with('status', 'Sanción eliminada.');
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
