<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('usuario')->get();
        return view('reportes.index', compact('reports'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        return view('reportes.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'descripcion' => ['required', 'string', 'max:255'],
        ]);

        Report::create($data + ['idusuario' => $data['idusuario'] ?? null]);

        return redirect()->route('reportes.index')->with('status', 'Reporte creado.');
    }

    public function edit(Report $report)
    {
        $usuarios = Usuario::all();
        return view('reportes.edit', compact('report', 'usuarios'));
    }

    public function update(Request $request, Report $report)
    {
        $data = $request->validate([
            'idusuario' => ['nullable', 'integer', 'exists:usuarios,idusuario'],
            'descripcion' => ['required', 'string', 'max:255'],
        ]);

        $report->update($data);

        return redirect()->route('reportes.index')->with('status', 'Reporte actualizado.');
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('reportes.index')->with('status', 'Reporte eliminado.');
    }
}
