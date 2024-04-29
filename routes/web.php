<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LayerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SendbackController;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Middleware\EnsureUserHasRole;

Route::get('/', function () {
    return redirect('home');
});

Route::get('/home', function () {
    return view('pages.home');
})->middleware(['auth', 'verified'])->name('home');

Route::get('dbauth', [SsoController::class, 'dbauth']);

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
                
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                    ->name('password.request');
                    
    Route::get('reset-password-email', [PasswordResetLinkController::class, 'selfResetView']);
    
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                    ->name('password.email');
    
    
});


Route::middleware('auth')->group(function () {

    Route::get('reset-self', [PasswordResetLinkController::class, 'selfReset'])
                ->name('password.reset.self');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'task'])->name('tasks');

    // Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals');
    Route::get('/goals/detail/{id}', [GoalController::class, 'show'])->name('goals.detail');
    Route::get('/goals/form/{id}', [GoalController::class, 'create'])->name('goals.form');
    Route::post('/goals/submit', [GoalController::class, 'store'])->name('goals.submit');
    Route::get('/goals/edit/{id}', [GoalController::class, 'edit'])->name('goals.edit');
    Route::post('/goals/update', [GoalController::class, 'update'])->name('goals.update');
    
    // Approval
    Route::post('/approval/goal', [ApprovalController::class, 'store'])->name('approval.goal');

    // Sendback
    Route::post('/sendback/goal', [SendbackController::class, 'store'])->name('sendback.goal');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('export/employees', [ExportExcelController::class, 'export'])->name('export.employee');
    Route::get('/get-report-content/{reportType}', [ReportController::class, 'getReportContent']);
    Route::get('/export/report-emp', [ExportExcelController::class, 'exportreportemp'])->name('export.reportemp');
    
    // Authentication
    
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    Route::get('/get-tooltip-content', [GoalController::class, 'getTooltipContent']);
    Route::get('/units-of-measurement', [GoalController::class, 'unitOfMeasurement']);
    
});

Route::group(['middleware'=>'admin'],function(){
	//Employee
    Route::get('/employees', [EmployeeController::class, 'employee'])->name('employees');
    Route::get('/employee/filter', 'EmployeeController@filterEmployees')->name('employee.filter');

    // Schedule
    Route::get('/schedules', [ScheduleController::class, 'schedule'])->name('schedules');
    Route::get('/schedules/form', [ScheduleController::class, 'form'])->name('schedules-form');
    Route::post('/save-schedule', [ScheduleController::class, 'save'])->name('save-schedule');
    Route::get('/edit-schedule/{id}', [ScheduleController::class, 'edit'])->name('edit-schedule');
    Route::post('/update-schedule', [ScheduleController::class, 'update'])->name('update-schedule');
    Route::delete('/schedule/{id}', [ScheduleController::class, 'softDelete'])->name('soft-delete-schedule');

    // Assignments
    Route::get('/assignments', [AssignmentController::class, 'assignment'])->name('assignments');

    // Roles
    Route::get('/roles', [RoleController::class, 'role'])->name('roles');
});

Route::fallback(function () {
    return view('errors.404');
});

require __DIR__.'/auth.php';
