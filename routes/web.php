<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PayrollController;


Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/summary', [DashboardController::class, 'getMonthlySummary'])->middleware(['auth'])->name('dashboard.summary');
Route::get('/dashboard/chart', [DashboardController::class, 'getChartData'])->middleware(['auth'])->name('dashboard.chart');

Route::middleware(['auth'])->group(function () {

    // VIEW LIST
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');

    // CREATE PAGE
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');

    // STORE DATA
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');

    // SHOW DETAILS
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');

    // EDIT EMPLOYEE
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');

    // DELETE EMPLOYEE
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // API - GET EMPLOYEE JSON
    Route::get('/api/employees/{id}', [EmployeeController::class, 'getJson']);

});

Route::middleware(['auth'])->group(function () {

    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');

    Route::get('/holidays/{id}/edit', [HolidayController::class, 'edit'])->name('holidays.edit');
    Route::put('/holidays/{id}', [HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

});

// PAYROLL MODULE ROUTES
Route::middleware(['auth'])->group(function () {

    // ATTENDANCE ROUTES
    Route::get('/payroll/attendance', [PayrollController::class, 'attendance'])->name('payroll.attendance');
    Route::get('/payroll/attendance/get', [PayrollController::class, 'getAttendance'])->name('payroll.attendance.get');
    Route::get('/payroll/attendance/add', [PayrollController::class, 'addAttendance'])->name('payroll.attendance.add');
    Route::post('/payroll/attendance', [PayrollController::class, 'storeAttendance'])->name('payroll.attendance.store');
    Route::get('/payroll/attendance/export', [PayrollController::class, 'exportAttendance'])->name('payroll.attendance.export');
    Route::get('/payroll/attendance/details', [PayrollController::class, 'getAttendanceDetails'])->name('payroll.attendance.details');
    Route::delete('/payroll/attendance/{id}', [PayrollController::class, 'destroyAttendance'])->name('payroll.attendance.destroy');
    Route::delete('/payroll/attendance/date/{date}', [PayrollController::class, 'destroyAttendanceByDate'])->name('payroll.attendance.destroyByDate');
    Route::post('/payroll/attendance/delete-bulk', [PayrollController::class, 'bulkDestroyAttendance'])->name('payroll.attendance.bulkDestroy');

    // PAYROLL CALCULATION ROUTES
    Route::get('/payroll/calculation', [PayrollController::class, 'calculation'])->name('payroll.calculation');
    Route::post('/payroll/calculate', [PayrollController::class, 'calculatePayroll'])->name('payroll.calculate');
    Route::post('/payroll/store', [PayrollController::class, 'storePayroll'])->name('payroll.store');

    // PAYROLL LIST ROUTES
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/get', [PayrollController::class, 'getPayroll'])->name('payroll.get');
    Route::get('/payroll/export', [PayrollController::class, 'export'])->name('payroll.export');
    Route::get('/payroll/{id}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/{id}/status', [PayrollController::class, 'updateStatus'])->name('payroll.status');
    Route::delete('/payroll/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
