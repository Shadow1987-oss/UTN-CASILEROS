<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'matricula' => ['required', 'integer', 'unique:alumnos,matricula'],
            'nombre' => ['required', 'string', 'max:50'],
            'idcarrera' => ['nullable', 'integer', 'exists:carreras,idcarrera'],
            'cuatrimestre' => ['nullable', 'integer'],
            'apellidoPaterno' => ['nullable', 'string', 'max:50'],
            'apellidoMaterno' => ['nullable', 'string', 'max:50'],
            'numero_telefono' => ['nullable', 'string', 'max:50'],
        ]);

        Student::create($data);

        return redirect()->route('students.index')->with('status', 'Estudiante creado.');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'matricula' => ['required', 'integer', Rule::unique('alumnos', 'matricula')->ignore($student->matricula, 'matricula')],
            'nombre' => ['required', 'string', 'max:50'],
            'idcarrera' => ['nullable', 'integer', 'exists:carreras,idcarrera'],
            'cuatrimestre' => ['nullable', 'integer'],
            'apellidoPaterno' => ['nullable', 'string', 'max:50'],
            'apellidoMaterno' => ['nullable', 'string', 'max:50'],
            'numero_telefono' => ['nullable', 'string', 'max:50'],
        ]);

        $student->update($data);

        return redirect()->route('students.index')->with('status', 'Estudiante actualizado.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('status', 'Student deleted.');
    }
}
