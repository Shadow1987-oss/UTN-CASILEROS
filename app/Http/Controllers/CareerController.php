<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

/**
 * Controlador CRUD para carreras universitarias.
 *
 * Gestiona el catálogo de carreras al que se vinculan los alumnos.
 * Tabla: carreras  |  PK: idcarrera
 */
class CareerController extends Controller
{
    /**
     * Listado de todas las carreras.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $careers = Career::orderBy('idcarrera')->get();
        return view('careers.index', compact('careers'));
    }

    public function create()
    {
        return view('careers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idcarrera' => ['required', 'integer', 'min:1', 'unique:carreras,idcarrera'],
            'nombre_carre' => ['required', 'string', 'max:50'],
        ]);

        Career::create($data);

        return redirect()->route('careers.index')->with('status', 'Carrera creada.');
    }

    public function show(Career $career)
    {
        return redirect()->route('careers.index');
    }

    public function edit(Career $career)
    {
        return view('careers.edit', compact('career'));
    }

    public function update(Request $request, Career $career)
    {
        $data = $request->validate([
            'idcarrera' => ['required', 'integer', 'min:1', Rule::unique('carreras', 'idcarrera')->ignore($career->idcarrera, 'idcarrera')],
            'nombre_carre' => ['required', 'string', 'max:50'],
        ]);

        $career->update($data);

        return redirect()->route('careers.index')->with('status', 'Carrera actualizada.');
    }

    /**
     * Elimina una carrera solo si no tiene alumnos asociados.
     *
     * @param  \App\Models\Career  $career
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Career $career)
    {
        $studentsLinked = Student::where('idcarrera', $career->idcarrera)->count();

        if ($studentsLinked > 0) {
            return redirect()->route('careers.index')->with('status', "No se puede eliminar la carrera porque tiene {$studentsLinked} alumno(s) asociado(s). Reasigna o elimina esos alumnos primero.");
        }

        try {
            $career->delete();
        } catch (QueryException $exception) {
            return redirect()->route('careers.index')->with('status', 'No se puede eliminar la carrera porque está relacionada con otros registros.');
        }

        return redirect()->route('careers.index')->with('status', 'Carrera eliminada.');
    }
}
