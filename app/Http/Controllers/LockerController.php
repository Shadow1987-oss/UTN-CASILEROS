<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LockerController extends Controller
{
    public function index()
    {
        $lockers = Locker::with('building')->get();

        return view('lockers.index', compact('lockers'));
    }

    public function create()
    {
        return view('lockers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idedificio' => ['nullable', 'integer', 'exists:edificios,idedificio'],
            'numeroCasiller' => ['required', 'integer'],
            'estado' => ['required', 'string', 'max:10'],
        ]);

        Locker::create($data);

        return redirect()->route('lockers.index')->with('status', 'Casillero creado.');
    }

    public function edit(Locker $locker)
    {
        return view('lockers.edit', compact('locker'));
    }

    public function update(Request $request, Locker $locker)
    {
        $data = $request->validate([
            'idedificio' => ['nullable', 'integer', 'exists:edificios,idedificio'],
            'numeroCasiller' => ['required', 'integer'],
            'estado' => ['required', 'string', 'max:10'],
        ]);

        $locker->update($data);

        return redirect()->route('lockers.index')->with('status', 'Casillero actualizado.');
    }

    public function destroy(Locker $locker)
    {
        $locker->delete();

        return redirect()->route('lockers.index')->with('status', 'Locker deleted.');
    }
}
