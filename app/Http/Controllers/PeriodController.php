<?php

namespace App\Http\Controllers;

use App\Models\Period;
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

    public function destroy(Period $period)
    {
        $period->delete();

        return redirect()->route('periods.index')->with('status', 'Period deleted.');
    }
}
