<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class StudentImportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $file = $data['file'];
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getPathname();

        $reader = in_array($extension, ['csv', 'txt'], true)
            ? ReaderEntityFactory::createCSVReader()
            : ReaderEntityFactory::createXLSXReader();

        $reader->open($path);

        $header = null;
        $columns = null;
        $indexes = [];
        $imported = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            $isHeader = true;

            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();

                if ($isHeader) {
                    $header = $cells;
                    $columns = collect($header)->map(function ($value) {
                        return strtolower(trim((string) $value));
                    });

                                $required = ['nombre', 'matricula'];
                    $missing = collect($required)->diff($columns)->values();

                    if ($missing->isNotEmpty()) {
                        $reader->close();
                        return back()->withErrors(['file' => 'Missing columns: ' . $missing->implode(', ')]);
                    }

                    $indexes = [
                        'nombre' => $columns->search('nombre'),
                        'matricula' => $columns->search('matricula'),
                        'idcarrera' => $columns->search('idcarrera'),
                        'cuatrimestre' => $columns->search('cuatrimestre'),
                        'numero_telefono' => $columns->search('numero_telefono'),
                    ];

                    $isHeader = false;
                    continue;
                }

                $matricula = trim((string) ($cells[$indexes['matricula']] ?? ''));
                $nombre = trim((string) ($cells[$indexes['nombre']] ?? ''));

                if ($matricula === '' || $nombre === '') {
                    continue;
                }

                Student::updateOrCreate(
                    ['matricula' => $matricula],
                    [
                        'nombre' => $nombre,
                        'idcarrera' => isset($indexes['idcarrera']) && $indexes['idcarrera'] !== false ? trim((string) ($cells[$indexes['idcarrera']] ?? null)) : null,
                        'cuatrimestre' => isset($indexes['cuatrimestre']) && $indexes['cuatrimestre'] !== false ? trim((string) ($cells[$indexes['cuatrimestre']] ?? null)) : null,
                        'numero_telefono' => isset($indexes['numero_telefono']) && $indexes['numero_telefono'] !== false ? trim((string) ($cells[$indexes['numero_telefono']] ?? null)) : null,
                    ]
                );

                $imported++;
            }

            break;
        }

        $reader->close();

        if ($header === null) {
            return back()->withErrors(['file' => 'File has no data.']);
        }

        return redirect()->route('students.index')->with('status', "Imported {$imported} students.");
    }
}
