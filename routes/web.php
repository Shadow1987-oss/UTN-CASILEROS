<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LockerController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentImportController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('students', StudentController::class);
Route::post('students/import', [StudentImportController::class, 'store'])->name('students.import');

Route::resource('lockers', LockerController::class);
Route::resource('periods', PeriodController::class);
Route::resource('assignments', AssignmentController::class);
Route::post('assignments/{assignment}/release', [AssignmentController::class, 'release'])->name('assignments.release');

Route::resource('reportes', App\Http\Controllers\ReportController::class);
Route::resource('sanciones', App\Http\Controllers\SanctionController::class);
Route::resource('recibe', App\Http\Controllers\ReceiptController::class);
