<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::orderBy('idedificio')->get();
        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('buildings.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idedificio' => ['required', 'integer', 'min:1', 'unique:edificios,idedificio'],
            'num_edific' => ['required', 'string', 'max:50'],
        ]);

        Building::create($data);

        return redirect()->route('buildings.index')->with('status', 'Edificio creado.');
    }

    public function show(Building $building)
    {
        return redirect()->route('buildings.index');
    }

    public function edit(Building $building)
    {
        return view('buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $data = $request->validate([
            'idedificio' => ['required', 'integer', 'min:1', Rule::unique('edificios', 'idedificio')->ignore($building->idedificio, 'idedificio')],
            'num_edific' => ['required', 'string', 'max:50'],
        ]);

        $building->update($data);

        return redirect()->route('buildings.index')->with('status', 'Edificio actualizado.');
    }

    public function destroy(Building $building)
    {
        try {
            $building->delete();
        } catch (QueryException $exception) {
            return redirect()->route('buildings.index')->with('status', 'No se puede eliminar el edificio porque está relacionado con otros registros.');
        }

        return redirect()->route('buildings.index')->with('status', 'Edificio eliminado.');
    }
}
