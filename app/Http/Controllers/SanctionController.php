<?php

namespace App\Http\Controllers;

use App\Models\Sanction;
use App\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SanctionController extends Controller
{
    public function index()
    {
        $sanciones = Sanction::with('usuario')->get();
        return view('sanciones.index', compact('sanciones'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        return view('sanciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idsancion' => ['required', 'integer', 'unique:sanciones,idsancion'],
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'sancion' => ['required', 'string', 'max:50'],
            'motivo' => ['nullable', 'string', 'max:50'],
        ]);

        Sanction::create($data);

        return redirect()->route('sanciones.index')->with('status', 'Sanción creada.');
    }

    public function edit(Sanction $sancione)
    {
        $usuarios = Usuario::all();
        return view('sanciones.edit', ['sancione' => $sancione, 'usuarios' => $usuarios]);
    }

    public function update(Request $request, Sanction $sancione)
    {
        $data = $request->validate([
            'idsancion' => ['required', 'integer', Rule::unique('sanciones', 'idsancion')->ignore($sancione->idsancion, 'idsancion')],
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'sancion' => ['required', 'string', 'max:50'],
            'motivo' => ['nullable', 'string', 'max:50'],
        ]);

        $sancione->update($data);

        return redirect()->route('sanciones.index')->with('status', 'Sanción actualizada.');
    }

    public function show(Sanction $sancione)
    {
        return redirect()->route('sanciones.index');
    }

    public function destroy(Sanction $sancione)
    {
        try {
            $sancione->delete();
        } catch (QueryException $exception) {
            return redirect()->route('sanciones.index')->with('status', 'No se puede eliminar la sanción porque está relacionada con otros registros.');
        }

        return redirect()->route('sanciones.index')->with('status', 'Sanción eliminada.');
    }
}
