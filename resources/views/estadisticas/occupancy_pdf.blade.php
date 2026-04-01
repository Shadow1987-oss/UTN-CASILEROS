{{-- Plantilla PDF para exportar el reporte de ocupación de casilleros.
     Se genera con dompdf cuando está disponible. --}}
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Ocupación</title>
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
    <h1>Reporte de Ocupación</h1>
    <p class="muted">Generado: {{ now()->format('d/m/Y H:i') }}</p>
    <p class="muted">
        Edificio: {{ $pdfFilters['edificio'] ?? 'Todos' }} |
        Área: {{ $pdfFilters['area'] ?? 'Todas' }} |
        Planta: {{ $pdfFilters['planta'] ?? 'Todas' }} |
        Período: {{ $pdfFilters['periodo'] ?? 'Todos' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Métrica</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total casilleros</td>
                <td>{{ $data['total_lockers'] }}</td>
            </tr>
            <tr>
                <td>Disponibles</td>
                <td>{{ $data['available'] }}</td>
            </tr>
            <tr>
                <td>Ocupados</td>
                <td>{{ $data['occupied'] }}</td>
            </tr>
            <tr>
                <td>Dañados</td>
                <td>{{ $data['damaged'] }}</td>
            </tr>
            <tr>
                <td>Asignaciones activas</td>
                <td>{{ $data['active_assignments'] }}</td>
            </tr>
            <tr>
                <td>Promedio días de ocupación</td>
                <td>{{ $data['average_occupancy_days'] }}</td>
            </tr>
        </tbody>
    </table>

    <h2 style="margin-top: 14px;">Resumen por edificio</h2>
    <table>
        <thead>
            <tr>
                <th>Edificio</th>
                <th>Total</th>
                <th>Ocupados</th>
                <th>Dañados</th>
                <th>Disponibles</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['by_building'] as $row)
                <tr>
                    <td>{{ $row['building'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['occupied'] }}</td>
                    <td>{{ $row['damaged'] }}</td>
                    <td>{{ $row['available'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay datos para los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
