<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LockerController extends Controller
{
    public function index(Request $request)
    {
        $query = Locker::with('building');

        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        $lockers = $query->get();

        return view('lockers.index', compact('lockers'));
    }

    public function create()
    {
        $buildings = \App\Models\Building::all();
        return view('lockers.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idcasillero' => ['required', 'integer', 'unique:casilleros,idcasillero'],
            'idedificio' => ['nullable', 'integer', 'exists:edificios,idedificio'],
            'numeroCasiller' => ['required', 'integer'],
            'estado' => ['required', 'string', 'max:10'],
        ]);

        Locker::create($data);

        return redirect()->route('lockers.index')->with('status', 'Casillero creado.');
    }

    public function edit(Locker $locker)
    {
        $buildings = \App\Models\Building::all();
        return view('lockers.edit', compact('locker', 'buildings'));
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

    public function show(Locker $locker)
    {
        return redirect()->route('lockers.index');
    }

    public function destroy(Locker $locker)
    {
        try {
            $locker->delete();
        } catch (QueryException $exception) {
            return redirect()->route('lockers.index')->with('status', 'No se puede eliminar el casillero porque está relacionado con otros registros.');
        }

        return redirect()->route('lockers.index')->with('status', 'Casillero eliminado.');
    }
}
