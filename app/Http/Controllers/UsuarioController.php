<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador CRUD para tutores/usuarios del dominio.
 *
 * Los "usuarios" (tabla usuarios) representan tutores y administradores
 * dentro del dominio de negocio (distintos de los users de autenticación).
 * Se usan como responsables en asignaciones, reportes y sanciones.
 *
 * Tabla: usuarios  |  PK: idusuario
 */
class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('idusuario')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idusuario' => ['required', 'integer', 'min:1', 'unique:usuarios,idusuario'],
            'nombre' => ['required', 'string', 'max:50'],
            'apellidoP' => ['nullable', 'string', 'max:50'],
            'apellidoM' => ['nullable', 'string', 'max:50'],
            'cargo' => ['nullable', 'string', 'max:50'],
        ]);

        Usuario::create($data);

        return redirect()->route('usuarios.index')->with('status', 'Tutor creado.');
    }

    public function show(Usuario $usuario)
    {
        return redirect()->route('usuarios.index');
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'idusuario' => ['required', 'integer', 'min:1', Rule::unique('usuarios', 'idusuario')->ignore($usuario->idusuario, 'idusuario')],
            'nombre' => ['required', 'string', 'max:50'],
            'apellidoP' => ['nullable', 'string', 'max:50'],
            'apellidoM' => ['nullable', 'string', 'max:50'],
            'cargo' => ['nullable', 'string', 'max:50'],
        ]);

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('status', 'Tutor actualizado.');
    }

    public function destroy(Usuario $usuario)
    {
        try {
            $usuario->delete();
        } catch (QueryException $exception) {
            return redirect()->route('usuarios.index')->with('status', 'No se puede eliminar el tutor porque está relacionado con otros registros.');
        }

        return redirect()->route('usuarios.index')->with('status', 'Tutor eliminado.');
    }
}
