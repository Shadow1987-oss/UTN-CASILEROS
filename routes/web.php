<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LockerRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LockerController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rutas principales de la aplicación UTN Lockers.
|
| Estructura de roles:
|   - guest: login y registro (correo institucional UTNay)
|   - estudiante: /mi-casillero (ver asignación, solicitar casillero)
|   - admin,tutor: dashboard, lecturas de alumnos/casilleros/asignaciones,
|                  estadísticas y gestión de solicitudes
|   - admin: CRUD completo de todos los recursos
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'estudiante'
            ? redirect('/mi-casillero')
            : redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    // ── Rutas públicas de autenticación (solo invitados) ──
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Notificaciones internas (cualquier usuario autenticado) ──
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read_all');

    // ── Vista de estudiante: mi casillero y solicitudes ──
    Route::get('/mi-casillero', [StudentController::class, 'myLocker'])
        ->middleware('role:estudiante')
        ->name('student.home');
    Route::post('/mi-casillero/solicitar', [StudentController::class, 'requestLocker'])
        ->middleware('role:estudiante')
        ->name('student.request_locker');

    // ── Rutas para admin y tutores (lecturas + asignaciones + estadísticas) ──
    Route::middleware('role:admin,tutor')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show')
            ->where('student', '[0-9]+|[A-Za-z]+-[0-9]+');
        Route::get('lockers', [LockerController::class, 'index'])->name('lockers.index');

        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::post('assignments/{assignment}/release', [AssignmentController::class, 'release'])->name('assignments.release');
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        // ── Estadísticas (dashboards analíticos, NO confundir con reportes CRUD) ──
        Route::get('estadisticas', [App\Http\Controllers\ReportsController::class, 'index'])->name('estadisticas.index');
        Route::get('estadisticas/ocupacion', [App\Http\Controllers\ReportsController::class, 'occupancy'])->name('estadisticas.ocupacion');
        Route::get('estadisticas/por-grupo', [App\Http\Controllers\ReportsController::class, 'byGroup'])->name('estadisticas.por_grupo');
        Route::get('estadisticas/ocupacion/exportar', [App\Http\Controllers\ReportsController::class, 'exportOccupancy'])->name('estadisticas.ocupacion.exportar');
        Route::get('estadisticas/por-grupo/exportar', [App\Http\Controllers\ReportsController::class, 'exportByGroup'])->name('estadisticas.por_grupo.exportar');

        // ── Solicitudes de casillero (revisión por staff) ──
        Route::get('locker-requests', [LockerRequestController::class, 'index'])->name('locker_requests.index');
        Route::post('locker-requests/{lockerRequest}/approve', [LockerRequestController::class, 'approve'])->name('locker_requests.approve');
        Route::post('locker-requests/{lockerRequest}/reject', [LockerRequestController::class, 'reject'])->name('locker_requests.reject');
    });

    // ── Rutas exclusivas de administrador (CRUD completo) ──
    Route::middleware('role:admin')->group(function () {
        // Catálogos base
        Route::resource('careers', CareerController::class);
        Route::resource('buildings', BuildingController::class);

        // Tutores: CRUD completo
        Route::resource('tutores', UsuarioController::class)
            ->names('usuarios')
            ->parameters(['tutores' => 'usuario']);

        // Estudiantes: CRUD completo (excepto index/show que son compartidos con tutor)
        Route::resource('students', StudentController::class)->except(['index', 'show']);
        Route::post('students/import', [StudentImportController::class, 'store'])->name('students.import');

        // Casilleros: CRUD (excepto index que es compartido)
        Route::resource('lockers', LockerController::class)->except(['index']);

        // Períodos académicos
        Route::resource('periods', PeriodController::class);

        // Reportes de incidencias (CRUD) y sanciones
        Route::resource('reportes', App\Http\Controllers\ReportController::class)
            ->parameters(['reportes' => 'report']);
        Route::resource('sanciones', App\Http\Controllers\SanctionController::class);
        Route::resource('recibe', App\Http\Controllers\ReceiptController::class)
            ->parameters(['recibe' => 'recibo']);
    });
});
