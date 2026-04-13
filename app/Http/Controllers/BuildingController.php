<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador CRUD para edificios.
 *
 * Gestiona los edificios donde se ubican los casilleros.
 * Tabla: edificios  |  PK: idedificio
 */
class BuildingController extends Controller
{
    /**
     * Listado de todos los edificios.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $buildings = Building::orderBy('idedificio')->get();
        return view('buildings.index', compact('buildings'));
    }

    /**
     * Formulario de creación de edificio.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('buildings.create');
    }

    /**
     * Almacena un nuevo edificio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Formulario de edición de edificio.
     *
     * @param  \App\Models\Building  $building
     * @return \Illuminate\View\View
     */
    public function edit(Building $building)
    {
        return view('buildings.edit', compact('building'));
    }

    /**
     * Actualiza un edificio existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Building      $building
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Building $building)
    {
        $data = $request->validate([
            'num_edific' => ['required', 'string', 'max:50'],
        ]);

        $building->update($data);

        return redirect()->route('buildings.index')->with('status', 'Edificio actualizado.');
    }

    /**
     * Elimina un edificio si no tiene registros relacionados.
     *
     * @param  \App\Models\Building  $building
     * @return \Illuminate\Http\RedirectResponse
     */
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
