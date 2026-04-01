{{-- Plantilla PDF para exportar el reporte por grupo/carrera.
     Se genera con dompdf cuando está disponible. --}}
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte por Grupo</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1 {
            margin-bottom: 0;
        }

        .muted {
            color: #666;
            margin-top: 2px;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }
    </style>
</head>

<body>
    <h1>Reporte por Grupo</h1>
    <p class="muted">Generado: {{ now()->format('d/m/Y H:i') }}</p>
    <p class="muted">
        Carrera: {{ $pdfFilters['carrera'] ?? 'Todas' }} |
        Cuatrimestre: {{ $pdfFilters['cuatrimestre'] ?? 'Todos' }} |
        Grupo: {{ $pdfFilters['grupo'] ?? 'Todos' }} |
        Edificio: {{ $pdfFilters['edificio'] ?? 'Todos' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Carrera</th>
                <th>Total estudiantes</th>
                <th>Con casillero</th>
                <th>Sin casillero</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                <tr>
                    <td>{{ $row['career'] }}</td>
                    <td>{{ $row['total_students'] }}</td>
                    <td>{{ $row['with_lockers'] }}</td>
                    <td>{{ $row['without_lockers'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay datos para los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
