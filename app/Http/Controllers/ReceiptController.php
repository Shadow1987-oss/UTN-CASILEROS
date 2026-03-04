<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Sanction;
use App\Models\Student;
use Illuminate\Http\Request;

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
            'idsancion' => ['nullable', 'integer', 'exists:sanciones,idsancion'],
            'matricula' => ['nullable', 'integer', 'exists:alumnos,matricula'],
        ]);

        Receipt::create($data);

        return redirect()->route('recibe.index')->with('status', 'Recibo creado.');
    }

    public function destroy(Receipt $recibo)
    {
        $recibo->delete();
        return redirect()->route('recibe.index')->with('status', 'Recibo eliminado.');
    }
}
