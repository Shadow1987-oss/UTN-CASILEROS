<?php

namespace App\Http\Controllers;

use App\Models\Sanction;
use App\Models\Usuario;
use Illuminate\Http\Request;

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
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'sancion' => ['required', 'string', 'max:50'],
            'motivo' => ['nullable', 'string', 'max:255'],
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
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'sancion' => ['required', 'string', 'max:50'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $sancione->update($data);

        return redirect()->route('sanciones.index')->with('status', 'Sanción actualizada.');
    }

    public function destroy(Sanction $sancione)
    {
        $sancione->delete();
        return redirect()->route('sanciones.index')->with('status', 'Sanción eliminada.');
    }
}
