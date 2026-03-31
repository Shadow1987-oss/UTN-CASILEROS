<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Sanction;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReceiptController extends Controller
{
    public function index()
    {
        $recibos = Receipt::with(['sanction', 'student'])->get();
        return view('recibe.index', compact('recibos'));
    }

    public function create()
    {
        $sanciones = Sanction::all();
        $students = Student::all();
        return view('recibe.create', compact('sanciones', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idrecibe' => ['required', 'integer', 'min:1', 'unique:recibe,idrecibe'],
            'idsancion' => ['required', 'integer', 'min:1', 'exists:sanciones,idsancion'],
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', 'exists:alumnos,matricula'],
        ]);

        $data['matricula'] = $this->normalizeMatricula((string) $data['matricula']) ?? $data['matricula'];

        Receipt::create($data);

        return redirect()->route('recibe.index')->with('status', 'Recibo creado.');
    }

    public function show(Receipt $recibo)
    {
        return redirect()->route('recibe.index');
    }

    public function edit(Receipt $recibo)
    {
        $sanciones = Sanction::all();
        $students = Student::all();
        return view('recibe.edit', compact('recibo', 'sanciones', 'students'));
    }

    public function update(Request $request, Receipt $recibo)
    {
        $data = $request->validate([
            'idrecibe' => ['required', 'integer', 'min:1', Rule::unique('recibe', 'idrecibe')->ignore($recibo->idrecibe, 'idrecibe')],
            'idsancion' => ['required', 'integer', 'min:1', 'exists:sanciones,idsancion'],
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{2,10}-?\d{3,10}$/', 'exists:alumnos,matricula'],
        ]);

        $data['matricula'] = $this->normalizeMatricula((string) $data['matricula']) ?? $data['matricula'];

        $recibo->update($data);

        return redirect()->route('recibe.index')->with('status', 'Recibo actualizado.');
    }

    public function destroy(Receipt $recibo)
    {
        try {
            $recibo->delete();
        } catch (QueryException $exception) {
            return redirect()->route('recibe.index')->with('status', 'No se puede eliminar el recibo porque está relacionado con otros registros.');
        }

        return redirect()->route('recibe.index')->with('status', 'Recibo eliminado.');
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
}
