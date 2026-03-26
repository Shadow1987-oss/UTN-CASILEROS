# Lockers (UTN)

Sistema Laravel para gestionar asignación de casilleros, estudiantes, periodos, sanciones y reportes.

## Módulos

- Estudiantes (`alumnos`)
- Casilleros (`casilleros`)
- Períodos (`periodos`)
- Asignaciones (`asignamientos`)
- Sanciones (`sanciones`)
- Recibos de sanción (`recibe`)
- Reportes (`reportes`) + reportes estadísticos (`reports/*`)

## Requisitos

- PHP 7.4+
- Composer
- MySQL/MariaDB

## Instalación

### 1) Instalar dependencias

```bash
composer install
```

### 2) Configurar entorno

```bash
copy .env.example .env
php artisan key:generate
```

### 3) Configurar credenciales de BD

Editar `.env` con tus credenciales.

### 4) Ejecutar migraciones

```bash
php artisan migrate
```

## Nota de esquema

Este proyecto usa el esquema real en español (`alumnos`, `casilleros`, `periodos`, etc.).
Varios IDs primarios son manuales (no autoincrementales), por eso los formularios de creación solicitan ID.

## Importación de estudiantes

Desde la vista de estudiantes se permite importar CSV/XLSX.
Encabezados mínimos:

- `nombre`
- `matricula`

Opcionales:

- `idcarrera`
- `cuatrimestre`
- `numero_telefonico` (también acepta `numero_telefono`)

## Reportes PDF

La exportación PDF usa DomPDF (`barryvdh/laravel-dompdf`).
Si aún no está instalada en tu entorno:

```bash
composer require barryvdh/laravel-dompdf
```

## Tarea programada

Comando para liberar asignaciones de periodos vencidos:

```bash
php artisan assignments:release-expired
```

Programado en `app/Console/Kernel.php` para ejecución diaria.

## Pruebas

```bash
php artisan test
```
