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
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $file = $data['file'];
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getPathname();

        if ($extension === 'xls') {
            return back()->withErrors(['file' => 'El formato XLS no es compatible. Guarda el archivo como XLSX o CSV e inténtalo de nuevo.']);
        }

        try {
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
                            return back()->withErrors(['file' => 'Faltan columnas obligatorias: ' . $missing->implode(', ')]);
                        }

                        $indexes = [
                            'nombre' => $columns->search('nombre'),
                            'matricula' => $columns->search('matricula'),
                            'idcarrera' => $columns->search('idcarrera'),
                            'cuatrimestre' => $columns->search('cuatrimestre'),
                            'grupo' => $columns->search('grupo'),
                            'numero_telefonico' => $columns->search('numero_telefonico') !== false
                                ? $columns->search('numero_telefonico')
                                : $columns->search('numero_telefono'),
                        ];

                        $isHeader = false;
                        continue;
                    }

                    $matricula = $this->normalizeMatricula(trim((string) ($cells[$indexes['matricula']] ?? '')));
                    $nombre = trim((string) ($cells[$indexes['nombre']] ?? ''));

                    if ($matricula === '' || $nombre === '') {
                        continue;
                    }

                    Student::updateOrCreate(
                        ['matricula' => $matricula],
                        [
                            'nombre' => $nombre,
                            'idcarrera' => isset($indexes['idcarrera']) && $indexes['idcarrera'] !== false
                                ? $this->nullableInt($cells[$indexes['idcarrera']] ?? null, 1)
                                : null,
                            'cuatrimestre' => isset($indexes['cuatrimestre']) && $indexes['cuatrimestre'] !== false
                                ? $this->nullableInt($cells[$indexes['cuatrimestre']] ?? null, 1, 10)
                                : null,
                            'grupo' => isset($indexes['grupo']) && $indexes['grupo'] !== false
                                ? $this->nullableTrim($cells[$indexes['grupo']] ?? null)
                                : null,
                            'numero_telefonico' => isset($indexes['numero_telefonico']) && $indexes['numero_telefonico'] !== false
                                ? $this->nullableTrim($cells[$indexes['numero_telefonico']] ?? null)
                                : null,
                        ]
                    );

                    $imported++;
                }

                break;
            }

            $reader->close();

            if ($header === null) {
                return back()->withErrors(['file' => 'El archivo no contiene datos.']);
            }

            return redirect()->route('students.index')->with('status', "Se importaron {$imported} estudiantes.");
        } catch (\Throwable $exception) {
            return back()->withErrors(['file' => 'No se pudo procesar el archivo. Verifica formato, columnas y codificación.']);
        }
    }

    private function normalizeMatricula(string $matricula): string
    {
        $normalized = strtoupper(trim($matricula));
        $normalized = preg_replace('/\s+/', '', $normalized);

        if (preg_match('/^([A-Z]{2,10})-?(\d{3,10})$/', $normalized, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        return $normalized;
    }

    private function nullableTrim($value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));
        return $trimmed === '' ? null : $trimmed;
    }

    private function nullableInt($value, int $min = null, int $max = null): ?int
    {
        $trimmed = trim((string) ($value ?? ''));

        if ($trimmed === '' || !preg_match('/^-?\d+$/', $trimmed)) {
            return null;
        }

        $number = (int) $trimmed;

        if ($min !== null && $number < $min) {
            return null;
        }

        if ($max !== null && $number > $max) {
            return null;
        }

        return $number;
    }
}
