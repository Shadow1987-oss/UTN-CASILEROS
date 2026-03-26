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
            'idrecibe' => ['required', 'integer', 'unique:recibe,idrecibe'],
            'idsancion' => ['required', 'integer', 'exists:sanciones,idsancion'],
            'matricula' => ['required', 'integer', 'exists:alumnos,matricula'],
        ]);

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
            'idrecibe' => ['required', 'integer', Rule::unique('recibe', 'idrecibe')->ignore($recibo->idrecibe, 'idrecibe')],
            'idsancion' => ['required', 'integer', 'exists:sanciones,idsancion'],
            'matricula' => ['required', 'integer', 'exists:alumnos,matricula'],
        ]);

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
}
