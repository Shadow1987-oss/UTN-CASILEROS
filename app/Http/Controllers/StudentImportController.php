<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

/**
 * Controlador para importación masiva de estudiantes vía CSV/XLSX.
 *
 * Recibe un archivo CSV o XLSX con columnas mínimas: nombre, matricula.
 * Columnas opcionales: idcarrera, cuatrimestre, grupo, numero_telefonico.
 * Utiliza la librería Box\Spout para leer archivos.
 *
 * La validación del archivo se hace por extensión (no MIME) para
 * compatibilidad con Windows donde la detección MIME es poco confiable.
 */
class StudentImportController extends Controller
{
    /**
     * Procesa la importación del archivo subido.
     *
     * Flujo:
     * 1. Valida extensión del archivo (csv, txt, xlsx; xls no soportado).
     * 2. Abre el archivo con Box\Spout.
     * 3. Lee el encabezado y verifica columnas obligatorias.
     * 4. Por cada fila, normaliza la matrícula y hace updateOrCreate.
     * 5. Registra errores en el log con contexto detallado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', function ($attribute, $value, $fail) {
                $allowed = ['csv', 'txt', 'xlsx', 'xls'];
                $ext = strtolower($value->getClientOriginalExtension());
                if (!in_array($ext, $allowed, true)) {
                    $fail('El archivo debe ser CSV, TXT, XLSX o XLS.');
                }
            }],
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
            \Log::error('Student import failed', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            $userMessage = 'No se pudo procesar el archivo.';

            if (stripos($exception->getMessage(), 'column') !== false || stripos($exception->getMessage(), 'field') !== false) {
                $userMessage .= ' Verifica que las columnas coincidan con el formato esperado.';
            } elseif (stripos($exception->getMessage(), 'encoding') !== false || stripos($exception->getMessage(), 'utf') !== false) {
                $userMessage .= ' Verifica que la codificación del archivo sea UTF-8.';
            } elseif (stripos($exception->getMessage(), 'zip') !== false || stripos($exception->getMessage(), 'corrupt') !== false) {
                $userMessage .= ' El archivo parece estar corrupto. Vuelve a guardarlo e inténtalo de nuevo.';
            } else {
                $userMessage .= ' Verifica formato, columnas y codificación. Detalle: ' . mb_strimwidth($exception->getMessage(), 0, 150, '...');
            }

            return back()->withErrors(['file' => $userMessage]);
        }
    }

    /**
     * Normaliza la matrícula: mayúsculas, sin espacios, formato LETRAS-NÚMEROS.
     *
     * Ejemplo: 'tic320072' → 'TIC-320072'
     *
     * @param  string  $matricula
     * @return string
     */
    private function normalizeMatricula(string $matricula): string
    {
        $normalized = strtoupper(trim($matricula));
        $normalized = preg_replace('/\s+/', '', $normalized);

        if (preg_match('/^([A-Z]{2,10})-?(\d{3,10})$/', $normalized, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        return $normalized;
    }

    /**
     * Recorta un valor y retorna null si está vacío.
     *
     * @param  mixed  $value
     * @return string|null
     */
    private function nullableTrim($value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));
        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Convierte un valor a entero nullable con rango opcional.
     *
     * Retorna null si el valor está vacío, no es numérico,
     * o está fuera del rango [$min, $max].
     *
     * @param  mixed     $value
     * @param  int|null  $min  Valor mínimo aceptable
     * @param  int|null  $max  Valor máximo aceptable
     * @return int|null
     */
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
