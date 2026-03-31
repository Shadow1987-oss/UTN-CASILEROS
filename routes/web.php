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
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read_all');

    Route::get('/mi-casillero', [StudentController::class, 'myLocker'])
        ->middleware('role:estudiante')
        ->name('student.home');
    Route::post('/mi-casillero/solicitar', [StudentController::class, 'requestLocker'])
        ->middleware('role:estudiante')
        ->name('student.request_locker');

    Route::middleware('role:admin,tutor')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::get('lockers', [LockerController::class, 'index'])->name('lockers.index');

        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::post('assignments/{assignment}/release', [AssignmentController::class, 'release'])->name('assignments.release');
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        Route::get('reports', [App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
        Route::get('reports/occupancy', [App\Http\Controllers\ReportsController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('reports/by-group', [App\Http\Controllers\ReportsController::class, 'byGroup'])->name('reports.by_group');
        Route::get('reports/occupancy/export', [App\Http\Controllers\ReportsController::class, 'exportOccupancy'])->name('reports.occupancy.export');
        Route::get('reports/by-group/export', [App\Http\Controllers\ReportsController::class, 'exportByGroup'])->name('reports.by_group.export');

        Route::get('locker-requests', [LockerRequestController::class, 'index'])->name('locker_requests.index');
        Route::post('locker-requests/{lockerRequest}/approve', [LockerRequestController::class, 'approve'])->name('locker_requests.approve');
        Route::post('locker-requests/{lockerRequest}/reject', [LockerRequestController::class, 'reject'])->name('locker_requests.reject');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('careers', CareerController::class);
        Route::resource('buildings', BuildingController::class);
        Route::resource('tutores', UsuarioController::class)
            ->names('usuarios')
            ->parameters(['tutores' => 'usuario'])
            ->except(['create', 'store']);

        Route::resource('students', StudentController::class)->except(['index', 'show', 'create', 'store']);
        Route::post('students/import', [StudentImportController::class, 'store'])->name('students.import');

        Route::resource('lockers', LockerController::class)->except(['index']);
        Route::resource('periods', PeriodController::class);

        Route::resource('reportes', App\Http\Controllers\ReportController::class)
            ->parameters(['reportes' => 'report']);
        Route::resource('sanciones', App\Http\Controllers\SanctionController::class);
        Route::resource('recibe', App\Http\Controllers\ReceiptController::class)
            ->parameters(['recibe' => 'recibo']);
    });
});
