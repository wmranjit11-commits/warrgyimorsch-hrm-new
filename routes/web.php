<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\ZKTController;
// use Rats\Zkteco\Lib\ZKTeco;


Route::get('/sync-attendance', [ZKTController::class, 'syncAttendance']);

Route::get('/', function () {
    return view('auth.login');
});

use App\Http\Controllers\DashboardController;

// Emergency Role Fix Route

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

    // EXPORT EMPLOYEES
    Route::get('/employees-export', [EmployeeController::class, 'export'])->name('employees.export');

    Route::get('/attendance-history', [EmployeeController::class, 'getAttendance'])->name('attendance-history');
    Route::get('/celebrations', [EmployeeController::class, 'employeeDays'])->name('employees.employeeDays');

    // API - GET EMPLOYEE JSON
    Route::get('/api/employees/{id}', [EmployeeController::class, 'getJson']);
    Route::get('/api/employees/{id}/attendance', [EmployeeController::class, 'getAttendance']);

    // PROJECT MODULE
    Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [\App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [\App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::patch('/projects/{project}/update-field', [\App\Http\Controllers\ProjectController::class, 'updateField'])->name('projects.update-field');
    Route::post('/projects/bulk-delete', [\App\Http\Controllers\ProjectController::class, 'bulkDelete'])->name('projects.bulk-delete');
    Route::delete('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/projects/{project}/tasks-summary', [\App\Http\Controllers\ProjectController::class, 'tasksSummary'])->name('projects.tasks-summary');

    // DAILY TASKS
    Route::get('/daily-tasks', [\App\Http\Controllers\DailyTaskController::class, 'index'])->name('daily-tasks.index');
    Route::post('/daily-tasks', [\App\Http\Controllers\DailyTaskController::class, 'store'])->name('daily-tasks.store');
    Route::put('/daily-tasks/{dailyTask}', [\App\Http\Controllers\DailyTaskController::class, 'update'])->name('daily-tasks.update');
    Route::patch('/daily-tasks/{dailyTask}/status', [\App\Http\Controllers\DailyTaskController::class, 'updateStatus'])->name('daily-tasks.update-status');
    Route::patch('/daily-tasks/{dailyTask}/priority', [\App\Http\Controllers\DailyTaskController::class, 'updatePriority'])->name('daily-tasks.update-priority');
    Route::delete('/daily-tasks/{dailyTask}', [\App\Http\Controllers\DailyTaskController::class, 'destroy'])->name('daily-tasks.destroy');
    Route::post('/daily-tasks/bulk-delete', [\App\Http\Controllers\DailyTaskController::class, 'bulkDestroy'])->name('daily-tasks.bulk-delete');
    Route::post('/daily-tasks/follow-up', [\App\Http\Controllers\DailyTaskController::class, 'storeFollowUp'])->name('daily-tasks.follow-up.store');
    Route::put('/daily-tasks/follow-up/{id}', [\App\Http\Controllers\DailyTaskController::class, 'updateFollowUp'])->name('daily-tasks.follow-up.update');
    Route::delete('/daily-tasks/follow-up/{id}', [\App\Http\Controllers\DailyTaskController::class, 'destroyFollowUp'])->name('daily-tasks.follow-up.destroy');
    Route::get('/daily-tasks/{taskId}/follow-ups', [\App\Http\Controllers\DailyTaskController::class, 'getFollowUps']);

    // STATIC PAGES
    Route::get('/help', function () {
        return view('pages.help');
    })->name('help');
    Route::get('/terms', function () {
        return view('pages.terms');
    })->name('terms');
    Route::get('/privacy', function () {
        return view('pages.privacy');
    })->name('privacy');

    // MASTER MODULE
    Route::get('/master/departments', [\App\Http\Controllers\MasterController::class, 'departments'])->name('master.departments');
    Route::get('/master/designations', [\App\Http\Controllers\MasterController::class, 'designations'])->name('master.designations');
    Route::get('/master/roles', [\App\Http\Controllers\MasterController::class, 'roles'])->name('master.roles');
    Route::post('/master/department', [\App\Http\Controllers\MasterController::class, 'storeDepartment'])->name('master.department.store');
    Route::put('/master/department/{id}', [\App\Http\Controllers\MasterController::class, 'updateDepartment'])->name('master.department.update');
    Route::delete('/master/department/{id}', [\App\Http\Controllers\MasterController::class, 'destroyDepartment'])->name('master.department.destroy');
    Route::post('/master/designation', [\App\Http\Controllers\MasterController::class, 'storeDesignation'])->name('master.designation.store');
    Route::put('/master/designation/{id}', [\App\Http\Controllers\MasterController::class, 'updateDesignation'])->name('master.designation.update');
    Route::delete('/master/designation/{id}', [\App\Http\Controllers\MasterController::class, 'destroyDesignation'])->name('master.designation.destroy');
    Route::post('/master/role', [\App\Http\Controllers\MasterController::class, 'storeRole'])->name('master.role.store');
    Route::put('/master/role/{id}', [\App\Http\Controllers\MasterController::class, 'updateRole'])->name('master.role.update');
    Route::delete('/master/role/{id}', [\App\Http\Controllers\MasterController::class, 'destroyRole'])->name('master.role.destroy');

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
    Route::get('/payroll/attendance/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.attendance.edit');
    Route::get('/payroll/attendance/date/{attendance_date}/edit', [PayrollController::class, 'editByDate'])->name('payroll.attendance.editByDate');
    Route::put('/payroll/attendance/date/{attendance_date}', [PayrollController::class, 'updateByDate'])->name('payroll.attendance.updateByDate');
    Route::post('/payroll/attendance', [PayrollController::class, 'storeAttendance'])->name('payroll.attendance.store');
    Route::get('/payroll/attendance/export', [PayrollController::class, 'exportAttendance'])->name('payroll.attendance.export');
    Route::post('payroll/attendance/import', [PayrollController::class, 'import'])->name('payroll.attendance.import');
    Route::get('/payroll/attendance/details', [PayrollController::class, 'getAttendanceDetails'])->name('payroll.attendance.details');
    Route::delete('/payroll/attendance/{id}', [PayrollController::class, 'destroyAttendance'])->name('payroll.attendance.destroy');
    Route::delete('/payroll/attendance/date/{date}', [PayrollController::class, 'destroyAttendanceByDate'])->name('payroll.attendance.destroyByDate');
    Route::post('/payroll/attendance/delete-bulk', [PayrollController::class, 'bulkDestroyAttendance'])->name('payroll.attendance.bulkDestroy');
    Route::get('/payroll/attendance/employee', [PayrollController::class, 'employeeWiseAttendace'])->name('payroll.attendace.employee');
    Route::get('/payroll/attendance/employee-wise-details', [PayrollController::class, 'employeeWiseDetails'])->name('payroll.attendance.employee.details');
    Route::get('/payroll/attendace/employee/{employee_id}/edit', [PayrollController::class, 'editByName'])->name('payroll.attendance.employee.editByName');
    Route::put('/payroll/attendance/employee/{employee_id}/update', [PayrollController::class, 'updateByName'])->name('payroll.attendance.employee.updateByName');

    // PAYROLL CALCULATION ROUTES
    Route::get('/payroll/calculation', [PayrollController::class, 'calculation'])->name('payroll.calculation');
    Route::post('/payroll/calculate', [PayrollController::class, 'calculatePayroll'])->name('payroll.calculate');
    Route::post('/payroll/sendDateRange', [PayrollController::class, 'calculateInRage'])->name('payroll.sendDateRange');
    Route::post('/payroll/store', [PayrollController::class, 'storePayroll'])->name('payroll.store');

    // PAYROLL LIST ROUTES
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/get', [PayrollController::class, 'getPayroll'])->name('payroll.get');
    Route::get('/payroll/export', [PayrollController::class, 'export'])->name('payroll.export');
    Route::get('/payroll/{id}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/{id}/status', [PayrollController::class, 'updateStatus'])->name('payroll.status');
    Route::delete('/payroll/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');

    // Payroll remarks
    Route::post('/payroll/{id}/remarks', [PayrollController::class, 'saveRemarks']);
    Route::post('/payroll/{id}/mark-read', [PayrollController::class, 'markAsRead']);
});

// LEAVE MODULE ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/leave/allotment', [LeaveController::class, 'allotment'])->name('leave.allotment');
    Route::post('/leave/allotment', [LeaveController::class, 'storeAllotment'])->name('leave.storeAllotment');
    Route::get('/leave/balance', [LeaveController::class, 'allotment'])->name('leave.balance');
    Route::get('/leave/balance/export', [LeaveController::class, 'exportBalances'])->name('leave.balance.export');
    Route::get('/api/leave/balance', [LeaveController::class, 'apiBalanceList']);

    // LEAVE APPLICATIONS
    Route::get('/leave/history', [LeaveApplicationController::class, 'index'])->name('leave.history');
    Route::get('/leave/export', [LeaveApplicationController::class, 'export'])->name('leave.export');
    Route::post('/leave/apply', [LeaveApplicationController::class, 'store'])->name('leave.apply');
    Route::post('/leave/action', [LeaveApplicationController::class, 'updateAction'])->name('leave.updateAction');
    Route::delete('/leave/application/{id}', [LeaveApplicationController::class, 'destroy'])->name('leave.application.destroy');
    Route::get('/api/leave/details/{id}', [LeaveApplicationController::class, 'getDetails']);
    Route::get('/api/leave/employee/{employeeId}', [LeaveApplicationController::class, 'getEmployeeLeaves']);

    // NOTIFICATIONS
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile/details', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/leave-balance', [ProfileController::class, 'leaveBalance'])->name('profile.leave-balance');
    Route::get('/profile/leave-history', [ProfileController::class, 'leaveHistory'])->name('profile.leave-history');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

require __DIR__ . '/auth.php';
