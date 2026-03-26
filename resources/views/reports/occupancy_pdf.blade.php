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
        </tbody>
    </table>
</body>

</html>
