<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = Period::all();

        return view('periods.index', compact('periods'));
    }

    public function create()
    {
        return view('periods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idperiodo' => ['required', 'integer', 'unique:periodos,idperiodo'],
            'nombrePerio' => ['required', 'string', 'max:50'],
            'fechaInicio' => ['nullable', 'date'],
            'fechaFin' => ['nullable', 'date', 'after_or_equal:fechaInicio'],
        ]);

        Period::create($data);

        return redirect()->route('periods.index')->with('status', 'Período creado.');
    }

    public function edit(Period $period)
    {
        return view('periods.edit', compact('period'));
    }

    public function update(Request $request, Period $period)
    {
        $data = $request->validate([
            'nombrePerio' => ['required', 'string', 'max:50'],
            'fechaInicio' => ['nullable', 'date'],
            'fechaFin' => ['nullable', 'date', 'after_or_equal:fechaInicio'],
        ]);

        $period->update($data);

        return redirect()->route('periods.index')->with('status', 'Período actualizado.');
    }

    public function show(Period $period)
    {
        return redirect()->route('periods.index');
    }

    public function destroy(Period $period)
    {
        try {
            $period->delete();
        } catch (QueryException $exception) {
            return redirect()->route('periods.index')->with('status', 'No se puede eliminar el período porque está relacionado con otros registros.');
        }

        return redirect()->route('periods.index')->with('status', 'Período eliminado.');
    }
}
